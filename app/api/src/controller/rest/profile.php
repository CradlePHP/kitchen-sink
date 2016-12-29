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
$cradle->post('/rest/profile/update', function($request, $response) {
    $request->setStage('role', 'profile');
    cradle()->trigger('rest-permitted', $request, $response);

    if($response->isError()) {
        return;
    }

    //call the job
    cradle()->trigger('profile-update', $request, $response);
});
