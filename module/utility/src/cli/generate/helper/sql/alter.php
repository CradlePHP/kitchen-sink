<?php //-->

$createAlterQueries = function($data, $database) {
    $queries = [];
    $columns = $database->getColumns($data['name']);
    $query = $database->getAlterQuery($data['name']);

    $primary = false;
    $active = false;
    $created = false;
    $updated = false;
    $exists = [];

    foreach($columns as $column) {
        //don't do primary
        if($data['primary'] === $column['Field']) {
            $primary = true;
            continue;
        }

        //don't do active
        if($data['active'] === $column['Field']) {
            $active = true;
            continue;
        }

        //don't do created
        if($data['created'] === $column['Field']) {
            $created = true;
            continue;
        }

        //don't do updated
        if($data['updated'] === $column['Field']) {
            $updated = true;
            continue;
        }

        $exists[] = $name = $column['Field'];

        //if there is no field in the schema
        if(!isset($data['fields'][$name]['sql']['type'])) {
            $query->removeField($name);
            continue;
        }

        $field = $data['fields'][$name];

        $attributes = ['type' => $field['sql']['type']];

        if(isset($field['sql']['length'])) {
            $attributes['type'] .= '(' . $field['sql']['length'] . ')';
        }

        $attributes['null'] = true;
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

        $default = null;
        if (isset($attributes['default'])) {
            $default = $attributes['default'];
        }

        //if all matches
        if($attributes['type'] === $column['Type']
            && $attributes['null'] == ($column['Null'] === 'YES')
            && $default === $column['Default']
        ) {
            continue;
        }

        //do the alter
        $query->changeField($name, $attributes);
    }

    foreach($data['fields'] as $name => $field) {
        if(!isset($field['sql']) || in_array($name, $exists)) {
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

    if(!$primary && $data['primary']) {
        $query->addPrimaryKey($data['primary']);
        $query->addField($data['primary'], [
            'type' => 'int(10)',
            'null' => false,
            'attribute' => 'UNSIGNED',
            'auto_increment' => true,
        ]);
    }

    if(!$active && $data['active']) {
        $query->addKey($data['active'], [$data['active']]);
        $query->addField($schema['active'], [
            'type' => 'int(1)',
            'null' => false,
            'default' => 1,
            'attribute' => 'UNSIGNED'
        ]);
    }

    if(!$created && $data['created']) {
        $query->addKey($data['created'], [$data['created']]);
        $query->addField($data['created'], [
            'type' => 'datetime',
            'null' => false
        ]);
    }

    if(!$created && $data['updated']) {
        $query->addKey($data['updated'], [$data['updated']]);
        $query->addField($data['updated'], [
            'type' => 'datetime',
            'null' => false
        ]);
    }

    $query = (string) $query;
    if($query !== 'ALTER TABLE `' . $data['name'] . '` ;') {
        $queries[] = $query;
    }

    $installed = $database->getTables($data['name'] . '_%');
    $relations = [];

    if(isset($data['relations'])) {
        $relations = array_keys($data['relations']);
    }

    foreach($installed as $relation) {
        $relation = str_replace($data['name'] . '_', '', $relation);
        //uninstall if it's not in the schema
        if (!in_array($relation, $relations)) {
            $queries[] = 'DROP TABLE IF EXISTS `' . $data['name'] . '_' . $relation . '`;';
        }
    }

    foreach($data['relations'] as $name => $relation) {
        //install if it's installed
        if (in_array($data['name'] . '_' . $name, $installed)) {
            continue;
        }

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

        $queries[] = (string) $query;
    }

    return $queries;
};

return $createAlterQueries($data, $database);
