<?php //-->

$createSchmeaQueries = function($data, $database) {
    $queries = [];
    //determine the create schema
    $query = $database->getCreateQuery($data['name']);
    $query->addPrimaryKey($data['primary']);
    $query->addField($data['primary'], [
        'type' => 'int(10)',
        'null' => false,
        'attribute' => 'UNSIGNED',
        'auto_increment' => true,
    ]);

    if(isset($data['active'])) {
        $query->addKey($data['active'], [$data['active']]);
        $query->addField($data['active'], [
            'type' => 'int(1)',
            'null' => false,
            'default' => 1,
            'attribute' => 'UNSIGNED'
        ]);
    }

    if(isset($data['created'])) {
        $query->addKey($data['created'], [$data['created']]);
        $query->addField($data['created'], [
            'type' => 'datetime',
            'null' => false
        ]);
    }

    if(isset($data['updated'])) {
        $query->addKey($data['updated'], [$data['updated']]);
        $query->addField($data['updated'], [
            'type' => 'datetime',
            'null' => false
        ]);
    }

    foreach($data['fields'] as $name => $field) {
        if(!isset($field['sql'])) {
            continue;
        }

        $attributes = ['type' => $field['sql']['type']];

        if(isset($field['sql']['length'])) {
            $attributes['type'] .= '(' . $field['sql']['length'] . ')';
        }

        if(isset($field['sql']['default']) && trim($field['sql']['default'])) {
            $attributes['default'] = $field['sql']['default'];
        } else if(!isset($field['sql']['required']) || !$field['sql']['required']) {
            $attributes['null'] = true;
        }

        if(isset($field['sql']['required']) && $field['sql']['required']) {
            $attributes['null'] = false;
        }

        if(isset($field['sql']['attribute']) && $field['sql'][$name]['attribute']) {
            $attributes['attribute'] = $field['sql']['attribute'];
        }

        $query->addField($name, $attributes);

        if(isset($field['sql']['index']) && $field['sql']['index']) {
            $query->addKey($name, [$name]);
        }

        if(isset($field['sql']['unique']) && $field['sql']['unique']) {
            $query->addUniqueKey($name, [$name]);
        }

        if(isset($field['sql']['primary']) && $field['sql']['primary']) {
            $query->addPrimaryKey($name);
        }
    }

    $queries[] = 'DROP TABLE IF EXISTS `' . $data['name'] . '`;';
    $queries[] = (string) $query;

    if(isset($data['relations'])) {
        //determine the relation schema
        foreach($data['relations'] as $name => $relation) {
            $query = $database->getCreateQuery($data['name'] . '_' . $name);

            $query->addPrimaryKey($data['primary']);
            $query->addField($data['primary'], [
                'type' => 'int(10)',
                'null' => false,
                'attribute' => 'UNSIGNED'
            ]);

            $query->addPrimaryKey($relation['primary']);
            $query->addField($relation['primary'], [
                'type' => 'int(10)',
                'null' => false,
                'attribute' => 'UNSIGNED'
            ]);

            $queries[] = 'DROP TABLE IF EXISTS `'. $data['name'] . '_' . $name . '`;';
            $queries[] = (string) $query;
        }
    }

    return $queries;
};

return $createSchmeaQueries($data, $database);
