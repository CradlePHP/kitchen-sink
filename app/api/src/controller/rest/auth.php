<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Routes
 */
$cradle->post('/rest/access', function ($request, $response) {
    //set the profile id
    $profile = $request->get('source', 'profile_id');
    $request->setStage('permission', $profile);

    //call the job
    cradle()->trigger('session-access', $request, $response);
});
