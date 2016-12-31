<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Oauth\App\Service as AppService;
use Cradle\Module\Oauth\App\Validator as AppValidator;

/**
 * App Create Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('app-create', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    $errors = AppValidator::getCreateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //----------------------------//
    // 3. Prepare Data
    //deflate permissions
    $data['app_permissions'] = json_encode($data['app_permissions']);

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $appSql = AppService::get('sql');
    $appRedis = AppService::get('redis');
    $appElastic = AppService::get('elastic');

    //save item to database
    $results = $appSql->create($data);

    //link item to profile
    $appSql->linkProfile($results['app_id'], $results['profile_id']);

    //index item
    $appElastic->create($results['app_id']);

    //invalidate cache
    $appRedis->removeSearch();

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
    //----------------------------//
    // 1. Get Data
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

    //----------------------------//
    // 2. Validate Data
    //we need an id
    if (!$id) {
        return $response->setError(true, 'Invalid ID');
    }

    //----------------------------//
    // 3. Prepare Data
    //no preparation needed
    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $appSql = AppService::get('sql');
    $appRedis = AppService::get('redis');
    $appElastic = AppService::get('elastic');

    $results = null;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = $appRedis->getDetail($id);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = $appElastic->get($id);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = $appSql->get($id);
        }

        if ($results) {
            //cache it from database or index
            $appRedis->createDetail($id, $results);
        }
    }

    if (!$results) {
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
    //----------------------------//
    // 1. Get Data
    $this->trigger('app-detail', $request, $response);

    //----------------------------//
    // 2. Validate Data
    if ($response->isError()) {
        return;
    }

    //----------------------------//
    // 3. Prepare Data
    $data = $response->getResults();

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $appSql = AppService::get('sql');
    $appRedis = AppService::get('redis');
    $appElastic = AppService::get('elastic');

    //save to database
    $results = $appSql->update([
        'app_id' => $data['app_id'],
        'app_token' => md5(uniqid()),
        'app_secret' => md5(uniqid())
    ]);

    //index item
    $appElastic->update($data['app_id']);

    //invalidate cache
    $appRedis->removeDetail($data['app_id']);
    $appRedis->removeDetail($data['app_token']);
    $appRedis->removeSearch();

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
    //----------------------------//
    // 1. Get Data
    $this->trigger('app-detail', $request, $response);

    //----------------------------//
    // 2. Validate Data
    if ($response->isError()) {
        return;
    }

    //----------------------------//
    // 3. Prepare Data
    $data = $response->getResults();

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $appSql = AppService::get('sql');
    $appRedis = AppService::get('redis');
    $appElastic = AppService::get('elastic');

    //save to database
    $results = $appSql->update([
        'app_id' => $data['app_id'],
        'app_active' => 0
    ]);

    //remove from index
    $appElastic->remove($data['app_id']);

    //invalidate cache
    $appRedis->removeDetail($data['app_id']);
    $appRedis->removeDetail($data['app_token']);
    $appRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* App Restore Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('app-restore', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $this->trigger('app-detail', $request, $response);

    //----------------------------//
    // 2. Validate Data
    if ($response->isError()) {
        return;
    }

    //----------------------------//
    // 3. Prepare Data
    $data = $response->getResults();

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $appSql = AppService::get('sql');
    $appRedis = AppService::get('redis');
    $appElastic = AppService::get('elastic');

    //save to database
    $results = $appSql->update([
        'app_id' => $data['app_id'],
        'app_active' => 1
    ]);

    //re add from index
    $appElastic->create($data['app_id']);

    //invalidate cache
    $appRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});

/**
* App Search Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('app-search', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    //no validation needed
    //----------------------------//
    // 3. Prepare Data
    //no preparation needed
    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $appSql = AppService::get('sql');
    $appRedis = AppService::get('redis');
    $appElastic = AppService::get('elastic');

    $results = false;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = $appRedis->getSearch($data);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = $appElastic->search($data);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = $appSql->search($data);
        }

        if ($results) {
            //cache it from database or index
            $appRedis->createSearch($data, $results);
        }
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
    //----------------------------//
    // 1. Get Data
    $this->trigger('app-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    $errors = AppValidator::getUpdateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //----------------------------//
    // 3. Prepare Data
    //deflate permissions
    if (isset($data['app_permissions'])) {
        $data['app_permissions'] = json_encode($data['app_permissions']);
    }

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $appSql = AppService::get('sql');
    $appRedis = AppService::get('redis');
    $appElastic = AppService::get('elastic');

    //save to database
    $results = $appSql->update($data);

    //update index
    $appElastic->update($response->getResults('app_id'));

    //invalidate cache
    $appRedis->removeDetail($response->getResults('app_id'));
    $appRedis->removeDetail($response->getResults('app_token'));
    $appRedis->removeSearch();

    //set response format
    $response->setError(false)->setResults($results);
});
