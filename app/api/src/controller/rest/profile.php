<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Profile detail is accessable by all
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/profile/detail/:profile_id', 'profile-detail');

/**
 * Profile search is accessable by all
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/profile/search', 'profile-search');

/**
 * Profile update myself
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/rest/profile/update', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    $request->setStage('role', 'profile');
    cradle()->trigger('rest-permitted', $request, $response);

    if ($response->isError()) {
        return;
    }

    //----------------------------//
    // 2. Prepare Data
    //if profile_image has no value make it null
    if ($request->hasStage('profile_image') && !$request->getStage('profile_image')) {
        $request->setStage('profile_image', null);
    }

    //if profile_email has no value make it null
    if ($request->hasStage('profile_email') && !$request->getStage('profile_email')) {
        $request->setStage('profile_email', null);
    }

    //if profile_phone has no value make it null
    if ($request->hasStage('profile_phone') && !$request->getStage('profile_phone')) {
        $request->setStage('profile_phone', null);
    }

    //profile_slug is disallowed
    $request->removeStage('profile_slug');

    //if profile_detail has no value make it null
    if ($request->hasStage('profile_detail') && !$request->getStage('profile_detail')) {
        $request->setStage('profile_detail', null);
    }

    //if profile_job has no value make it null
    if ($request->hasStage('profile_job') && !$request->getStage('profile_job')) {
        $request->setStage('profile_job', null);
    }

    //if profile_gender has no value use the default value
    if ($request->hasStage('profile_gender') && !$request->getStage('profile_gender')) {
        $request->setStage('profile_gender', 'unknown');
    }

    //if profile_birth has no value make it null
    if ($request->hasStage('profile_birth') && !$request->getStage('profile_birth')) {
        $request->setStage('profile_birth', null);
    }

    //if profile_website has no value make it null
    if ($request->hasStage('profile_website') && !$request->getStage('profile_website')) {
        $request->setStage('profile_website', null);
    }

    //if profile_facebook has no value make it null
    if ($request->hasStage('profile_facebook') && !$request->getStage('profile_facebook')) {
        $request->setStage('profile_facebook', null);
    }

    //if profile_linkedin has no value make it null
    if ($request->hasStage('profile_linkedin') && !$request->getStage('profile_linkedin')) {
        $request->setStage('profile_linkedin', null);
    }

    //if profile_twitter has no value make it null
    if ($request->hasStage('profile_twitter') && !$request->getStage('profile_twitter')) {
        $request->setStage('profile_twitter', null);
    }

    //if profile_google has no value make it null
    if ($request->hasStage('profile_google') && !$request->getStage('profile_google')) {
        $request->setStage('profile_google', null);
    }

    //profile_type is disallowed
    $request->removeStage('profile_type');

    //profile_flag is disallowed
    $request->removeStage('profile_flag');

    //----------------------------//
    // 3. Process Request
    cradle()->trigger('profile-update', $request, $response);
});
