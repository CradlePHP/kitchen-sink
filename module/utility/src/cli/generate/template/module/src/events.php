<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

{{#if service}}
{{~#each service}}
use Cradle\Module\{{camel this 1}}\Service as {{camel this 1}}Service;
{{/each~}}
{{/if}}

{{#if validator}}
{{~#each validator}}
use Cradle\Module\{{camel this 1}}\Validator as {{camel this 1}}Validator;
{{/each~}}
{{/if}}

use Cradle\Http\Request;
use Cradle\Http\Response;

{{#if events}}
{{#each events}}
/**
 * {{../name}} {{@key}}
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('{{../name}}-{{@key}}', function ($request, $response) {
    //get data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    {{#each instructions}}
        {{#when this.0 '===' 'validate'}}

    //validate
    $errors = {{camel this.2 1}}Validator::get{{capital this.1}}Errors($data);

    //if there are errors
    if (!empty($errors)) {
        return $response
            ->setError(true, 'Invalid Parameters')
            ->set('json', 'validation', $errors);
    }

        {{/when}}
    {{/each}}

    // this/these will be used alot
    {{#each service.sql}}
    ${{this}}Sql = {{camel this 1}}Service::get('sql');
    {{/each}}

    {{#each service.elastic}}
    ${{this}}Elastic = {{camel this 1}}Service::get('elastic');
    {{/each}}

    {{#each service.redis}}
    ${{this}}Redis = {{camel this 1}}Service::get('redis');
    {{/each}}

    {{#each instructions}}
        {{#when this.0 '===' 'sql'}}
            {{#when this.1 '===' 'create'}}
    //create sql
    $results = ${{this.2}}Sql->create($data);
            {{/when}}

            {{#when this.1 '===' 'get'}}
    //get sql
    $results = ${{this.2}}Sql->get($data['{{../../primary}}']);
            {{/when}}

            {{#when this.1 '===' 'remove'}}
                {{#if ../../active}}
    //update sql
    $results = ${{this.2}}Sql->update([
        '{{../../primary}}' => $data['{{../../primary}}'],
        'profile_active' => 0
    ]);
                {{else}}
    //remove sql
    $results = ${{this.2}}Sql->remove($data['{{../../primary}}']);
                {{/if}}
            {{/when}}

            {{#when this.1 '===' 'restore'}}
                {{#if ../../active}}
    //update sql
    $results = ${{this.2}}Sql->update([
        '{{../../primary}}' => $data['{{../../primary}}'],
        'profile_active' => 1
    ]);
                {{/if}}
            {{/when}}

            {{#when this.1 '===' 'search'}}
    //search sql
    $results = ${{this.2}}Sql->search($data);
            {{/when}}

            {{#when this.1 '===' 'update'}}
    //update sql
    $results = ${{this.2}}Sql->update($data);
            {{/when}}

            {{#when this.1 '===' 'link'}}
    if(isset($data['{{this.3}}'])) {
        //link item to {{this.2}}
        ${{../../name}}Sql->link{{camel this.2 1}}($results['{{../../primary}}'], $data['{{this.3}}']);
    }
            {{/when}}

            {{#when this.1 '===' 'unlink'}}
    if(isset($data['{{this.3}}'])) {
        //unlink item from {{this.2}}
        ${{../../name}}Sql->unlink{{camel this.2 1}}($results['{{../../primary}}'], $data['{{this.3}}']);
    }
            {{/when}}

            {{#when this.1 '===' 'linkAll'}}
    //unlink item from {{this.2}}
    ${{../../name}}Sql->linkAll{{camel this.2 1}}($results['{{../../primary}}']);
            {{/when}}

            {{#when this.1 '===' 'unlinkAll'}}
    //unlink all items from {{this.2}}
    ${{../../name}}Sql->unlinkAll{{camel this.2 1}}($results['{{../../primary}}']);
            {{/when}}
        {{/when}}

        {{#when this.0 '===' 'elastic'}}
            {{#when this.1 '===' 'create'}}
    //create elastic
    $results = ${{this.2}}Elastic->create($data['{{../../primary}}']);
            {{/when}}

            {{#when this.1 '===' 'get'}}
    //get elastic
    $results = ${{this.2}}Elastic->get($data['{{../../primary}}']);
            {{/when}}

            {{#when this.1 '===' 'remove'}}
    //remove elastic
    $results = ${{this.2}}Elastic->remove($data['{{../../primary}}']);
            {{/when}}

            {{#when this.1 '===' 'search'}}
    //search elastic
    $results = ${{this.2}}Sql->search($data);
            {{/when}}

            {{#when this.1 '===' 'update'}}
    //update elastic
    $results = ${{this.2}}Sql->update($data['{{../../primary}}']);
            {{/when}}
        {{/when}}

        {{#when this.0 '===' 'redis'}}
            {{#when this.1 '===' 'createDetail'}}
    //create detail in redis
    ${{this.2}}Redis->createDetail($results['{{this.3}}'], $results);
            {{/when}}

            {{#when this.1 '===' 'createSearch'}}
    //create search in redis
    ${{this.2}}Redis->createSearch($data, $results);
            {{/when}}

            {{#when this.1 '===' 'getDetail'}}
    //get detail from redis
    $results = ${{this.2}}Redis->getDetail($data['{{this.3}}']);
            {{/when}}

            {{#when this.1 '===' 'getSearch'}}
    //get search from redis
    $results = ${{this.2}}Redis->getSearch($data);
            {{/when}}

            {{#when this.1 '===' 'removeDetail'}}
    //remove detail from redis
    ${{this.2}}Redis->removeDetail($data['{{this.3}}']);
            {{/when}}

            {{#when this.1 '===' 'removeSearch'}}
    //remove search from redis
    ${{this.2}}Redis->removeSearch($data);
            {{/when}}
        {{/when}}

        {{#when this.0 '===' 'get-detail'}}

    $id = null;
    {{#if ../../unique~}}
    if (isset($data['{{../../primary}}'])) {
        $id = $data['{{../../primary}}'];
    } else if (isset($data['{{../../unique}}'])) {
        $id = $data['{{../../unique}}'];
    }
    {{~else~}}
    if (isset($data['{{../../primary}}'])) {
        $id = $data['{{../../primary}}'];
    }
    {{~/if}}

    //we need an id
    if (!$id) {
        return $response->setError(true, 'Invalid ID');
    }

    $results = null;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = ${{../../name}}Redis->getDetail($id);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = ${{../../name}}Elastic->get($id);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = ${{../../name}}Sql->get($id);
        }

        if ($results) {
            //cache it from database or index
            ${{../../name}}Redis->createDetail($id, $results);
        }
    }

    if (!$results) {
        return $response->setError(true, 'Not Found');
    }

        {{/when}}

        {{#when this.0 '===' 'get-search'}}

    $results = false;

    //if no flag
    if (!$request->hasGet('nocache')) {
        //get it from cache
        $results = ${{../../name}}Redis->getSearch($data);
    }

    //if no results
    if (!$results) {
        //if no flag
        if (!$request->hasGet('noindex')) {
            //get it from index
            $results = ${{../../name}}Elastic->search($data);
        }

        //if no results
        if (!$results) {
            //get it from database
            $results = ${{../../name}}Sql->search($data);
        }

        if ($results) {
            //cache it from database or index
            ${{../../name}}Redis->createSearch($data, $results);
        }
    }
        {{/when}}

        {{#when this.0 '===' 'permissions'}}
    //if permission is provided
    $permission = $request->getStage('permission');
    if ($permission
        && isset($results['profile_id'])
        && $results['profile_id'] != $permission
    )
    {
        return $response->setError(true, 'Invalid Permissions');
    }
        {{/when}}

        {{#when this.0 '===' 'detail'}}
    //get the {{../../name}} detail
    $this->trigger('{{../../name}}-detail', $request, $response);
        {{/when}}
    {{/each}}

    //return response format
    $response->setError(false)->setResults($results);
});
{{/each}}
{{/if}}
