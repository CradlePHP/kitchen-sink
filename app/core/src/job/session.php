<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
* Session Access Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-access', function ($request, $response) {
    //get the session detail
    $this->trigger('session-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $sessionModel = $this->package('/app/core')->model('session');

    //validate
    $errors = $sessionModel->getAccessErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //save to database
    $results = $sessionModel->databaseUpdate([
        'session_id' => $response->getResults('session_id'),
        'session_status' => 'ACCESS',
        'session_token' => md5(uniqid()),
        'session_secret' => md5(uniqid())
    ]);

    //index app
    $sessionModel->indexUpdate($response->getResults('session_id'));

    //invalidate cache
    $sessionModel->cacheRemoveDetail($response->getResults('session_id'));
    $sessionModel->cacheRemoveDetail($response->getResults('session_token'));
    $sessionModel->cacheRemoveSearch();

    //return response format
    $response->setError(false)->setResults([
        'access_token' => $results['session_token'],
        'access_secret' => $results['session_secret'],
        'profile_id' => $response->getResults('profile_id'),
        'profile_name' => $response->getResults('profile_name'),
        'profile_email' => $response->getResults('profile_email'),
        'profile_image' => $response->getResults('profile_image')
    ]);
});

/**
 * Session Create Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('session-create', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $sessionModel = $this->package('/app/core')->model('session');

    //validate
    $errors = $sessionModel->getCreateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //deflate permissions
    $data['session_permissions'] = json_encode($data['session_permissions']);

    //save to database
    $results = $sessionModel->databaseCreate($data);

    //link auth
    $sessionModel->linkAuth($results['session_id'], $results['auth_id']);

    //link app
    $sessionModel->linkApp($results['session_id'], $results['app_id']);

    //index
    $sessionModel->indexCreate($results['session_id']);

    //invalidate cache
    $sessionModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* Session Detail Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-detail', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    $id = null;
    if (isset($data['session_id'])) {
        $id = $data['session_id'];
    } else if (isset($data['session_token'])) {
        $id = $data['session_token'];
    }

    //we need a app id
    if (!$id) {
        return $response->setError(true, 'Invalid ID');
    }

    //this/these will be used a lot
    $sessionModel = $this->package('/app/core')->model('session');

    //get it from cache
    $results = $sessionModel->cacheDetail($id);

    //if no results
    if(!$results) {
        //get it from index
        $results = $sessionModel->indexDetail($id);

        //if no results
        if(!$results) {
            //get it from database
            $results = $sessionModel->databaseDetail($id);
        }

        if($results) {
            //cache it from database or index
            $sessionModel->cacheCreateDetail($id, $results);
        }
    }

    if(!$results) {
        return $response->setError(true, 'Not Found');
    }

    //if permission is provided
    $permission = $request->getStage('permission');
    if ($permission && $results['profile_id'] != $permission) {
        return $response->setError(true, 'Invalid Permissions');
    }

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* Session Refresh Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-refresh', function ($request, $response) {
    //get the item detail
    $this->trigger('session-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $sessionModel = $this->package('/app/core')->model('session');

    //save to database
    $results = $sessionModel->databaseUpdate([
        'session_id' => $data['session_id'],
        'session_token' => md5(uniqid()),
        'session_secret' => md5(uniqid())
    ]);

    //index item
    $sessionModel->indexUpdate($data['session_id']);

    //invalidate cache
    $sessionModel->cacheRemoveDetail($data['session_id']);
    $sessionModel->cacheRemoveDetail($data['session_token']);
    $sessionModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* Session Remove Job (Hard Remove)
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-remove', function ($request, $response) {
    //get the session detail
    $this->trigger('session-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //this/these will be used a lot
    $sessionModel = $this->package('/app/core')->model('session');

    //get data
    $data = $response->getResults();

    //remove from database
    $sessionModel->databaseRemove($data['session_id']);

    //link auth
    $sessionModel->unlinkAuth($data['session_id'], $data['auth_id']);

    //link app
    $sessionModel->unlinkApp($data['session_id'], $data['app_id']);

    //remove from index
    $sessionModel->indexRemove($data['session_id']);

    //invalidate cache
    $sessionModel->cacheRemoveDetail($data['session_id']);
    $sessionModel->cacheRemoveDetail($data['session_token']);
    $sessionModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($data);
});

/**
* Session Search Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-search', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $sessionModel = $this->package('/app/core')->model('session');

    //get it from cache
    $results = $sessionModel->cacheSearch($data);

    //if no results
    if(!$results) {
        //get it from index
        $results = $sessionModel->indexSearch($data);

        //if no results
        if(!$results) {
            //get it from database
            $results = $sessionModel->databaseSearch($data);
        }

        //cache it from database or index
        $sessionModel->cacheCreateSearch($data, $results);
    }

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* Session Update Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('session-update', function ($request, $response) {
    //get the session detail
    $this->trigger('session-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $sessionModel = $this->package('/app/core')->model('session');

    //validate
    $errors = $sessionModel->getUpdateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //deflate permissions
    if(isset($data['session_permissions'])) {
        $data['session_permissions'] = json_encode($data['session_permissions']);
    }

    //save app to database
    $results = $sessionModel->databaseUpdate($data);

    //index
    $sessionModel->indexUpdate($response->getResults('session_id'));

    //invalidate cache
    $sessionModel->cacheRemoveDetail($response->getResults('session_id'));
    $sessionModel->cacheRemoveDetail($response->getResults('session_token'));
    $sessionModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($results);
});
