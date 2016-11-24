<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * OAuth Permission Check
 *
 * @param Request $request
 * @param Request $response
 */
$cradle->on('rest-permitted', function($request, $response) {
    $role = $request->getStage('role');
    $token = $request->getStage('access_token');
    $secret = $request->getStage('access_secret');

    if(!$token) {
        return false;
    }

    if ($request->getMethod() === 'GET' && !$secret) {
        return $response->setError(true, 'Unauthorize Request');
    }

    if (strpos($role, 'user_') === 0) {
        //retreive the permissions based on the session token and session secret
        $search = $this
            ->package('global')
            ->database()
            ->search('session')
            ->setColumns('session.*', 'profile.*', 'app.*')
            ->innerJoinUsing('session_app', 'session_id')
            ->innerJoinUsing('app', 'app_id')
            ->innerJoinUsing('session_auth', 'session_id')
            ->innerJoinUsing('auth_profile', 'auth_id')
            ->innerJoinUsing('profile', 'profile_id')
            ->filterBySessionToken($token)
            ->filterBySessionStatus('ACCESS')
            ->addFilter('session_permissions LIKE %s', '%' . $role . '%');

        if ($secret) {
            $search->filterBySessionSecret($secret);
        }

        $row = $search->getRow();

        if (empty($row)) {
            return $response->setError(true, 'Unauthorize Request');
        }

        $request->set('source', $row);
        $request->set('source', 'access_token', $token);
        $request->set('source', 'access_secret', $secret);

        return $response->setError(false);
    }

    $search = $this
        ->package('global')
        ->database()
        ->search('app')
        ->setColumns('profile.*', 'app.*')
        ->innerJoinUsing('app_profile', 'app_id')
        ->innerJoinUsing('profile', 'profile_id')
        ->filterByAppToken($token)
        ->addFilter('app_permissions LIKE %s', '%' . $role . '%');

    if ($secret) {
        $search->filterByAppSecret($secret);
    }

    $row = $search->getRow();

    if (empty($row)) {
        return $response->setError(true, 'Unauthorize Request');
    }

    $request->set('source', $row);
    $request->set('source', 'access_token', $token);
    $request->set('source', 'access_secret', $secret);

    return $response->setError(false);
});

/**
 * Make a step to generate dialog pages
 *
 * @param Request $request
 * @param Request $response
 */
$cradle->on('render-dialog-page', function($request, $response) {
    $content = cradle('/app/api')->template('dialog/_page', [
        'page' => $response->getPage(),
        'results' => $response->getResults(),
        'content' => $response->getContent()
    ]);

    $response->setContent($content);
});

/**
 * Make a step to generate developer pages
 *
 * @param Request $request
 * @param Request $response
 */
$cradle->on('render-developer-page', function($request, $response) {
    //protocol
    $protocol = 'http';
    if($_SERVER['SERVER_PORT'] != 80) {
        $protocol = 'https';
    }

    //url and base
    $base = $url = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if(strpos($url, '?') !== false) {
        $base = substr($url, 0, strpos($url, '?') + 1);
    }

    $response->addMeta('url', $url)->addMeta('base', $base);

    //path
    $path = $request->getPath('string');
    if(strpos($path, '?') !== false) {
        $path = substr($path, 0, strpos($path, '?'));
    }

    $response->addMeta('path', $path);

    $content = cradle('/app/api')->template(
        'developer/_page',
        [
            'page' => $response->getPage(),
            'results' => $response->getResults(),
            'content' => $response->getContent()
        ],
        [
            'developer_head',
            'developer_foot'
        ]
    );

    $response->setContent($content);
});

/**
 * Generic template method for app/wwww
 *
 * @param *string $path
 * @param array   $data
 * @param array   $partial
 *
 * @return string
 */
$cradle->package('/app/api')->addMethod('template', function ($path, array $data = [], $partials = []) {
    // get the root directory
    $root = __DIR__ . '/src/template/';

    //render
    $handlebars = cradle()->package('global')->handlebars();

    // check for partials
    if(!is_array($partials)) {
        $partials = [$partials];
    }

    foreach ($partials as $partial) {
        //Sample: product_comment => product/_comment
        //Sample: flash => _flash
        $file = str_replace('_', '/_', $partial) . '.html';

        if(strpos($file, '_') === false) {
            $file = '_' . $file;
        }

        // register the partial
        $handlebars->registerPartial($partial, file_get_contents($root . $file));
    }

    // set the main template
    $template = $handlebars->compile(file_get_contents($root . $path . '.html'));
    return $template($data);
});

//include the other routes
include_once(__DIR__ . '/src/controller/rest/auth.php');
include_once(__DIR__ . '/src/controller/rest/profile.php');

include_once(__DIR__ . '/src/controller/dialog/auth.php');
include_once(__DIR__ . '/src/controller/developer/auth.php');
include_once(__DIR__ . '/src/controller/developer/app.php');
