<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

 /**
  * Render the Signup Page
  *
  * @param Request $request
  * @param Response $response
  */
 $cradle->get('/dialog/signup', function ($request, $response) {
     //redirect
     $query = http_build_query($request->get('get'));
     $redirect = urlencode('/dialog/request?' . $query);
     cradle('global')->redirect('/signup?redirect_uri='.$redirect);
 });

 /**
  * Render the Login Page
  *
  * @param Request $request
  * @param Response $response
  */
    $cradle->get('/dialog/login', function ($request, $response) {
        //redirect
        $query = http_build_query($request->get('get'));
        $redirect = urlencode('/dialog/request?' . $query);
          cradle('global')->redirect('/login?redirect_uri='.$redirect);
    });

 /**
  * Render the Account Page
  *
  * @param Request $request
  * @param Response $response
  */
    $cradle->get('/dialog/account', function ($request, $response) {
        //redirect
        $query = http_build_query($request->get('get'));
        cradle('global')->redirect('/account?'.$query);
    });

/**
 * Render the Request Page
 *
 * @param Request $request
 * @param Response $response
 */
    $cradle->get('/dialog/request', function ($request, $response) {
        //----------------------------//
        // 1. Route Permissions
        //for logged in
        cradle('global')->requireLogin();

        //validate parameters
        if (!$request->hasStage('client_id') || !$request->hasStage('redirect_uri')) {
            return cradle()->triggerRoute('get', '/dialog/invalid', $request, $response);
        }

        //----------------------------//
        // 2. Prepare Data`
        //get app detail
        $token = $request->getStage('client_id');
        $request->setStage('app_token', $token);
        cradle()->trigger('app-detail', $request, $response);

        $app = $response->getResults();
        $permitted = $app['app_permissions'];

        $requested = [];
        if ($request->hasStage('scope')) {
            $requested = explode(',', $request->getStage('scope'));
        }

        //possible request types
        $types = [
        //public
        'public_profile' => null,
        'public_product' => null,

        //personal
        'personal_profile' => null,
        'personal_comment' => null,
        'personal_review' => null,
        'personal_product' => null,

        //user
        'user_profile' => [
            'title' => 'User Profile',
            'description' => 'Access another user profile',
            'icon' => 'user'
        ],
        'user_comment' => [
            'title' => 'User Comments',
            'description' => 'Access to another user published comments',
            'icon' => 'comment'
        ],
        'user_review' => [
            'title' => 'User Reviews',
            'description' => 'Access to another user published reviews',
            'icon' => 'comment'
        ],
        'user_product' => [
            'title' => 'User Products',
            'description' => 'Access to another user published products',
            'icon' => 'tag'
        ]
        ];

        //the final permission set
        $permissions = ['public_profile' => null, 'public_product' => null];

        foreach ($requested as $permission) {
            //if they dont have permissions to ask
            if (!in_array($permission, $permitted)) {
                return cradle()->triggerRoute('get', '/dialog/invalid', $request, $response);
            }

            //if it's not a real permission
            if (!isset($types[$permission])) {
                continue;
            }

            $permissions[$permission] = $types[$permission];
        }

        //set data
        $data = [
        'permissions' => $permissions,
        'app' => $app
        ];

        //add CSRF
        cradle()->trigger('csrf-load', $request, $response);
        $data['csrf'] = $response->getResults('csrf');

        if ($response->isError()) {
            $response->setFlash($response->getMessage(), 'danger');
            $data['errors'] = $response->getValidation();
        }

        //----------------------------//
        // 3. Render Template
        $class = 'page-dialog-request';
        $title = cradle('global')->translate('Request Access');
        $body = cradle('/app/api')->template('dialog/request', $data);

        //Set Content
        $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

        //Render page
    }, 'render-dialog-page');

/**
 * Process the Request Page
 *
 * @param Request $request
 * @param Response $response
 */
    $cradle->post('/dialog/request', function ($request, $response) {
        //----------------------------//
        // 1. Route Permissions
        //for logged in
        cradle('global')->requireLogin();

        //csrf check
        cradle()->trigger('csrf-validate', $request, $response);

        if ($response->isError()) {
            return cradle()->triggerRoute('get', '/dialog/invalid', $request, $response);
        }

        //validate parameters
        if (!$request->hasStage('client_id') || !$request->hasStage('redirect_uri')) {
            return cradle()->triggerRoute('get', '/dialog/invalid', $request, $response);
        }

        if ($request->getStage('action') !== 'allow') {
            //redirect
            $url = $request->getStage('redirect_uri');
            cradle()->getDispatcher()->redirect($url . '?error=deny');
        }

        //----------------------------//
        // 2. Prepare Data
        //get auth id and app id
        $auth = $request->getSession('me', 'auth_id');
        $token = $request->getStage('client_id');
        $request->setStage('app_token', $token);
        cradle()->trigger('app-detail', $request, $response);
        $app = $response->getResults('app_id');

        $request->setStage('auth_id', $auth);
        $request->setStage('app_id', $app);

        //flatten permissions
        $permissions = $request->getStage('session_permissions');

        if (!$permissions) {
            $request->setStage('session_permissions', []);
        }

        //----------------------------//
        // 3. Process Request
        cradle()->trigger('session-create', $request, $response);

        //----------------------------//
        // 4. Interpret Results
        if ($response->isError()) {
            return cradle()->triggerRoute('get', '/dialog/invalid', $request, $response);
        }

        //it was good

        //redirect
        $url = $request->getStage('redirect_uri');
        $code = $response->getResults('session_token');
        cradle()->getDispatcher()->redirect($url . '?code=' . $code);
    });

/**
 * Process the Logout
 *
 * @param Request $request
 * @param Response $response
 */
    $cradle->get('/dialog/logout', function ($request, $response) {
         //redirect
         //TODO: Find better way
         $query = http_build_query($request->get('get'));
         cradle('global')->redirect('/logout?'.$query);
    });

/**
 * Render the Invalid Page
 *
 * @param Request $request
 * @param Response $response
 */
    $cradle->get('/dialog/invalid', function ($request, $response) {
        //----------------------------//
        // 1. Route Permissions
        //not needed
        //----------------------------//
        // 2. Prepare Data
        //prepare data
        $data = [];
        if ($response->hasJson()) {
            $data = $response->getJson();
        }

        //----------------------------//
        // 3. Render Template
        $class = 'page-dialog-invalid';
        $title = cradle('global')->translate('Invalid Request');
        $body = cradle('/app/api')->template('dialog/invalid', $data);

        //set Content
        $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

        //render page
    }, 'render-dialog-page');
