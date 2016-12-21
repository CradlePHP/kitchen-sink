<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
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
$cradle->get('/developer/app/search', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin();

    //Prepare body
    $profile = $request->getSession('me', 'profile_id');
    $request->setStage('filter', 'profile_id', $profile);
    cradle()->trigger('app-search', $request, $response);
    $data = array_merge($request->getStage(), $response->getResults());

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    //Render body
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
$cradle->get('/developer/app/create', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin();

    //Prepare body
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    if($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-developer-app-create branding';
    $title = cradle('global')->translate('Create an App');
    $body = cradle('/app/api')->template('developer/app/form', $data);

    //Set Content
    $response
        ->setPage('title', $title)
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
$cradle->get('/developer/app/update/:app_id', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin();

    //Prepare body
    $data = ['item' => $request->getPost()];

    //add CSRF
    cradle()->trigger('csrf-load', $request, $response);
    $data['csrf'] = $response->getResults('csrf');

    //if no item
    if(empty($data['item'])) {
        //get the detail with permission
        $permission = $request->getSession('me', 'profile_id');
        $request->setStage('permission', $permission);
        cradle()->trigger('app-detail', $request, $response);

        //can we update ?
        if($response->isError()) {
            //add a flash
            cradle('global')->flash($response->getMessage(), 'danger');
            return cradle('global')->redirect('/developer/app/search');
        }

        $data['item'] = $response->getResults();
    }

    if($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-developer-app-update branding';
    $title = cradle('global')->translate('Updating App');
    $body = cradle('/app/api')->template('developer/app/form', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render page
}, 'render-developer-page');

/**
 * Process the App Create Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/developer/app/create', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if($response->isError()) {
        return cradle()->triggerRoute('get', '/developer/app/create', $request, $response);
    }

    //add profile id
    $profile = $request->getSession('me', 'profile_id');
    $request->setStage('profile_id', $profile);

    //flatten permissions
    $permissions = $request->getStage('app_permissions');

    if(!$permissions) {
        $request->setStage('app_permissions', []);
    }

    cradle()->trigger('app-create', $request, $response);

    if($response->isError()) {
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
$cradle->post('/developer/app/update/:app_id', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if($response->isError()) {
        $route = '/developer/app/update/' . $request->getStage('app_id');
        return cradle()->triggerRoute('get', $route, $request, $response);
    }

    //set permission
    $permission = $request->getSession('me', 'profile_id');
    $request->setStage('permission', $permission);

    //flatten permissions
    $permissions = $request->getStage('app_permissions');

    if(!$permissions) {
        $request->setStage('app_permissions', []);
    }

    cradle()->trigger('app-update', $request, $response);

    if($response->isError()) {
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
$cradle->get('/developer/app/remove/:app_id', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if($response->isError()) {
        cradle('global')->flash($response->getMessage(), 'danger');
        return cradle('global')->redirect('/developer/app/search');
    }

    //set permission
    $permission = $request->getSession('me', 'profile_id');
    $request->setStage('permission', $permission);

    cradle()->trigger('app-remove', $request, $response);

    //deal with results
    if($response->isError()) {
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
$cradle->get('/developer/app/refresh/:app_id', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin();

    //csrf check
    cradle()->trigger('csrf-validate', $request, $response);

    if($response->isError()) {
        cradle('global')->flash($response->getMessage(), 'danger');
        return cradle('global')->redirect('/developer/app/search');
    }

    //set permission
    $permission = $request->getSession('me', 'profile_id');
    $request->setStage('permission', $permission);

    cradle()->trigger('app-refresh', $request, $response);

    //deal with results
    if($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('App was Refreshed');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('/developer/app/search');
});
