<?php //-->
/**
 * This file is part of the Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Sql\SqlFactory;

/**
 * OAuth Permission Check
 *
 * @param Request $request
 * @param Request $response
 */
$cradle->on('rest-permitted', function ($request, $response) {
    if ($request->hasStage('client_id')) {
        $this->trigger('rest-app-permitted', $request, $response);
    } else {
        $this->trigger('rest-session-permitted', $request, $response);
    }

    if ($response->isError() || !$request->hasStage('role')) {
        return;
    }

    $role = $request->getStage('role');
    $profile = $request->getStage('profile_id');

    //if there's a profile_id
    if ($profile) {
        //it should be an app
        if ($request->get('source', 'type') !== 'app') {
            return $response->setError(true, 'Unauthorize Request');
        }

        //I should have an admin role
        if (!in_array('admin_' . $role, $request->get('source', 'app_permissions'))) {
            return $response->setError(true, 'Unauthorize Request');
        }
    } else if ($request->get('source', 'type') === 'app') {
        //I should have a personal role
        if (!in_array('personal_' . $role, $request->get('source', 'app_permissions'))) {
            return $response->setError(true, 'Unauthorize Request');
        }

        $profile = $request->get('source', 'profile_id');
    } else {
        //I should have a user role
        if (!in_array('user_' . $role, $request->get('source', 'app_permissions'))) {
            return $response->setError(true, 'Unauthorize Request');
        }

        $profile = $request->get('source', 'profile_id');
    }

    //set profile
    $request->setStage('profile_id', $profile);
    $request->setStage('permission', $profile);

    //set app
    $app = $request->get('source', 'app_id');
    $request->setStage('app_id', $app);
});

/**
 * OAuth App Permission Check
 *
 * @param Request $request
 * @param Request $response
 */
$cradle->on('rest-app-permitted', function ($request, $response) {
    if (!$request->hasStage('client_id')) {
        return $response->setError(true, 'Unauthorize Request');
    }

    $token = $request->getStage('client_id');
    $secret = $request->getStage('client_secret');

    if ($request->getMethod() !== 'GET' && !$secret) {
        return $response->setError(true, 'Unauthorize Request');
    }

    $database = SqlFactory::load(cradle('global')->service('sql-main'));

    $search = $database
        ->search('app')
        ->setColumns('profile.*', 'app.*')
        ->innerJoinUsing('app_profile', 'app_id')
        ->innerJoinUsing('profile', 'profile_id')
        ->filterByAppToken($token);

    if ($secret) {
        $search->filterByAppSecret($secret);
    }

    $row = $search->getRow();

    if (empty($row)) {
        return $response->setError(true, 'Unauthorize Request');
    }

    if ($row['app_permissions']) {
        $row['app_permissions'] = json_decode($row['app_permissions'], true);
    } else {
        $row['app_permissions'] = [];
    }

    $request->set('source', $row);
    $request->set('source', 'type', 'app');
    $request->set('source', 'token', $token);
    $request->set('source', 'secret', $secret);

    return $response->setError(false);
});

/**
 * OAuth Session Permission Check
 *
 * @param Request $request
 * @param Request $response
 */
$cradle->on('rest-session-permitted', function ($request, $response) {
    if (!$request->hasStage('access_token')) {
        return $response->setError(true, 'Unauthorize Request');
    }

    $token = $request->getStage('access_token');
    $secret = $request->getStage('access_secret');

    if ($request->getMethod() !== 'GET' && !$secret) {
        return $response->setError(true, 'Unauthorize Request');
    }

    $database = SqlFactory::load(cradle('global')->service('sql-main'));

    $search = $database
        ->search('session')
        ->setColumns('session.*', 'profile.*', 'app.*')
        ->innerJoinUsing('session_app', 'session_id')
        ->innerJoinUsing('app', 'app_id')
        ->innerJoinUsing('session_auth', 'session_id')
        ->innerJoinUsing('auth_profile', 'auth_id')
        ->innerJoinUsing('profile', 'profile_id')
        ->filterBySessionToken($token)
        ->filterBySessionStatus('ACCESS');

    if ($secret) {
        $search->filterBySessionSecret($secret);
    }

    $row = $search->getRow();

    if (empty($row)) {
        return $response->setError(true, 'Unauthorize Request');
    }

    if ($row['session_permissions']) {
        $row['session_permissions'] = json_decode($row['session_permissions'], true);
    } else {
        $row['session_permissions'] = [];
    }

    $request->set('source', $row);
    $request->set('source', 'type', 'session');
    $request->set('source', 'token', $token);
    $request->set('source', 'secret', $secret);

    return $response->setError(false);
});

/**
 * Make a step to generate dialog pages
 *
 * @param Request $request
 * @param Request $response
 */
$cradle->on('render-dialog-page', function ($request, $response) {
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
$cradle->on('render-developer-page', function ($request, $response) {
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
