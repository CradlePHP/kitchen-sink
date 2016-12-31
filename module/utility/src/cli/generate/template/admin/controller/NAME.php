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
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
    if(!$request->hasStage('range')) {
        $request->setStage('range', 50);
    }

    {{~#if sortable.length}}

    //filter possible sorting options
    //we do this to prevent SQL injections
    if(is_array($request->getStage('order'))) {
        $sortable = [
        {{~#each sortable}}
            {{~#noop}}
            '{{this}}'{{#unless @last}},{{/unless}}
            {{~/noop~}}
        {{/each}}
        ];

        foreach($request->getStage('order') as $key => $direction) {
            if(!in_array($key, $sortable)) {
                $request->removeStage('order', $key);
            } else if ($direction !== 'ASC' && $direction !== 'DESC') {
                $request->removeStage('order', $key);
            }
        }
    }
    {{~/if}}

    {{~#if filterable.length}}

    //filter possible filter options
    //we do this to prevent SQL injections
    if(is_array($request->getStage('filter'))) {
        $filterable = [
            {{~#each filerable}}
                {{~#noop}}
                '{{this}}'{{#unless @last}},{{/unless}}
                {{~/noop~}}
            {{/each}}
        ];

        foreach($request->getStage('filter') as $key => $value) {
            if(!in_array($key, $sortable)) {
                $request->removeStage('filter', $key);
            }
        }
    }
    {{~/if}}

    //trigger job
    cradle()->trigger('{{name}}-search', $request, $response);
    $data = array_merge($request->getStage(), $response->getResults());

    //----------------------------//
    // 3. Render Template
    $class = 'page-admin-{{name}}-search page-admin';
    $data['title'] = cradle('global')->translate('{{capital plural}}');
    $body = cradle('/app/admin')->template('{{name}}/search', $data);

    //set content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);

    //render page
}, 'render-admin-page');

/**
 * Render the {{capital name}} Create Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/{{name}}/create', function($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
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

    //----------------------------//
    // 3. Render Template
    $class = 'page-developer-{{name}}-create page-admin';
    $data['title'] = cradle('global')->translate('Create {{capital singular}}');
    $body = cradle('/app/admin')->template('{{name}}/form', $data);

    //set content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);

    //render page
}, 'render-admin-page');

/**
 * Render the {{capital name}} Update Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/{{name}}/update/:{{primary}}', function($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
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

    //----------------------------//
    // 3. Render Template
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
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
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
        $request->setStage('app_id', 1);
    }
    {{~/if}}

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-create', $request, $response);

    //----------------------------//
    // 4. Interpret Results
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
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
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

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-update', $request, $response);

    //----------------------------//
    // 4. Interpret Results
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
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
    // no data to preapre
    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-remove', $request, $response);

    //----------------------------//
    // 4. Interpret Results
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
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
    // no data to preapre
    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-restore', $request, $response);

    //----------------------------//
    // 4. Interpret Results
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
