<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Module\{{camel name 1}}\Service as {{camel name 1}}Service;
use Cradle\Module\{{camel name 1}}\Validator as {{camel name 1}}Validator;

use Cradle\Http\Request;
use Cradle\Http\Response;

{{~#if has_file}}

use Cradle\Module\Utility\File;
{{~/if}}

/**
 * {{camel name 1}} Create Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('{{name}}-create', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    ${{name}}Sql = {{camel name 1}}Service::get('sql');
    ${{name}}Redis = {{camel name 1}}Service::get('redis');
    ${{name}}Elastic = {{camel name 1}}Service::get('elastic');

    //validate
    $errors = {{camel name 1}}Validator::getCreateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //prepare data
    {{~#each fields}}
        {{~#when sql.type '===' 'json'}}

    if($data['{{@key}}']) {
        $data['{{@key}}'] = json_encode($data['{{@key}}']);
    }
        {{~/when}}

        {{~#when sql.type '===' 'date'}}

        if($data['{{@key}}']) {
            $data['{{@key}}'] = date('Y-m-d', strtotime($data['{{@key}}']));
        }
        {{~/when}}

        {{~#when sql.type '===' 'time'}}
    if($data['{{@key}}']) {
        $data['{{@key}}'] = date('H:i:s', strtotime($data['{{@key}}']));
    }
        {{~/when}}

        {{~#when sql.type '===' 'datetime'}}

    if($data['{{@key}}']) {
        $data['{{@key}}'] = date('Y-m-d H:i:s', strtotime($data['{{@key}}']));
    }
        {{~/when}}

        {{~#when field.type '===' 'image-field'}}

    //if there is an image
    if (isset($data['{{@key}}'])) {
        //upload files
        //try cdn if enabled
        $config = $this->package('global')->service('s3-main');
        $data['{{@key}}'] = File::base64ToS3($data['{{@key}}'], $config);
        //try being old school
        $upload = $this->package('global')->path('upload');
        $data['{{@key}}'] = File::base64ToUpload($data['{{@key}}'], $upload);
    }
        {{~/when}}

        {{~#when field.type '===' 'images-field'}}

    //if there is an image
    if (isset($data['{{@key}}'])) {
        //upload files
        //try cdn if enabled
        $config = $this->package('global')->service('s3-main');
        $data['{{@key}}'] = File::base64ToS3($data['{{@key}}'], $config);
        //try being old school
        $upload = $this->package('global')->path('upload');
        $data['{{@key}}'] = File::base64ToUpload($data['{{@key}}'], $upload);
    }
        {{~/when}}
    {{~/each}}

    //save {{name}} to database
    $results = ${{name}}Sql->create($data);

    {{~#each relations}}
    //link {{@key}}
    if(isset($data['{{primary}}'])) {
        ${{name}}Sql->link{{camel @key 1}}($results['{{../primary}}'], $data['{{primary}}']);
    }
    {{~/each}}

    //index {{name}}
    ${{name}}Elastic->create($results['{{primary}}']);

    //invalidate cache
    ${{name}}Redis->removeSearch();

    //return response format
    $response->setError(false)->setResults($results);
});

/**
 * {{camel name 1}} Detail Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('{{name}}-detail', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    $id = null;
    if (isset($data['{{primary}}'])) {
        $id = $data['{{primary}}'];
    {{#if unique.length}}{{#each unique~}}
    } else if (isset($data['{{this}}'])) {
        $id = $data['{{this}}'];
    {{/each}}{{/if~}}
    }

    //we need an id
    if (!$id) {
        return $response->setError(true, 'Invalid ID');
    }

    //this/these will be used a lot
    ${{name}}Sql = {{camel name 1}}Service::get('sql');
    ${{name}}Redis = {{camel name 1}}Service::get('redis');
    ${{name}}Elastic = {{camel name 1}}Service::get('elastic');

    $results = null;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = ${{name}}Redis->getDetail($id);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = ${{name}}Elastic->get($id);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = ${{name}}Sql->get($id);
        }

        if ($results) {
            //cache it from database or index
            ${{name}}Redis->createDetail($id, $results);
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
 * {{camel name 1}} Remove Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('{{name}}-remove', function ($request, $response) {
    //get the {{name}} detail
    $this->trigger('{{name}}-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    ${{name}}Sql = {{camel name 1}}Service::get('sql');
    ${{name}}Redis = {{camel name 1}}Service::get('redis');
    ${{name}}Elastic = {{camel name 1}}Service::get('elastic');

    {{~#if active}}

    //save to database
    $results = ${{name}}Sql->update([
        '{{primary}}' => $data['{{primary}}'],
        '{{active}}' => 0
    ]);
    {{~else}}

    //remove from database
    $results = ${{name}}Sql->remove($data['{{primary}}']);
    {{~/if}}

    //remove from index
    ${{name}}Elastic->remove($data['{{primary}}']);

    //invalidate cache
    ${{name}}Redis->removeDetail($data['{{primary}}']);
    {{#if unique.length}}{{#each unique~}}
    ${{../name}}Redis->removeDetail($data['{{this}}']);
    {{/each}}{{/if~}}

    ${{name}}Redis->removeSearch();

    $response->setError(false)->setResults($results);
});

{{~#if active}}

/**
 * {{camel name 1}} Restore Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('{{name}}-restore', function ($request, $response) {
    //get the {{name}} detail
    $this->trigger('{{name}}-detail', $request, $response);

    //if there's an error
    if ($response->isError()) {
        return;
    }

    //get data
    $data = $response->getResults();

    //this/these will be used a lot
    ${{name}}Sql = {{camel name 1}}Service::get('sql');
    ${{name}}Redis = {{camel name 1}}Service::get('redis');
    ${{name}}Elastic = {{camel name 1}}Service::get('elastic');

    //save to database
    $results = ${{name}}Sql->update([
        '{{primary}}' => $data['{{primary}}'],
        '{{active}}' => 1
    ]);

    //create index
    ${{name}}Elastic->create($data['{{primary}}']);

    //invalidate cache
    ${{name}}Redis->removeSearch();

    $response->setError(false)->setResults($results);
});
{{~/if}}

/**
 * {{camel name 1}} Search Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('{{name}}-search', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //this/these will be used a lot
    ${{name}}Sql = {{camel name 1}}Service::get('sql');
    ${{name}}Redis = {{camel name 1}}Service::get('redis');
    ${{name}}Elastic = {{camel name 1}}Service::get('elastic');

    $results = false;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = ${{name}}Redis->getSearch($data);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = ${{name}}Elastic->search($data);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = ${{name}}Sql->search($data);
        }

        if ($results) {
            //cache it from database or index
            ${{name}}Redis->createSearch($data, $results);
        }
    }

    //set response format
    $response->setError(false)->setResults($results);
});

/**
 * {{camel name 1}} Update Job
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('{{name}}-update', function ($request, $response) {
    //get the {{name}} detail
    $this->trigger('{{name}}-detail', $request, $response);

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
    ${{name}}Sql = {{camel name 1}}Service::get('sql');
    ${{name}}Redis = {{camel name 1}}Service::get('redis');
    ${{name}}Elastic = {{camel name 1}}Service::get('elastic');

    //validate
    $errors = {{camel name 1}}Validator::getUpdateErrors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

    //prepare data
    {{~#each fields}}
        {{~#when sql.type '===' 'json'}}

    if($data['{{@key}}']) {
        $data['{{@key}}'] = json_encode($data['{{@key}}']);
    }
        {{~/when}}

        {{~#when sql.type '===' 'date'}}

        if($data['{{@key}}']) {
            $data['{{@key}}'] = date('Y-m-d', strtotime($data['{{@key}}']));
        }
        {{~/when}}

        {{~#when sql.type '===' 'time'}}
    if($data['{{@key}}']) {
        $data['{{@key}}'] = date('H:i:s', strtotime($data['{{@key}}']));
    }
        {{~/when}}

        {{~#when sql.type '===' 'datetime'}}

    if($data['{{@key}}']) {
        $data['{{@key}}'] = date('Y-m-d H:i:s', strtotime($data['{{@key}}']));
    }
        {{~/when}}

        {{~#when field.type '===' 'image-field'}}

    //if there is an image
    if (isset($data['{{@key}}'])) {
        //upload files
        //try cdn if enabled
        $config = $this->package('global')->service('s3-main');
        $data['{{@key}}'] = File::base64ToS3($data['{{@key}}'], $config);
        //try being old school
        $upload = $this->package('global')->path('upload');
        $data['{{@key}}'] = File::base64ToUpload($data['{{@key}}'], $upload);
    }
        {{~/when}}

        {{~#when field.type '===' 'images-field'}}

    //if there is an image
    if (isset($data['{{@key}}'])) {
        //upload files
        //try cdn if enabled
        $config = $this->package('global')->service('s3-main');
        $data['{{@key}}'] = File::base64ToS3($data['{{@key}}'], $config);
        //try being old school
        $upload = $this->package('global')->path('upload');
        $data['{{@key}}'] = File::base64ToUpload($data['{{@key}}'], $upload);
    }
        {{~/when}}
    {{~/each}}

    //save {{name}} to database
    $results = ${{name}}Sql->update($data);

    //index {{name}}
    ${{name}}Elastic->update($response->getResults('{{primary}}'));

    //invalidate cache
    ${{name}}Redis->removeDetail($response->getResults('{{primary}}'));
    {{#if unique.length}}{{#each unique~}}
    ${{../name}}Redis->removeDetail($data['{{this}}']);
    {{/each}}{{/if~}}
    ${{name}}Redis->removeSearch();

    //return response format
    $response->setError(false)->setResults($results);
});
