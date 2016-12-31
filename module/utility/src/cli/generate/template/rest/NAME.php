<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * {{capital name}} search
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/{{name}}/search', function($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only if permitted
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    //----------------------------//
    // 2. Prepare Data
    if(!$request->hasStage('range')) {
        $request->setStage('range', 50);
    }

    {{#if sortable.length}}
    //filter possible sorting options
    //we do this to prevent SQL injections
    $sortable = [
        {{~#each sortable}}
            {{~#noop}}
            '{{this}}'{{#unless @last}},{{/unless}}
            {{~/noop~}}
        {{/each}}
    ];

    foreach($request->getStage('order') as $key => $direction) {
        if(!in_array($key, $sortable)) {
            $request->remove('stage', 'order', $key);
        } else if ($direction !== 'ASC' && $direction !== 'DESC') {
            $request->remove('stage', 'order', $key);
        }
    }
    {{/if}}

    {{#if filterable.length}}
    //filter possible filter options
    //we do this to prevent SQL injections
    $filterable = [
        {{~#each filterable}}
            {{~#noop}}
            '{{this}}'{{#unless @last}},{{/unless}}
            {{~/noop~}}
        {{/each}}
    ];

    foreach($request->getStage('filter') as $key => $value) {
        if(!in_array($key, $sortable)) {
            $request->remove('stage', 'filter', $key);
        }
    }
    {{/if}}

    {{~#if relations.profile}}

    $profile = $request->getStage('profile_id');
    $request->setStage('filter', 'profile_id', $profile);
    {{~/if}}

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-search', $request, $response);
});

/**
 * {{capital name}} detail
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/{{name}}/detail/:{{primary}}', function($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only if permitted
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    //----------------------------//
    // 2. Prepare Data
    // no data to preapre
    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-detail', $request, $response);
});

/**
 * {{capital name}} create
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/rest/{{name}}/create', function($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only if permitted
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

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

    //optional
    {{~#each fields}}
        {{~#if sql.default}}
    if ($request->hasStage('{{name}}') && !$request->getStage('{{name}}')) {
        $request->setStage('{{name}}', '{{sql.default}}');
    }
        {{~else}}
            {{~#unless sql.required}}
    if ($request->hasStage('{{name}}') && !$request->getStage('{{name}}')) {
        $request->setStage('{{name}}', null);
    }
            {{~/unless}}
        {{~/if}}
    {{~/each}}

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-create', $request, $response);
});

/**
 * {{capital name}} update
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/rest/{{name}}/update/:{{primary}}', function($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only if permitted
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

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
});

/**
 * {{capital name}} remove
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/{{name}}/remove/:{{primary}}', function($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only if permitted
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    //----------------------------//
    // 2. Prepare Data
    // no data to preapre
    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-remove', $request, $response);
});
{{#if active}}
/**
 * {{capital name}} restore
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/{{name}}/restore/:{{primary}}', function($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only if permitted
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    //----------------------------//
    // 2. Prepare Data
    // no data to preapre
    //----------------------------//
    // 3. Process Request
    cradle()->trigger('{{name}}-restore', $request, $response);
});
{{/if}}
