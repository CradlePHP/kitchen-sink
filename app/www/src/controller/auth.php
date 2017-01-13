<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Utility\File;

/**
 * Process the Verification Page
 *
 * SIGNUP FLOW:
 * - GET /signup
 * - POST /signup
 * - EMAIL
 * - GET /activate/auth_id/hash
 * - GET /login
 *
 * VERIFY FLOW:
 * - GET /verify
 * - POST /verify
 * - EMAIL
 * - GET /activate/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/activate/:auth_id/:hash', function ($request, $response) {
    //get the detail
    cradle()->trigger('auth-detail', $request, $response);

    //form hash
    $authId = $response->getResults('auth_id');
    $authUpdated = $response->getResults('auth_updated');
    $hash = md5($authId.$authUpdated);

    //check the verification hash
    if ($hash !== $request->getStage('hash')) {
        cradle('global')->flash('Invalid verification. Try again.', 'danger');
        return cradle('global')->redirect('/verify');
    }

    //activate
    $request->setStage('auth_active', 1);

    //trigger the job
    cradle()->trigger('auth-update', $request, $response);

    if ($response->isError()) {
        cradle('global')->flash('Invalid verification. Try again.', 'danger');
        return cradle('global')->redirect('/verify');
    }

    //it was good
    //add a flash
    cradle('global')->flash('Activation Successful', 'success');

    //redirect
    cradle('global')->redirect('/login');
});

/**
 * Render the Signup Page
 *
 * SIGNUP FLOW:
 * - GET /signup
 * - POST /signup
 * - EMAIL
 * - GET /activate/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/signup', function ($request, $response) {
    //Prepare body
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    //add captcha
    cradle()->trigger('captcha-load', $request, $response);
    $data['captcha'] = $response->getResults('captcha');

    if ($response->isError()) {
        if ($response->getValidation('auth_slug')) {
            $message = $response->getValidation('auth_slug');
            $response->addValidation('profile_email', $message);
        }

        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-auth-signup';
    $title = cradle('global')->translate('Sign Up');
    $body = cradle('/app/www')->template('signup', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render blank page
}, 'render-www-blank');

/**
 * Render the Login Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/login', function ($request, $response) {
    //Prepare body
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');


    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');

        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-auth-login';
    $title = cradle('global')->translate('Log In');
    $body = cradle('/app/www')->template('login', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render blank page
}, 'render-www-blank');

/**
 * Process the Logout
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/logout', function ($request, $response) {
    //TODO: Sessions for clusters
    unset($_SESSION['me']);

    //add a flash
    cradle('global')->flash('Log Out Successful', 'success');

    //redirect
    $redirect = '/';
    if ($request->hasGet('redirect_uri')) {
        $redirect = $request->getGet('redirect_uri');
    }

    cradle('global')->redirect($redirect);
});

/**
 * Render the Account Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/account', function ($request, $response) {
    //Need to be logged in
    cradle('global')->requireLogin();

    //Prepare body
    $data = ['item' => $request->getPost()];

    //add CDN
    $config = $this->package('global')->service('s3-main');
    $data['cdn_config'] = File::getS3Client($config);

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    //If no post
    if (!$request->hasPost('profile_name')) {
        //set default data
        $data['item'] = $request->getSession('me');
    }

    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-auth-account';
    $title = cradle('global')->translate('Account Settings');
    $body = cradle('/app/www')->template('account', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render blank page
}, 'render-www-blank');

/**
 * Render the Forgot Page
 *
 * FORGOT FLOW:
 * - GET /forgot
 * - POST /forgot
 * - EMAIL
 * - GET /recover/auth_id/hash
 * - POST /recover/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/forgot', function ($request, $response) {
    //Prepare body
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-auth-forgot';
    $title = cradle('global')->translate('Forgot Password');
    $body = cradle('/app/www')->template('forgot', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render blank page
}, 'render-www-blank');

/**
 * Render the Recover Page
 *
 * FORGOT FLOW:
 * - GET /forgot
 * - POST /forgot
 * - EMAIL
 * - GET /recover/auth_id/hash
 * - POST /recover/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/recover/:auth_id/:hash', function ($request, $response) {
    //get the detail
    cradle()->trigger('auth-detail', $request, $response);

    //form hash
    $authId = $response->getResults('auth_id');
    $authUpdated = $response->getResults('auth_updated');
    $hash = md5($authId.$authUpdated);

    //check the verification hash
    if ($hash !== $request->getStage('hash')) {
        cradle('global')->flash('Invalid verification. Try again.', 'danger');
        return cradle('global')->redirect('/verify');
    }

    //Prepare body
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-auth-recover';
    $title = cradle('global')->translate('Recover Password');
    $body = cradle('/app/www')->template('recover', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render blank page
}, 'render-www-blank');

/**
 * Render the Verify Page
 *
 * VERIFY FLOW:
 * - GET /verify
 * - POST /verify
 * - EMAIL
 * - GET /activate/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/verify', function ($request, $response) {
    //Prepare body
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-auth-verify';
    $title = cradle('global')->translate('Verify Account');
    $body = cradle('/app/www')->template('verify', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render blank page
}, 'render-www-blank');

/**
 * Process the Account Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/account', function ($request, $response) {
    //need to be online
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/account', $request, $response);
    }

    //set the auth_id and profile_id
    $request->setStage('auth_id', $request->getSession('me', 'auth_id'));
    $request->setStage('profile_id', $request->getSession('me', 'profile_id'));
    $request->setStage('permission', $request->getSession('me', 'profile_id'));

    //remove password if empty
    if (!$request->getStage('auth_password')) {
        $request->removeStage('auth_password');
    }

    if (!$request->getStage('confirm')) {
        $request->removeStage('confirm');
    }

    //trigger the job
    cradle()->trigger('auth-update', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/account', $request, $response);
    }

    //it was good
    //update the session
    cradle()->trigger('auth-detail', $request, $response);
    $_SESSION['me'] = $response->getResults();

    //add a flash
    cradle('global')->flash('Update Successful', 'success');

    //redirect
    $redirect = '/';
    if ($request->hasGet('redirect_uri')) {
        $redirect = $request->getGet('redirect_uri');
    }

    cradle('global')->redirect($redirect);
});

/**
 * Process the Login Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/login', function ($request, $response) {
    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/login', $request, $response);
    }

    //call the job
    cradle()->trigger('auth-login', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/login', $request, $response);
        ;
    }

    //it was good

    //store to session
    //TODO: Sessions for clusters
    $_SESSION['me'] = $response->getResults();

    //redirect
    if ($request->hasGet('redirect')) {
        return cradle('global')->redirect($request->getGet('redirect'));
    }

    $redirect = '/';
    if ($request->hasGet('redirect_uri')) {
        $redirect = $request->getGet('redirect_uri');
    }

    return cradle('global')->redirect($redirect);
});

/**
 * Process the Forgot Page
 *
 * FORGOT FLOW:
 * - GET /forgot
 * - POST /forgot
 * - EMAIL
 * - GET /recover/auth_id/hash
 * - POST /recover/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/forgot', function ($request, $response) {
    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/forgot', $request, $response);
    }

    //trigger the job
    cradle()->trigger('auth-forgot', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/forgot', $request, $response);
    }

    //its good
    $response->setFlash('An email with recovery instructions will be sent in a few minutes.', 'success');
    cradle()->triggerRoute('get', '/forgot', $request, $response);
});

/**
 * Process the Recover Page
 *
 * FORGOT FLOW:
 * - GET /forgot
 * - POST /forgot
 * - EMAIL
 * - GET /recover/auth_id/hash
 * - POST /recover/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/recover/:auth_id/:hash', function ($request, $response) {
    //get the detail
    cradle()->trigger('auth-detail', $request, $response);

    //form hash
    $authId = $response->getResults('auth_id');
    $authUpdated = $response->getResults('auth_updated');
    $hash = md5($authId.$authUpdated);

    //check the recovery hash
    if ($hash !== $request->getStage('hash')) {
        cradle('global')->flash('This recovery page is expired. Please try again.', 'danger');
        return cradle('global')->redirect('/forgot');
    }

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        $redirect = '/recover/' . $authId . '/' . $hash;
        return cradle()->triggerRoute('get', $redirect, $request, $response);
    }

    //trigger the job
    cradle()->trigger('auth-recover', $request, $response);

    if ($response->isError()) {
        $redirect = '/recover/' . $authId . '/' . $hash;
        return cradle()->triggerRoute('get', $redirect, $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('Recovery Successful', 'success');

    //redirect
    cradle('global')->redirect('/login');
});

/**
 * Process the Signup Page
 *
 * SIGNUP FLOW:
 * - GET /signup
 * - POST /signup
 * - EMAIL
 * - GET /activate/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/signup', function ($request, $response) {
    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/signup', $request, $response);
    }

    //captcha check
    cradle()->trigger('captcha-validate', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/signup', $request, $response);
    }

    //set defaults
    if (!$request->hasStage('auth_permissions')) {
        $request->setStage('auth_permissions', [
            'public_profile',
            'personal_profile'
        ]);
    }

    //trigger the job
    cradle()->trigger('auth-create', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/signup', $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('Sign Up Successful. Please check your email for verification process.', 'success');

    //redirect
    $query = http_build_query($request->get('get'));
    cradle('global')->redirect('/login?' . $query);
});

/**
 * Process the Verify Page
 *
 * VERIFY FLOW:
 * - GET /verify
 * - POST /verify
 * - EMAIL
 * - GET /activate/auth_id/hash
 * - GET /login
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/verify', function ($request, $response) {
    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/verify', $request, $response);
    }

    //trigger the job
    cradle()->trigger('auth-verify', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/verify', $request, $response);
    }

    //its good
    $response->setFlash('An email with verification instructions will be sent in a few minutes.', 'success');
    cradle()->triggerRoute('get', '/verify', $request, $response);
});
