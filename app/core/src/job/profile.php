<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\App\Core\File;
use Aws\S3\S3Client;
use Aws\S3\PostObjectV4;
use Cradle\Image\ImageHandler;

/**
 * Profile Create Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('profile-create', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    if (!isset($data['profile_image'])) {
        //generate image
        $protocol = 'http';
        if ($request->getServer('SERVER_PORT') === 443) {
            $protocol = 'https';
        }

        $host = $protocol . '://' . $request->getServer('HTTP_HOST');

        $data['profile_image'] = $host . '/images/avatar/avatar-'
            . ((floor(rand() * 1000) % 11) + 1) . '.png';
    }

    //this/these will be used a lot
    $profileModel = $this->package('/app/core')->model('profile');

    //validate
    $errors = $profileModel->getCreateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //if there is an image
    if($request->hasStage('profile_image')) {
        //upload files
        //try cdn if enabled
        $this->trigger('profile-image-base64-cdn', $request, $response);
        //try being old school
        $this->trigger('profile-image-base64-upload', $request, $response);

        $data['profile_image'] = $request->getStage('profile_image');
    }

    //save profile to database
    $results = $profileModel->databaseCreate($data);

    //index profile
    $profileModel->indexCreate($results['profile_id']);

    //invalidate cache
    $profileModel->cacheRemoveSearch();

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
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    $id = null;
    if (isset($data['profile_id'])) {
        $id = $data['profile_id'];
    } else if (isset($data['profile_slug']) && $data['profile_slug']) {
        $id = $data['profile_slug'];
    }

    //we need an id
    if (!$id) {
        return $response->setError(true, 'Invalid ID');
    }

    //this/these will be used a lot
    $profileModel = $this->package('/app/core')->model('profile');

    //get it from cache
    $results = $profileModel->cacheDetail($id);

    //if no results
    if(!$results) {
        //get it from index
        $results = $profileModel->indexDetail($id);

        //if no results
        if(!$results) {
            //get it from database
            $results = $profileModel->databaseDetail($id);
        }

        if($results) {
            //cache it from database or index
            $profileModel->cacheCreateDetail($id, $results);
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

    $response->setError(false)->setResults($results);
});

/**
* Profile Remove Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('profile-remove', function ($request, $response) {
    //get the profile detail
    $this->trigger('profile-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $profileModel = $this->package('/app/core')->model('profile');

    //save to database
    $results = $profileModel->databaseUpdate([
        'profile_id' => $data['profile_id'],
        'profile_active' => 0
    ]);

    //remove from index
    $profileModel->indexRemove($id);

    //invalidate cache
    $profileModel->cacheRemoveDetail($data['profile_id']);
    $profileModel->cacheRemoveDetail($data['profile_slug']);
    $profileModel->cacheRemoveSearch();

    $response->setError(false)->setResults($results);
});

/**
* Profile Restore Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('profile-restore', function ($request, $response) {
    //get the profile detail
    $this->trigger('profile-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    $profileModel = $this->package('/app/core')->model('profile');

    //save to database
    $results = $profileModel->databaseUpdate([
        'profile_id' => $data['profile_id'],
        'profile_active' => 1
    ]);

    //create index
    $profileModel->indexCreate($id);

    //invalidate cache
    $profileModel->cacheRemoveSearch();

    $response->setError(false)->setResults($id);
});

/**
* Profile Search Job
*
* @param Request $request
* @param Response $response
*/
$cradle->on('profile-search', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    $profileModel = $this->package('/app/core')->model('profile');

    //get it from cache
    $results = $profileModel->cacheSearch($data);

    //if no results
    if(!$results) {
        //get it from index
        $results = $profileModel->indexSearch($data);

        //if no results
        if(!$results) {
            //get it from database
            $results = $profileModel->databaseSearch($data);
        }

        //cache it from database or index
        $profileModel->cacheCreateSearch($data, $results);
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
    //get the profile detail
    $this->trigger('profile-detail', $request, $response);

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
    $profileModel = $this->package('/app/core')->model('profile');

    //validate
    $errors = $profileModel->getUpdateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //if there is an image
    if($request->hasStage('profile_image')) {
        //upload files
        //try cdn if enabled
        $this->trigger('profile-image-base64-cdn', $request, $response);
        //try being old school
        $this->trigger('profile-image-base64-upload', $request, $response);

        $data['profile_image'] = $request->getStage('profile_image');
    }

    //save profile to database
    $results = $profileModel->databaseUpdate($data);

    //index profile
    $profileModel->indexUpdate($response->getResults('profile_id'));

    //invalidate cache
    $profileModel->cacheRemoveDetail($response->getResults('profile_id'));
    $profileModel->cacheRemoveDetail($response->getResuts('profile_slug'));
    $profileModel->cacheRemoveSearch();

    //return response format
    $response->setError(false)->setResults($results);
});

/**
* File Base64 Upload Job (supporting job)
*
* @param Request $request
* @param Response $response
*/
$cradle->on('profile-image-base64-upload', function($request, $response) {
    $data = $request->getStage('profile_image');

    //if not base 64
    if (strpos($data, ';base64,') === false) {
        //we don't need to convert
        return;
    }

    //first get the destination
    $destination = $this->package('global')->path('upload');

    //if not
    if (!is_dir($destination)) {
        //make one
        mkdir($destination);
    }

    //this is the root for file_link
    $protocol = 'http';
    if ($request->getServer('SERVER_PORT') === 443) {
        $protocol = 'https';
    }

    $host = $protocol . '://' . $request->getServer('HTTP_HOST');
    $extension = File::getExtensionFromData($data);

    $file = '/' . md5(uniqid()) . '.' . $extension;

    $path = $destination . $file;
    $link = $host . '/upload' . $file;

    //data:mime;base64,data
    $base64 = substr($data, strpos($data, ',') + 1);
    file_put_contents($path, base64_decode($base64));

    //now put it back
    $request->setStage('profile_image', $link);
});

/**
* Upload Base64 images to CDN (supporting job)
*
* @param Request $request
* @param Response $response
*/
$cradle->on('profile-image-base64-cdn', function($request, $response) {
    //profile_image can be a link or base64
    $data = $request->getStage('profile_image');

    //if not base 64
    if (strpos($data, ';base64,') === false) {
        //we don't need to convert
        return;
    }

    $config = $this->package('global')->service('cdn-main');

    //if there's no service
    if(!$config) {
        //we cannot continue
        return;
    }

    // load s3
    $s3 = S3Client::factory([
        'version' => 'latest',
        'region'  => $config['region'], //example ap-southeast-1
        'credentials' => array(
            'key'    => $config['token'],
            'secret' => $config['secret'],
        )
    ]);

    $mime = File::getMimeFromData($data);
    $extension = File::getExtensionFromData($data);
    $file = md5(uniqid()) . '.' . $extension;
    $base64 = substr($data, strpos($data, ',') + 1);
    $body = fopen('data://' . $mime . ';base64,' . $base64, 'r');

    $s3->putObject([
        'Bucket'         => $config['bucket'],
        'ACL'            => 'public-read',
        'ContentType'    => $mime,
        'Key'            => 'upload/' . $file,
        'Body'           => $body,
        'CacheControl'   => 'max-age=43200'
    ]);

    fclose($body);

    $link = $config['host'] . '/' . $config['bucket'] . '/upload/' . $file;
    $request->setStage('profile_image', $link);
});

/**
* Upload images to CDN from client (supporting job)
*
* @param Request $request
* @param Response $response
*/
$cradle->on('profile-image-client-cdn', function($request, $response) {
    $config = $this->package('global')->service('cdn-main');

    //if there's no service
    if(!$config) {
        //we cannot continue
        return;
    }

    // load s3
    $s3 = S3Client::factory([
        'version' => 'latest',
        'region'  => $config['region'], //example ap-southeast-1
        'credentials' => array(
            'key'    => $config['token'],
            'secret' => $config['secret'],
        )
    ]);

    $postObject = new PostObjectV4(
        $s3,
        $config['bucket'],
        [
            'acl' => 'public-read',
            'key' => 'upload/' . md5(uniqid())
        ],
        [
            ['acl' => 'public-read'],
            ['bucket' => $config['bucket']],
            ['starts-with', '$key', 'upload/']
        ],
        '+2 hours'
    );

    $response->setResults('cdn', [
        // Get attributes to set on an HTML form, e.g., action, method, enctype
        'form' => $postObject->getFormAttributes(),
        // Get form input fields. This will include anything set as a form input in
        // the constructor, the provided JSON policy, your AWS Access Key ID, and an
        // auth signature.
        'inputs' => $postObject->getFormInputs()
    ]);
});
