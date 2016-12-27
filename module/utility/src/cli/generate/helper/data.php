<?php //-->

$dataBuilder = function() use ($schemas, $schema, $schemaRoot, $schemaName, $appName) {
    $data = include_once $schema;
    $data['name'] = $schemaName;
    $data['app'] = $appName;

    if(isset($data['relations']) && is_array($data['relations'])) {
        foreach($data['relations'] as $name => $relation) {
            if($relation['many']) {
                $data['one-to-many'][$name] = $relation;
            } else {
                $data['one-to-one'][$name] = $relation;
            }
        }
    }

    foreach($data['fields'] as $name => $field) {
        if(isset($field['unique']) && $field['unique']) {
            $data['unique'][] = $name;
        }

        if(isset($field['searchable']) && $field['searchable']) {
            $data['searchable'][] = $name;
        }

        if(isset($field['sortable']) && $field['sortable']) {
            $data['sortable'][] = $name;
        }
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

            if(isset($data['one-to-one'][$metaSchema]) && $field['type'] === 'json') {
                $data['json'][] = $name;
            }
        }
    }


    if(isset($data['events']) && is_array($data['events'])) {
        $data['validator'] = [];
        $data['service'] = [];
        foreach($data['events'] as $name => $instructions) {
            $data['events'][$name] = [
                'instructions' => $instructions,
                'service' => [
                    'sql' => [],
                    'elastic' => [],
                    'redis' => [],
                ]
            ];

            foreach($instructions as $instruction) {
                if($instruction[0] === 'validate') {
                    $data['validator'][] = $instruction[2];
                    continue;
                }

                if($instruction[0] === 'sql') {
                    $data['events'][$name]['service']['sql'] = $instruction[2];
                    $data['service'][] = $instruction[2];
                    continue;
                }

                if($instruction[0] === 'elastic') {
                    $data['events'][$name]['service']['elastic'] = $instruction[2];
                    $data['service'][] = $instruction[2];
                    continue;
                }

                if($instruction[0] === 'redis') {
                    $data['events'][$name]['service']['redis'] = $instruction[2];
                    $data['service'][] = $instruction[2];
                    continue;
                }

                if($instruction[0] === 'get-detail' || $instruction[0] === 'get-search') {
                    $data['events'][$name]['service']['sql'] = $data['name'];
                    $data['events'][$name]['service']['elastic'] = $data['name'];
                    $data['events'][$name]['service']['redis'] = $data['name'];
                    $data['service'][] = $data['name'];
                    continue;
                }
            }

            $data['events'][$name]['service']['sql'] = array_unique($data['events'][$name]['service']['sql']);
            $data['events'][$name]['service']['elastic'] = array_unique($data['events'][$name]['service']['elastic']);
            $data['events'][$name]['service']['redis'] = array_unique($data['events'][$name]['service']['redis']);
        }

        $data['validator'] = array_unique($data['validator']);
        $data['service'] = array_unique($data['service']);
    }

    return $data;
};

return $dataBuilder();
