<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\Session\Service as SessionService;
use Cradle\Module\Oauth\Session\Validator as SessionValidator;

/**
 * Session Access Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('session-access', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $this->trigger('session-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    $errors = SessionValidator::getAccessErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //----------------------------//
    // 3. Prepare Data
    //no preparation needed
    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $sessionSql = SessionService::get('sql');
    $sessionRedis = SessionService::get('redis');
    $sessionElastic = SessionService::get('elastic');

    //save to database
    $results = $sessionSql->update([
        'session_id' => $response->getResults('session_id'),
        'session_status' => 'ACCESS',
        'session_token' => md5(uniqid()),
        'session_secret' => md5(uniqid())
    ]);

    //index app
    $sessionElastic->update($response->getResults('session_id'));

    //invalidate cache
    $sessionRedis->removeDetail($response->getResults('session_id'));
    $sessionRedis->removeDetail($response->getResults('session_token'));
    $sessionRedis->removeSearch();

    //return response format
    $response->setError(false)->setResults([
        'access_token' => $results['session_token'],
        'access_secret' => $results['session_secret'],
        'profile_id' => $response->getResults('profile_id'),
        'profile_name' => $response->getResults('profile_name'),
        'profile_email' => $response->getResults('profile_email'),
        'profile_image' => $response->getResults('profile_image')
    ]);
});

/**
 * Session Create Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('session-create', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    $errors = SessionValidator::getCreateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //----------------------------//
    // 3. Prepare Data
    //deflate permissions
    $data['session_permissions'] = json_encode($data['session_permissions']);

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $sessionSql = SessionService::get('sql');
    $sessionRedis = SessionService::get('redis');
    $sessionElastic = SessionService::get('elastic');

    //save to database
    $results = $sessionSql->create($data);

    //link auth
    $sessionSql->linkAuth($results['session_id'], $results['auth_id']);

    //link app
    $sessionSql->linkApp($results['session_id'], $results['app_id']);

    //index
    $sessionElastic->create($results['session_id']);

    //invalidate cache
    $sessionRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* Session Detail Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-detail', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    $id = null;
    if (isset($data['session_id'])) {
        $id = $data['session_id'];
    } else if (isset($data['session_token'])) {
        $id = $data['session_token'];
    }

    //----------------------------//
    // 2. Validate Data
    //we need a app id
    if (!$id) {
        return $response->setError(true, 'Invalid ID');
    }

    //----------------------------//
    // 3. Prepare Data
    //no preparation needed
    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $sessionSql = SessionService::get('sql');
    $sessionRedis = SessionService::get('redis');
    $sessionElastic = SessionService::get('elastic');

    $results = null;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = $sessionRedis->getDetail($id);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = $sessionElastic->get($id);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = $sessionSql->get($id);
        }

        if ($results) {
            //cache it from database or index
            $sessionRedis->createDetail($id, $results);
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
* Session Refresh Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-refresh', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $this->trigger('session-detail', $request, $response);

    //----------------------------//
    // 2. Validate Data
    if ($response->isError()) {
        return;
    }

    //----------------------------//
    // 3. Prepare Data
    $data = $response->getResults();

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $sessionSql = SessionService::get('sql');
    $sessionRedis = SessionService::get('redis');
    $sessionElastic = SessionService::get('elastic');

    //save to database
    $results = $sessionSql->update([
        'session_id' => $data['session_id'],
        'session_token' => md5(uniqid()),
        'session_secret' => md5(uniqid())
    ]);

    //index item
    $sessionElastic->update($data['session_id']);

    //invalidate cache
    $sessionRedis->removeDetail($data['session_id']);
    $sessionRedis->removeDetail($data['session_token']);
    $sessionRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* Session Remove Job (Hard Remove)
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-remove', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $this->trigger('session-detail', $request, $response);

    //----------------------------//
    // 2. Validate Data
    if ($response->isError()) {
        return;
    }

    //----------------------------//
    // 3. Prepare Data
    $data = $response->getResults();

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $sessionSql = SessionService::get('sql');
    $sessionRedis = SessionService::get('redis');
    $sessionElastic = SessionService::get('elastic');

    //remove from database
    $sessionSql->remove($data['session_id']);

    //link auth
    $sessionSql->unlinkAuth($data['session_id'], $data['auth_id']);

    //link app
    $sessionSql->unlinkApp($data['session_id'], $data['app_id']);

    //remove from index
    $sessionElastic->remove($data['session_id']);

    //invalidate cache
    $sessionRedis->removeDetail($data['session_id']);
    $sessionRedis->removeDetail($data['session_token']);
    $sessionRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($data);
});

/**
* Session Search Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-search', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    //no validation needed
    //----------------------------//
    // 3. Prepare Data
    //no preparation needed
    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $sessionSql = SessionService::get('sql');
    $sessionRedis = SessionService::get('redis');
    $sessionElastic = SessionService::get('elastic');

    $results = false;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = $sessionRedis->getSearch($data);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = $sessionElastic->search($data);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = $sessionSql->search($data);
        }

        if ($results) {
            //cache it from database or index
            $sessionRedis->createSearch($data, $results);
        }
    }

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* Session Update Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-update', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $this->trigger('session-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    $errors = SessionValidator::getUpdateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //----------------------------//
    // 3. Prepare Data
    //deflate permissions
    if (isset($data['session_permissions'])) {
        $data['session_permissions'] = json_encode($data['session_permissions']);
    }

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $sessionSql = SessionService::get('sql');
    $sessionRedis = SessionService::get('redis');
    $sessionElastic = SessionService::get('elastic');

    //save app to database
    $results = $sessionSql->update($data);

    //index
    $sessionElastic->update($response->getResults('session_id'));

    //invalidate cache
    $sessionRedis->removeDetail($response->getResults('session_id'));
    $sessionRedis->removeDetail($response->getResults('session_token'));
    $sessionRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});
