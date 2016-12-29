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
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    {{~#if relations.profile}}

    $profile = $request->getStage('profile_id');
    $request->setStage('filter', 'profile_id', $profile);
    {{~/if}}

    cradle()->trigger('{{name}}-search', $request, $response);
});

/**
 * {{capital name}} detail
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/{{name}}/detail/:{{primary}}', function($request, $response) {
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    cradle()->trigger('{{name}}-detail', $request, $response);
});

/**
 * {{capital name}} create
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/rest/{{name}}/create', function($request, $response) {
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

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

    cradle()->trigger('{{name}}-create', $request, $response);
});

/**
 * {{capital name}} update
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/rest/{{name}}/update/:{{primary}}', function($request, $response) {
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

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
});

/**
 * {{capital name}} remove
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/{{name}}/remove/:{{primary}}', function($request, $response) {
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

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
    $request->setStage('role', '{{name}}');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    cradle()->trigger('{{name}}-restore', $request, $response);
});
{{/if}}
