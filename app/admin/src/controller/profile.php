<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\Utility\File;

/**
 * Render the Profile Search Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/profile/search', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
    if (!$request->hasStage('range')) {
        $request->setStage('range', 50);
    }

    //filter possible sorting options
    //we do this to prevent SQL injections
    if (is_array($request->getStage('order'))) {
        $sortable = [
            'profile_name'
        ];

        foreach ($request->getStage('order') as $key => $direction) {
            if (!in_array($key, $sortable)) {
                $request->removeStage('order', $key);
            } else if ($direction !== 'ASC' && $direction !== 'DESC') {
                $request->removeStage('order', $key);
            }
        }
    }

    //trigger job
    cradle()->trigger('profile-search', $request, $response);
    $data = array_merge($request->getStage(), $response->getResults());

    //----------------------------//
    // 3. Render Template
    $class = 'page-admin-profile-search page-admin';
    $data['title'] = cradle('global')->translate('Profiles');
    $body = cradle('/app/admin')->template('profile/search', $data);

    //set content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);

    //render page
}, 'render-admin-page');

/**
 * Render the Profile Create Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/profile/create', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
    $data = ['item' => $request->getPost()];

    //add CDN
    $config = $this->package('global')->service('s3-main');
    $data['cdn_config'] = File::getS3Client($config);

    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //----------------------------//
    // 3. Render Template
    $class = 'page-developer-profile-create page-admin';
    $data['title'] = cradle('global')->translate('Create Profile');
    $body = cradle('/app/admin')->template('profile/form', $data);

    //set content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);

    //render page
}, 'render-admin-page');

/**
 * Render the Profile Update Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/profile/update/:profile_id', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
    $data = ['item' => $request->getPost()];

    //if no item
    if (empty($data['item'])) {
        cradle()->trigger('profile-detail', $request, $response);

        //can we update ?
        if ($response->isError()) {
            //add a flash
            cradle('global')->flash($response->getMessage(), 'danger');
            return cradle('global')->redirect('/admin/profile/search');
        }

        $data['item'] = $response->getResults();
    }

    if ($response->isError()) {
        $response->setFlash($response->getMessage(), 'danger');
        $data['errors'] = $response->getValidation();
    }

    //----------------------------//
    // 3. Render Template
    $class = 'page-developer-profile-update page-admin';
    $data['title'] = cradle('global')->translate('Updating Profile');
    $body = cradle('/app/admin')->template('profile/form', $data);

    //Set Content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);

    //Render page
}, 'render-admin-page');

/**
 * Process the Profile Create Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/admin/profile/create', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

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
    cradle()->trigger('profile-create', $request, $response);

    //----------------------------//
    // 4. Interpret Results
    if ($response->isError()) {
        return cradle()->triggerRoute('get', '/admin/profile/create', $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('Profile was Created', 'success');

    //redirect
    cradle('global')->redirect('/admin/profile/search');
});

/**
 * Process the Profile Update Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->post('/admin/profile/update/:profile_id', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

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

    //----------------------------//
    // 4. Interpret Results
    if ($response->isError()) {
        $route = '/admin/profile/update/' . $request->getStage('profile_id');
        return cradle()->triggerRoute('get', $route, $request, $response);
    }

    //it was good
    //add a flash
    cradle('global')->flash('Profile was Updated', 'success');

    //redirect
    cradle('global')->redirect('/admin/profile/search');
});

/**
 * Process the Profile Remove
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/profile/remove/:profile_id', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
    // no data to preapre
    //----------------------------//
    // 3. Process Request
    cradle()->trigger('profile-remove', $request, $response);

    //----------------------------//
    // 4. Interpret Results
    if ($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('Profile was Removed');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('/admin/profile/search');
});

/**
 * Process the Profile Restore
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/admin/profile/restore/:profile_id', function ($request, $response) {
    //----------------------------//
    // 1. Route Permissions
    //only for admin
    cradle('global')->requireLogin('admin');

    //----------------------------//
    // 2. Prepare Data
    // no data to preapre
    //----------------------------//
    // 3. Process Request
    cradle()->trigger('profile-restore', $request, $response);

    //----------------------------//
    // 4. Interpret Results
    if ($response->isError()) {
        //add a flash
        cradle('global')->flash($response->getMessage(), 'danger');
    } else {
        //add a flash
        $message = cradle('global')->translate('Profile was Restored');
        cradle('global')->flash($message, 'success');
    }

    cradle('global')->redirect('/admin/profile/search');
});
