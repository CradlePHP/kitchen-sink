<?php //-->
/**
 * This file is part of the Custom Project.
 * (c) 2017-2019 Acme Inc.
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
            ->addFilter(
                "JSON_SEARCH(session_permissions, 'one', %s) IS NOT NULL",
                $role
            );

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

        //CUSTOM FOR SALAAAP
        $request->setStage('profile_experience_id', $row['profile_id']);

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
        ->addFilter(
            "JSON_SEARCH(app_permissions, 'one', %s) IS NOT NULL",
            $role
        );

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

    //CUSTOM FOR SALAAAP
    $request->setStage('profile_experience_id', $row['profile_id']);

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
