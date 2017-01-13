<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Render the App Search Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/developer/app/search', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //for logged in
    cradle('global')->requireLogin();

    //----------------------------//
    // 2. Prepare Data
    $profile = $request->getSession('me', 'profile_id');
    $request->setStage('filter', 'profile_id', $profile);
    cradle()->trigger('app-search', $request, $response);
    $data = array_merge($request->getStage(), $response->getResults());

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    //----------------------------//
    // 3. Render Template
    $class = 'page-developer-app-search branding';
    $title = cradle('global')->translate('Apps');
    $body = cradle('/app/api')->template('developer/app/search', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render page
}, 'render-developer-page');

/**
 * Render the App Create Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/developer/app/create', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //for logged in
    cradle('global')->requireLogin();

    //----------------------------//
    // 2. Prepare Data
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //----------------------------//
    // 3. Render Template
    $class = 'page-developer-app-create branding';
    $data['title'] = cradle('global')->translate('Create an App');
    $body = cradle('/app/api')->template('developer/app/form', $data);

    //Set Content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);

    //Render page
}, 'render-developer-page');

/**
 * Render the App Update Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/developer/app/update/:app_id', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //for logged in
    cradle('global')->requireLogin();

    //----------------------------//
    // 2. Prepare Data
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    //if no item
    if (empty($data['item'])) {
        //get the detail with permission
        $permission = $request->getSession('me', 'profile_id');
        $request->setStage('permission', $permission);
        cradle()->trigger('app-detail', $request, $response);

        //can we update ?
        if ($response->isError()) {
            //add a flash
            cradle('global')->flash($response->getMessage(), 'danger');
            return cradle('global')->redirect('/developer/app/search');
        }

        $data['item'] = $response->getResults();
    }

    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //----------------------------//
    // 3. Render Template
    $class = 'page-developer-app-update branding';
    $data['title'] = cradle('global')->translate('Updating App');
    $body = cradle('/app/api')->template('developer/app/form', $data);

    //set Content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);

    //render page
}, 'render-developer-page');

/**
 * Process the App Create Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/developer/app/create', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //for logged in
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/developer/app/create', $request, $response);
    }

    //----------------------------//
    // 2. Prepare Data
    //add profile id
    $profile = $request->getSession('me', 'profile_id');
    $request->setStage('profile_id', $profile);

    //flatten permissions
    $permissions = $request->getStage('app_permissions');

    if (!$permissions) {
        $request->setStage('app_permissions', []);
    }

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('app-create', $request, $response);

    //----------------------------//
    // 4. Interpret Results
    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/developer/app/create', $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('App was Created', 'success');

    //redirect
    cradle('global')->redirect('/developer/app/search');
});

/**
 * Process the App Update Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/developer/app/update/:app_id', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //for logged in
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        $route = '/developer/app/update/' . $request->getStage('app_id');
        return cradle()->triggerRoute('get', $route, $request, $response);
    }

    //----------------------------//
    // 2. Prepare Data
    //set permission
    $permission = $request->getSession('me', 'profile_id');
    $request->setStage('permission', $permission);

    //flatten permissions
    $permissions = $request->getStage('app_permissions');

    if (!$permissions) {
        $request->setStage('app_permissions', []);
    }

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('app-update', $request, $response);

    //----------------------------//
    // 4. Interpret Results
    if ($response->isError()) {
        $route = '/developer/app/update/' . $request->getStage('app_id');
        return cradle()->triggerRoute('get', $route, $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('App was Updated', 'success');

    //redirect
    cradle('global')->redirect('/developer/app/search');
});

/**
 * Process the App Remove
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/developer/app/remove/:app_id', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //for logged in
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        cradle('global')->flash($response->getMessage(), 'danger');
        return cradle('global')->redirect('/developer/app/search');
    }

    //----------------------------//
    // 2. Prepare Data
    //set permission
    $permission = $request->getSession('me', 'profile_id');
    $request->setStage('permission', $permission);

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('app-remove', $request, $response);

    //----------------------------//
    // 4. Interpret Results
    if ($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('App was Removed');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('/developer/app/search');
});

/**
 * Process the App Refresh
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/developer/app/refresh/:app_id', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //for logged in
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if ($response->isError()) {
        cradle('global')->flash($response->getMessage(), 'danger');
        return cradle('global')->redirect('/developer/app/search');
    }

    //----------------------------//
    // 2. Prepare Data`
    //set permission
    $permission = $request->getSession('me', 'profile_id');
    $request->setStage('permission', $permission);

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('app-refresh', $request, $response);

    //----------------------------//
    // 4. Interpret Results
    if ($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('App was Refreshed');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('/developer/app/search');
});
