<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\Auth\Service as AuthService;
use Cradle\Module\Oauth\Auth\Validator as AuthValidator;

use Cradle\Module\Profile\Validator as ProfileValidator;

/**
 * Auth Create Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-create', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
        $data['auth_slug'] = $data['profile_email'];
    }

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //validate
    $errors = AuthValidator::getCreateErrors($data);
    $errors = ProfileValidator::getCreateErrors($data, $errors);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //salt on password
    $data['auth_password'] = md5($data['auth_password']);

    //deflate permissions
    $data['auth_permissions'] = json_encode($data['auth_permissions']);

    //deactive account
    $data['auth_active'] = 0;

    //save item to database
    $results = $authSql->create($data);

    //also create profile
    $this->trigger('profile-create', $request, $response);

    $results = array_merge($results, $response->getResults());

    //link item to profile
    $authSql->linkProfile($results['auth_id'], $results['profile_id']);

    //index item
    $authElastic->create($results['auth_id']);

    //invalidate cache
    $authRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);

    //send mail
    $request->setSoftStage($response->getResults());

    //because there's no way the CLI queue would know the host
    $protocol = 'http';
    if ($request->getServer('SERVER_PORT') === 443) {
        $protocol = 'https';
    }

    $request->setStage('host', $protocol . '://' . $request->getServer('HTTP_HOST'));
    $data = $request->getStage();

    //try to queue, and if not
    if (!$this->package('global')->queue('auth-verify-mail', $data)) {
        //send mail manually
        $this->trigger('auth-verify-mail', $request, $response);
    }
});

/**
 * Auth Detail Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-detail', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    $id = null;
    if (isset($data['auth_id'])) {
        $id = $data['auth_id'];
    } else if (isset($data['auth_slug'])) {
        $id = $data['auth_slug'];
    }

    //we need an id
    if (!$id) {
        return $response->setError(true, 'Invalid ID');
    }

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    $results = null;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = $authRedis->getDetail($id);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = $authElastic->get($id);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = $authSql->get($id);
        }

        if ($results) {
            //cache it from database or index
            $authRedis->createDetail($id, $results);
        }
    }

    if (!$results) {
        return $response->setError(true, 'Not Found');
    }

    //if permission is provided
    $permission = $request->getStage('permission');
    if ($permission && $results['profile_id'] != $permission) {
        return $response->setError(true, 'Invalid Permissions');
    }

    //set response format
    $response->setError(false)->setResults($results);
});

/**
 * Auth Forgot Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-forgot', function ($request, $response) {
    //get the auth detail
    $this->trigger('auth-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //validate
    $errors = AuthValidator::getForgotErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //send mail
    $request->setSoftStage($response->getResults());

    //because there's no way the CLI queue would know the host
    $protocol = 'http';
    if ($request->getServer('SERVER_PORT') === 443) {
        $protocol = 'https';
    }

    $request->setStage('host', $protocol . '://' . $request->getServer('HTTP_HOST'));
    $data = $request->getStage();

    //try to queue, and if not
    if (!$this->package('global')->queue('auth-forgot-mail', $data)) {
        //send mail manually
        $this->trigger('auth-forgot-mail', $request, $response);
    }

    //return response format
    $response->setError(false);
});

/**
 * Auth Forgot Mail Job (supporting job)
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-forgot-mail', function ($request, $response) {
    $config = $this->package('global')->service('mail-main');

    if (!$config) {
        return;
    }

    //if it's not configured
    if($config['user'] === '<EMAIL ADDRESS>'
        || $config['pass'] === '<EMAIL PASSWORD>'
    )
    {
        return;
    }

    //form hash
    $authId = $request->getStage('auth_id');
    $authUpdated = $request->getStage('auth_updated');
    $hash = md5($authId.$authUpdated);

    //form link
    $host = $request->getStage('host');
    $link = $host . '/recover/' . $authId . '/' . $hash;

    //prepare data
    $from = [];
    $from[$config['user']] = $config['name'];

    $to = [];
    $to[$request->getStage('auth_slug')] = null;

    $subject = $this->package('global')->translate('Password Recovery from Salaaap!');
    $text = $this->package('/app/core')->template('email/recover.txt', ['link' => $link]);
    $html = $this->package('/app/core')->template('email/recover.html', [
        'host' => $host,
        'link' => $link
    ]);

    //send mail
    $message = new Swift_Message($subject);
    $message->setFrom($from);
    $message->setTo($to);
    $message->setBody($html, 'text/html');
    $message->addPart($text, 'text/plain');

    $transport = Swift_SmtpTransport::newInstance();
    $transport->setHost($config['host']);
    $transport->setPort($config['port']);
    $transport->setEncryption($config['type']);
    $transport->setUsername($config['user']);
    $transport->setPassword($config['pass']);

    $swift = Swift_Mailer::newInstance($transport);
    $swift->send($message, $failures);
});

/**
 * Auth Login Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-login', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //validate
    $errors = AuthValidator::getLoginErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //load up the detail
    $this->trigger('auth-detail', $request, $response);
});

/**
 * Auth Recover Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-recover', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //validate
    $errors = AuthValidator::getRecoverErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //update
    $this->trigger('auth-update', $request, $response);

    //return response format
    $response->setError(false);
});

/**
 * Auth Refresh Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-refresh', function ($request, $response) {
    //get the auth detail
    $this->trigger('auth-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //save item to database
    $results = $authSql->update([
        'auth_id' => $data['auth_id'],
        'auth_token' => md5(uniqid()),
        'auth_secret' => md5(uniqid())
    ]);

    //index item
    $authElastic->update($data['auth_id']);

    //invalidate cache
    $authRedis->removeDetail($data['auth_id']);
    $authRedis->removeDetail($data['auth_slug']);
    $authRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
 * Auth Remove Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-remove', function ($request, $response) {
    //get the auth detail
    $this->trigger('auth-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //save item to database
    $results = $authSql->update([
        'auth_id' => $data['auth_id'],
        'auth_active' => 0
    ]);

    //remove from index
    $authElastic->remove($data['auth_id']);

    //invalidate cache
    $authRedis->removeDetail($data['auth_id']);
    $authRedis->removeDetail($data['auth_slug']);
    $authRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
 * Auth Restore Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-restore', function ($request, $response) {
    //get the auth detail
    $this->trigger('auth-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //save item to database
    $results = $authSql->update([
        'auth_id' => $data['auth_id'],
        'auth_active' => 1
    ]);

    //remove from index
    $authElastic->create($data['auth_id']);

    //invalidate cache
    $authRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
 * Auth Search Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-search', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    $results = false;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = $authRedis->getSearch($data);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = $authElastic->search($data);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = $authSql->search($data);
        }

        if ($results) {
            //cache it from database or index
            $authRedis->createSearch($data, $results);
        }
    }

    //set response format
    $response->setError(false)->setResults($results);
});

/**
 * Auth Update Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-update', function ($request, $response) {
    //get the auth detail
    $this->trigger('auth-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //validate
    $errors = AuthValidator::getUpdateErrors($data);

    //check for profile errors if profile is being updated
    if (isset($data['profile_id'])) {
        $errors = ProfileValidator::getUpdateErrors($data, $errors);
    }

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    if (isset($data['auth_password'])) {
        //salt on password
        $data['auth_password'] = md5($data['auth_password']);
    }

    //deflate permissions
    if (isset($data['auth_permissions'])) {
        $data['auth_permissions'] = json_encode($data['app_permissions']);
    }

    //save item to database
    $results = $authSql->update($data);

    //index item
    $authElastic->update($response->getResults('auth_id'));

    //invalidate cache
    $authRedis->removeDetail($response->getResults('auth_id'));
    $authRedis->removeDetail($response->getResults('auth_slug'));
    $authRedis->removeSearch();

    //if profile id
    if (isset($data['profile_id'])) {
        //also update profile
        $this->trigger('profile-update', $request, $response);
        $results = array_merge($results, $response->getResults());
    }

    //return response format
    $response->setError(false)->setResults($results);
});

/**
 * Auth Verify Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-verify', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $authSql = AuthService::get('sql');
    $authRedis = AuthService::get('redis');
    $authElastic = AuthService::get('elastic');

    //validate
    $errors = AuthValidator::getVerifyErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //get the auth detail
    $this->trigger('auth-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //send mail
    $request->setSoftStage($response->getResults());

    //because there's no way the CLI queue would know the host
    $protocol = 'http';
    if ($request->getServer('SERVER_PORT') === 443) {
        $protocol = 'https';
    }

    $request->setStage('host', $protocol . '://' . $request->getServer('HTTP_HOST'));
    $data = $request->getStage();

    //try to queue, and if not
    if (!$this->package('global')->queue('auth-verify-mail', $data)) {
        //send mail manually
        $this->trigger('auth-verify-mail', $request, $response);
    }

    //return response format
    $response->setError(false);
});

/**
 * Auth Verify Mail Job (supporting job)
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('auth-verify-mail', function ($request, $response) {
    $config = $this->package('global')->service('mail-main');

    if (!$config) {
        return;
    }

    //if it's not configured
    if($config['user'] === '<EMAIL ADDRESS>'
        || $config['pass'] === '<EMAIL PASSWORD>'
    )
    {
        return;
    }

    //form hash
    $authId = $request->getStage('auth_id');
    $authUpdated = $request->getStage('auth_updated');
    $hash = md5($authId.$authUpdated);

    //form link
    $host = $request->getStage('host');
    $link = $host . '/activate/' . $authId . '/' . $hash;

    //prepare data
    $from = [];
    $from[$config['user']] = $config['name'];

    $to = [];
    $to[$request->getStage('auth_slug')] = null;

    $subject = $this->package('global')->translate('Account Verification from Salaaap!');
    $text = $this->package('/app/core')->template('email/verify.txt', ['link' => $link]);
    $html = $this->package('/app/core')->template('email/verify.html', [
        'host' => $host,
        'link' => $link
    ]);

    //send mail
    $message = new Swift_Message($subject);
    $message->setFrom($from);
    $message->setTo($to);
    $message->setBody($html, 'text/html');
    $message->addPart($text, 'text/plain');

    $transport = Swift_SmtpTransport::newInstance();
    $transport->setHost($config['host']);
    $transport->setPort($config['port']);
    $transport->setEncryption($config['type']);
    $transport->setUsername($config['user']);
    $transport->setPassword($config['pass']);

    $swift = Swift_Mailer::newInstance($transport);
    $swift->send($message, $failures);
});
