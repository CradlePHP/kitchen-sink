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
$cradle->get('/rest/profile/update', function($request, $response) {
    $request->setStage('role', 'personal_profile');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    $profile = $request->get('source', 'profile_id');
    $request->setStage('profile_id', $profile);
    $request->setStage('permission', $profile);

    //call the job
    cradle()->trigger('profile-update', $request, $response);
});

/**
 * Profile remove only if i have user_profile
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/rest/user/profile/update/:profile_id', function($request, $response) {
    $request->setStage('role', 'user_profile');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    //set the profile id
    $profile = $request->get('source', 'profile_id');
    $request->setStage('permission', $profile);

    //call the job
    cradle()->trigger('profile-update', $request, $response);
});
