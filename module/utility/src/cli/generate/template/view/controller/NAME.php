<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Render the {{capital name}} Search Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('{{path.search}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    //Prepare body
    cradle()->trigger('{{name}}-search', $request, $response);
    $data = array_merge($request->getStage(), $response->getResults());

    //Render body
    $class = 'page-admin-{{name}}-search page-admin';
    $title = cradle('global')->translate('{{capital name}} Search');
    $body = cradle('/app/admin')->template('{{name}}/search', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render page
}, 'render-admin-page');

/**
 * Render the {{capital name}} Create Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('{{path.create}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    //Prepare body
    $data = ['item' => $request->getPost()];

    if($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-developer-{{name}}-create page-admin';
    $title = cradle('global')->translate('Create {{capital name}}');
    $body = cradle('/{{name}}/api')->template('{{name}}/form', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render page
}, 'render-admin-page');

/**
 * Render the {{capital name}} Update Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('{{path.update}}/:{{primary}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    //Prepare body
    $data = ['item' => $request->getPost()];

    //if no item
    if(empty($data['item'])) {
        cradle()->trigger('{{name}}-detail', $request, $response);

        //can we update ?
        if($response->isError()) {
            //add a flash
            cradle('global')->flash($response->getMessage(), 'danger');
            return cradle('global')->redirect('{{search}}');
        }

        $data['item'] = $response->getResults();
    }

    if($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-developer-{{name}}-update page-admin';
    $title = cradle('global')->translate('Updating {{capital name}}');
    $body = cradle('/{{name}}/api')->template('{{name}}/form', $data);

    //Set Content
    $response
        ->setPage('title', $title)
        ->setPage('class', $class)
        ->setContent($body);

    //Render page
}, 'render-admin-page');

/**
 * Process the {{capital name}} Create Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('{{path.create}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    cradle()->trigger('{{name}}-create', $request, $response);

    if($response->isError()) {
        return cradle()->triggerRoute('get', '{{create}}', $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('{{capital name}} was Created', 'success');

    //redirect
    cradle('global')->redirect('{{path.search}}');
});

/**
 * Process the {{capital name}} Update Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('{{path.update}}/:{{primary}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    cradle()->trigger('{{name}}-update', $request, $response);

    if($response->isError()) {
        $route = '{{update}}/' . $request->getStage('{{name}}_id');
        return cradle()->triggerRoute('get', $route, $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('{{capital name}} was Updated', 'success');

    //redirect
    cradle('global')->redirect('{{path.search}}');
});

/**
 * Process the {{capital name}} Remove
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('{{path.remove}}/:{{primary}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    cradle()->trigger('{{name}}-remove', $request, $response);

    //deal with results
    if($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('{{capital name}} was Removed');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('{{path.search}}');
});
{{#if active}}
/**
 * Process the {{capital name}} Restore
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('{{path.restore}}/:{{primary}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    cradle()->trigger('{{name}}-restore', $request, $response);

    //deal with results
    if($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('{{capital name}} was Restored');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('{{path.search}}');
});
{{/if}}
