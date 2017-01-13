<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Profile\Service as ProfileService;
use Cradle\Module\Profile\Validator as ProfileValidator;

use Cradle\Http\Request;
use Cradle\Http\Response;

use Cradle\Module\Utility\File;

/**
 * Profile Create Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('profile-create', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    $errors = ProfileValidator::getCreateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //----------------------------//
    // 3. Prepare Data

    //if there is an image
    if (isset($data['profile_image'])) {
        //upload files
        //try cdn if enabled
        $config = $this->package('global')->service('s3-main');
        $data['profile_image'] = File::base64ToS3($data['profile_image'], $config);
        //try being old school
        $upload = $this->package('global')->path('upload');
        $data['profile_image'] = File::base64ToUpload($data['profile_image'], $upload);
    }

    if (isset($data['profile_birth'])) {
        $data['profile_birth'] = date('Y-m-d', strtotime($data['profile_birth']));
    }

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $profileSql = ProfileService::get('sql');
    $profileRedis = ProfileService::get('redis');
    $profileElastic = ProfileService::get('elastic');

    //save profile to database
    $results = $profileSql->create($data);

    //index profile
    $profileElastic->create($results['profile_id']);

    //invalidate cache
    $profileRedis->removeSearch();

    //return response format
    $response->setError(false)->setResults($results);
});

/**
 * Profile Detail Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('profile-detail', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    $id = null;
    if (isset($data['profile_id'])) {
        $id = $data['profile_id'];
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
    $profileSql = ProfileService::get('sql');
    $profileRedis = ProfileService::get('redis');
    $profileElastic = ProfileService::get('elastic');

    $results = null;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = $profileRedis->getDetail($id);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = $profileElastic->get($id);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = $profileSql->get($id);
        }

        if ($results) {
            //cache it from database or index
            $profileRedis->createDetail($id, $results);
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

    $response->setError(false)->setResults($results);
});

/**
 * Profile Remove Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('profile-remove', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    //get the profile detail
    $this->trigger('profile-detail', $request, $response);

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
    $profileSql = ProfileService::get('sql');
    $profileRedis = ProfileService::get('redis');
    $profileElastic = ProfileService::get('elastic');

    //save to database
    $results = $profileSql->update([
        'profile_id' => $data['profile_id'],
        'profile_active' => 0
    ]);

    //remove from index
    $profileElastic->remove($data['profile_id']);

    //invalidate cache
    $profileRedis->removeDetail($data['profile_id']);
    $profileRedis->removeSearch();

    $response->setError(false)->setResults($results);
});

/**
 * Profile Restore Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('profile-restore', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    //get the profile detail
    $this->trigger('profile-detail', $request, $response);

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
    $profileSql = ProfileService::get('sql');
    $profileRedis = ProfileService::get('redis');
    $profileElastic = ProfileService::get('elastic');

    //save to database
    $results = $profileSql->update([
        'profile_id' => $data['profile_id'],
        'profile_active' => 1
    ]);

    //create index
    $profileElastic->create($data['profile_id']);

    //invalidate cache
    $profileRedis->removeSearch();

    $response->setError(false)->setResults($results);
});

/**
 * Profile Search Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('profile-search', function ($request, $response) {
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
    $profileSql = ProfileService::get('sql');
    $profileRedis = ProfileService::get('redis');
    $profileElastic = ProfileService::get('elastic');

    $results = false;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = $profileRedis->getSearch($data);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = $profileElastic->search($data);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = $profileSql->search($data);
        }

        if ($results) {
            //cache it from database or index
            $profileRedis->createSearch($data, $results);
        }
    }

    //set response format
    $response->setError(false)->setResults($results);
});

/**
 * Profile Update Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('profile-update', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    //get the profile detail
    $this->trigger('profile-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data from stage
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    $errors = ProfileValidator::getUpdateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //----------------------------//
    // 3. Prepare Data

    //if there is an image
    if (isset($data['profile_image'])) {
        //upload files
        //try cdn if enabled
        $config = $this->package('global')->service('s3-main');
        $data['profile_image'] = File::base64ToS3($data['profile_image'], $config);
        //try being old school
        $upload = $this->package('global')->path('upload');
        $data['profile_image'] = File::base64ToUpload($data['profile_image'], $upload);
    }

    if (isset($data['profile_birth'])) {
        $data['profile_birth'] = date('Y-m-d', strtotime($data['profile_birth']));
    }

    //----------------------------//
    // 4. Process Data
    //this/these will be used a lot
    $profileSql = ProfileService::get('sql');
    $profileRedis = ProfileService::get('redis');
    $profileElastic = ProfileService::get('elastic');

    //save profile to database
    $results = $profileSql->update($data);

    //index profile
    $profileElastic->update($response->getResults('profile_id'));

    //invalidate cache
    $profileRedis->removeDetail($response->getResults('profile_id'));
    $profileRedis->removeSearch();

    //return response format
    $response->setError(false)->setResults($results);
});
