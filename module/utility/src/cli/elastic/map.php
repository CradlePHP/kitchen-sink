<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;
use Cradle\Sql\SqlFactory;
use Cradle\Module\Utility\ServiceFactory;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;

/**
 * CLI map index
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $index = $this->package('global')->service('elastic-main');

    if (!$index) {
        CommandLine::error('ElasticSearch is not enabled. Check config/services.php');
        return;
    }

    $database = SqlFactory::load($this->package('global')->service('sql-main'));

    CommandLine::system('Mapping Index...');

    //in this iteration
    //we need to get a flat version of all
    //the column meta from every table
    $tables = $database->query('show tables;');
    foreach ($tables as $i => $table) {
        $table = array_values($table);
        $tables[$i] = $table[0];
    }

    $meta = [];

    //in this iteration we will form the meta
    foreach ($tables as $table) {
        $columns = $database->getColumns($table);

        foreach ($columns as $i => $column) {
            $type = $column['Type'];

            if (strpos($type, '(')) {
                list($type, $tmp) = explode('(', $type);
                if (strpos($tmp, ')')) {
                    list($length, $tmp) = explode(')', $tmp);
                }
            }

            switch ($type) {
                case 'text':
                    $meta[$column['Field']]['type'] = 'text';
                    break;
                case 'json':
                    $meta[$column['Field']]['type'] = 'object';
                    //get a sample
                    $row = $database
                        ->search($table)
                        ->addFilter($column['Field'] . ' IS NOT NULL')
                        ->setRange(1)
                        ->getRow();

                    //find out what kind of object it is
                    $json = $row[$column['Field']];
                    if (strpos($json, '[{') === 0) {
                        $meta[$column['Field']]['type'] = 'nested';
                    } else if (strpos($json, '[') === 0 && $json !== '[]') {
                        $meta[$column['Field']]['type'] = 'string';
                    }
                    break;
                case 'float':
                    $meta[$column['Field']]['type'] = 'float';
                    break;
                case 'int':
                    $meta[$column['Field']]['type'] = 'integer';
                    if ($length && $length === 1) {
                        $meta[$column['Field']]['type'] = 'small';
                    }

                    if ($length && $length > 9) {
                        $meta[$column['Field']]['type'] = 'long';
                    }
                    break;
                case 'date':
                    $meta[$column['Field']]['type'] = 'date';
                    $meta[$column['Field']]['format'] = 'yyyy-MM-dd';
                    break;
                case 'time':
                    $meta[$column['Field']]['type'] = 'date';
                    $meta[$column['Field']]['format'] = 'HH:mm:ss';
                    break;
                case 'datetime':
                    $meta[$column['Field']]['type'] = 'date';
                    $meta[$column['Field']]['format'] = 'yyyy-MM-dd HH:mm:ss';
                    break;
                case 'varchar':
                default:
                    $meta[$column['Field']]['type'] = 'string';
                    break;
            }

            if ($column['Key']) {
                $meta[$column['Field']]['fields']['keyword']['type'] = 'keyword';
            }
        }
    }

    //in this iteration we will get the first data
    foreach ($tables as $table) {
        $sql = ServiceFactory::get($table, 'sql');

        if (!$sql) {
            continue;
        }

        $results = $sql->search(['range' => 1]);

        if (!$results['total'] || !isset($results['rows'][0])) {
            CommandLine::warning('No sample detail found in ' . $table . '. Skipping...');
            continue;
        }

        $map = [];
        foreach ($results['rows'][0] as $column => $value) {
            //if is object
            if (is_array($value) && !isset($meta[$column])) {
                $meta[$column]['type'] = 'object';
            }

            //if it's not found in the meta
            if (!isset($meta[$column])) {
                //we cant auto map this
                continue;
            }

            $map[$column] = $meta[$column];
        }

        if ($request->hasStage('o')) {
            echo json_encode([
                'mappings' => [
                    'main' => [
                        'properties' => $map
                    ]
                ]
            ], JSON_PRETTY_PRINT);
        }

        //now map
        try {
            $index->indices()->create(['index' => $table]);
            $index->indices()->putMapping([
                'index' => $table,
                'type' => 'main',
                'body' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => $map
                ]
            ]);
        } catch (NoNodesAvailableException $e) {
            //because there is no reason to continue;
            CommandLine::warning('No index server found. Aborting...');
            return;
        } catch (BadRequest400Exception $e) {
            //already mapped
            CommandLine::warning($e->getMessage());
        }
    }
};
