<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

{{~#if has_file}}

use Cradle\Module\Utility\File;
{{~/if}}

/**
 * Render the {{capital name}} Search Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/{{name}}/search', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    //Prepare body
    if(!$request->hasStage('range')) {
        $request->setStage('range', 50);
    }

    cradle()->trigger('{{name}}-search', $request, $response);
    $data = array_merge($request->getStage(), $response->getResults());

    //Render body
    $class = 'page-admin-{{name}}-search page-admin';
    $data['title'] = cradle('global')->translate('{{capital plural}}');
    $body = cradle('/app/admin')->template('{{name}}/search', $data);

    //Set Content
    $response
        ->setPage('title', $data['title'])
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
$cradle->get('/admin/{{name}}/create', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    //Prepare body
    $data = ['item' => $request->getPost()];

    {{~#if has_file}}

    //add CDN
    $config = $this->package('global')->service('s3-main');
    $data['cdn_config'] = File::getS3Client($config);
    {{~/if}}

    if($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-developer-{{name}}-create page-admin';
    $data['title'] = cradle('global')->translate('Create {{capital singular}}');
    $body = cradle('/app/admin')->template('{{name}}/form', $data);

    //Set Content
    $response
        ->setPage('title', $data['title'])
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
$cradle->get('/admin/{{name}}/update/:{{primary}}', function($request, $response) {
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
            return cradle('global')->redirect('/admin/{{name}}/search');
        }

        $data['item'] = $response->getResults();
    }

    if($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //Render body
    $class = 'page-developer-{{name}}-update page-admin';
    $data['title'] = cradle('global')->translate('Updating {{capital singular}}');
    $body = cradle('/app/admin')->template('{{name}}/form', $data);

    //Set Content
    $response
        ->setPage('title', $data['title'])
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
$cradle->post('/admin/{{name}}/create', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    {{~#each fields}}
        {{~#unless form.length}}

    //{{name}} is disallowed
    $request->removeStage('{{name}}');
        {{~else}}
            {{~#if sql.default}}

    //if {{name}} has no value use the default value
    if ($request->hasStage('{{name}}') && !$request->getStage('{{name}}')) {
        $request->setStage('{{name}}', '{{sql.default}}');
    }
            {{~else}}
                {{~#unless sql.required}}

    //if {{name}} has no value make it null
    if ($request->hasStage('{{name}}') && !$request->getStage('{{name}}')) {
        $request->setStage('{{name}}', null);
    }
                {{~/unless}}
            {{~/if}}
        {{~/unless}}
    {{~/each}}

    {{~#if relations.profile}}

    if(!$request->hasStage('profile_id')) {
        $request->setStage('profile_id', $request->getSession('me', 'profile_id'));
    }
    {{~/if}}
    {{~#if relations.app}}

    if(!$request->hasStage('app_id')) {
        $request->setStage('app_id', $request->getSession('me', 'app_id'));
    }
    {{~/if}}

    cradle()->trigger('{{name}}-create', $request, $response);

    if($response->isError()) {
        return cradle()->triggerRoute('get', '/admin/{{name}}/create', $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('{{capital singular}} was Created', 'success');

    //redirect
    cradle('global')->redirect('/admin/{{name}}/search');
});

/**
 * Process the {{capital name}} Update Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/admin/{{name}}/update/:{{primary}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    {{~#each fields}}
        {{~#unless form.length}}

    //{{name}} is disallowed
    $request->removeStage('{{name}}');
        {{~else}}
            {{~#if sql.default}}

    //if {{name}} has no value use the default value
    if ($request->hasStage('{{name}}') && !$request->getStage('{{name}}')) {
        $request->setStage('{{name}}', '{{sql.default}}');
    }
            {{~else}}
                {{~#unless sql.required}}

    //if {{name}} has no value make it null
    if ($request->hasStage('{{name}}') && !$request->getStage('{{name}}')) {
        $request->setStage('{{name}}', null);
    }
                {{~/unless}}
            {{~/if}}
        {{~/unless}}
    {{~/each}}

    cradle()->trigger('{{name}}-update', $request, $response);

    if($response->isError()) {
        $route = '/admin/{{name}}/update/' . $request->getStage('{{primary}}');
        return cradle()->triggerRoute('get', $route, $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('{{capital singular}} was Updated', 'success');

    //redirect
    cradle('global')->redirect('/admin/{{name}}/search');
});

/**
 * Process the {{capital name}} Remove
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/{{name}}/remove/:{{primary}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    cradle()->trigger('{{name}}-remove', $request, $response);

    //deal with results
    if($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('{{capital singular}} was Removed');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('/admin/{{name}}/search');
});
{{#if active}}
/**
 * Process the {{capital name}} Restore
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/{{name}}/restore/:{{primary}}', function($request, $response) {
    //for logged in
    cradle('global')->requireLogin('admin');

    cradle()->trigger('{{name}}-restore', $request, $response);

    //deal with results
    if($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('{{capital singular}} was Restored');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('/admin/{{name}}/search');
});
{{/if}}
