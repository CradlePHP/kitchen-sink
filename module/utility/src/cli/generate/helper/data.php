<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
$dataBuilder = function($schemas, $schema, $schemaRoot, $schemaName) {
    $data = include_once $schema;
    $data['name'] = $schemaName;

    if(isset($data['relations']) && is_array($data['relations'])) {
        foreach($data['relations'] as $name => $relation) {
            if($relation['many']) {
                $data['one-to-many'][$name] = $relation;
            } else {
                $data['one-to-one'][$name] = $relation;

                //because there are no schemas for this
                if($name === 'app') {
                    $data['json'][] = 'app_permissions';
                } else if($name === 'auth') {
                    $data['json'][] = 'auth_permissions';
                } else if($name === 'session') {
                    $data['json'][] = 'session_permissions';
                }
            }
        }
    }

    $normalizeField = include __DIR__ . '/field.php';

    foreach($data['fields'] as $name => $field) {
        $field['name'] = $name;
        $field = $normalizeField($field);

        if(isset($field['sql']['unique']) && $field['sql']['unique']) {
            $data['unique'][] = $name;
        }

        if(isset($field['sql']['type']) && $field['sql']['type'] === 'json') {
            $data['json'][] = $name;
        }

        if(isset($field['list']['searchable']) && $field['list']['searchable']) {
            $data['searchable'][] = $name;
        }

        if(isset($field['list']['sortable']) && $field['list']['sortable']) {
            $data['sortable'][] = $name;
        }

        if(isset($field['list']['filterable']) && $field['list']['filterable']) {
            $data['filterable'][] = $name;
        }

        if(isset($field['form']['type'])
            && (
                $field['form']['type'] === 'file'
                || $field['form']['type'] === 'image'
            )
        )
        {
            $data['has_file'] = true;
        } else if(isset($field['form']['inline_type'])
            && (
                $field['form']['inline_type'] === 'image-field'
                || $field['form']['inline_type'] === 'images-field'
            )
        )
        {
            $data['has_file'] = true;
        }

        $data['fields'][$name] = $field;
    }

    foreach($schemas as $metaSchema) {
        $path = $schemaRoot . '/' . $metaSchema . '.php';
        $metaData = include $path;

        if(isset($data['one-to-one'][$metaSchema])
            && isset($metaData['primary'])
            && !isset($data['one-to-one'][$metaSchema]['primary'])
        )
        {
            $data['one-to-one'][$metaSchema]['primary'] = $metaData['primary'];
        }

        if(!isset($metaData['fields']) || !is_array($metaData['fields'])) {
            continue;
        }

        foreach($metaData['fields'] as $name => $field) {
            $data['meta'][$name] = $field;

            if(isset($data['one-to-one'][$metaSchema]) && $field['sql']['type'] === 'json') {
                $data['json'][] = $name;
            }
        }
    }

    return $data;
};

return $dataBuilder($schemas, $schema, $schemaRoot, $schemaName);
