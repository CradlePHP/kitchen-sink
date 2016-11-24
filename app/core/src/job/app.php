<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * App Create Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('app-create', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $appModel = $this->package('/app/core')->model('app');

    //validate
    $errors = $appModel->getCreateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //save item to database
    $results = $appModel->databaseCreate($data);

    //link item to profile
    $appModel->linkProfile($results['app_id'], $results['profile_id']);

    //index item
    $appModel->indexCreate($results['app_id']);

    //invalidate cache
    $appModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* App Detail Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('app-detail', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    $id = null;
    if (isset($data['app_id'])) {
        $id = $data['app_id'];
    } else if (isset($data['app_token'])) {
        $id = $data['app_token'];
    }

    //we need an id
    if (!$id) {
        return $response->setError(true, 'Invalid ID');
    }

    //this/these will be used a lot
    $appModel = $this->package('/app/core')->model('app');

    //get it from cache
    $results = $appModel->cacheDetail($id);

    //if no results
    if(!$results) {
        //get it from index
        $results = $appModel->indexDetail($id);

        //if no results
        if(!$results) {
            //get it from database
            $results = $appModel->databaseDetail($id);
        }

        if($results) {
            //cache it from database or index
            $appModel->cacheCreateDetail($id, $results);
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
* App Refresh Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('app-refresh', function ($request, $response) {
    //get the item detail
    $this->trigger('app-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $appModel = $this->package('/app/core')->model('app');

    //save to database
    $results = $appModel->databaseUpdate([
        'app_id' => $data['app_id'],
        'app_token' => md5(uniqid()),
        'app_secret' => md5(uniqid())
    ]);

    //index item
    $appModel->indexUpdate($data['app_id']);

    //invalidate cache
    $appModel->cacheRemoveDetail($data['app_id']);
    $appModel->cacheRemoveDetail($data['app_token']);
    $appModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* App Remove Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('app-remove', function ($request, $response) {
    //get the item detail
    $this->trigger('app-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $appModel = $this->package('/app/core')->model('app');

    //save to database
    $results = $appModel->databaseUpdate([
        'app_id' => $data['app_id'],
        'app_active' => 0
    ]);

    //remove from index
    $appModel->indexRemove($data['app_id']);

    //invalidate cache
    $appModel->cacheRemoveDetail($data['app_id']);
    $appModel->cacheRemoveDetail($data['app_token']);
    $appModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($data);
});

/**
* App Restore Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('app-restore', function ($request, $response) {
    //get the app detail
    $this->trigger('app-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $appModel = $this->package('/app/core')->model('app');

    //save to database
    $results = $appModel->databaseUpdate([
        'app_id' => $data['app_id'],
        'app_active' => 1
    ]);

    //re add from index
    $appModel->indexCreate($data['app_id']);

    //invalidate cache
    $appModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($data);
});

/**
* App Search Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('app-search', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $appModel = $this->package('/app/core')->model('app');

    //get it from cache
    $results = $appModel->cacheSearch($data);

    //if no results
    if(!$results) {
        //get it from index
        $results = $appModel->indexSearch($data);

        //if no results
        if(!$results) {
            //get it from database
            $results = $appModel->databaseSearch($data);
        }

        //cache it from database or index
        $appModel->cacheCreateSearch($results);
    }

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* App Update Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('app-update', function ($request, $response) {
    //get the app detail
    $this->trigger('app-detail', $request, $response);

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
    $appModel = $this->package('/app/core')->model('app');

    //validate
    $errors = $appModel->getUpdateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //save to database
    $results = $appModel->databaseUpdate($data);

    //update index
    $appModel->indexUpdate($response->getResults('app_id'));

    //invalidate cache
    $appModel->cacheRemoveDetail($response->getResults('app_id'));
    $appModel->cacheRemoveDetail($response->getResults('app_token'));
    $appModel->cacheRemoveSearch();

    //set response format
    $response->setError(false)->setResults($results);
});
