<?php //-->

$createPlaceholderQueries = function($data, $database) {
    if(!isset($data['fixtures']) || empty($data['fixtures'])) {
        return false;
    }

    $found = false;
    $queries = [];
    $columns = [];

    if(isset($data['primary'])) {
        $columns[] = $data['primary'];
    }

    if(isset($data['active'])) {
        $columns[] = $data['active'];
    }

    if(isset($data['created'])) {
        $columns[] = $data['created'];
    }

    if(isset($data['updated'])) {
        $columns[] = $data['updated'];
    }

    foreach($data['fields'] as $name => $field) {
        if(isset($field['sql']['type'])) {
            $columns[] = $name;
        }
    }

    $query = $database->getInsertQuery($data['name']);
    foreach($data['fixtures'] as $i => $row) {
        foreach($row as $key => $value) {
            //if it's not in the sql columns
            if(!in_array($key, $columns)) {
                //skip it
                continue;
            }

            $found = true;

            if(is_string($value)) {
                $value = "'" . addslashes($value) . "'";
            } else if(is_bool($value)) {
                $value = (int) $value;
            } else if(is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }

            $query->set($key, $value, $i);
        }
    }

    if($found) {
        $queries[] = (string) $query;
    }

    if(!isset($data['relations'])) {
        return $queries;
    }

    foreach($data['relations'] as $name => $relation) {
        $query = $database->getInsertQuery($data['name'] . '_' . $name);

        $found = false;
        foreach($data['fixtures'] as $i => $row) {
            //if the primaries are set
            if(isset($row[$data['primary']], $row[$relation['primary']])) {
                $found = true;
                $query->set($data['primary'], $row[$data['primary']], $i);
                $query->set($relation['primary'], $row[$relation['primary']], $i);
            }
        }

        if($found) {
            $queries[] = (string) $query;
        }
    }

    return $queries;
};

return $createPlaceholderQueries($data, $database);
