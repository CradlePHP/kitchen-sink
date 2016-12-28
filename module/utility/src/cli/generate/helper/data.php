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

    $normalizeField = include __DIR__ . '/field.php';

    foreach($data['fields'] as $name => $field) {
        $field = $normalizeField($field);

        if($field['unique']) {
            $data['unique'][] = $name;
        }

        if($field['searchable']) {
            $data['searchable'][] = $name;
        }

        if($field['sortable']) {
            $data['sortable'][] = $name;
        }

        $field['name'] = $name;
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

            if(isset($data['one-to-one'][$metaSchema]) && $field['type'] === 'json') {
                $data['json'][] = $name;
            }
        }
    }

    return $data;
};

return $dataBuilder();
