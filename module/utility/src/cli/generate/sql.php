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

return function($request, $response) {
    //get database
    $service = $this->package('global')->service('sql-main');

    if(!$service) {
        CommandLine::error('Database was not found in config/services.php');
    }

    $cwd = $request->getServer('PWD');
    $schemaRoot = $cwd . '/schema';

    if(!is_dir($schemaRoot)) {
        return CommandLine::error('Schema folder not found. Generator Aborted.');
    }

    //schema
    $name = $request->getStage('schema');

    if(!$name) {
        //Available schemas
        $paths = scandir($schemaRoot, 0);
        $schemas = [];
        foreach($paths as $path) {
            if($path === '.' || $path === '..' || substr($path, -4) !== '.php') {
                continue;
            }

            $schemas[] = pathinfo($file, PATHINFO_FILENAME);
        }

        if(empty($schemas)) {
            return CommandLine::error('No Schemas found in ' . $root);
        } else {
            CommandLine::info('Available schemas:');
            foreach($schemas as $schema) {
                CommandLine::info(' - ' . $schema);
            }
        }

        $name = CommandLine::input('Which schema to use?');
    }

    if(!$name) {
        return CommandLine::error('Invalid schema. Generator Aborted.');
    }

    //now we can determine the source
    $schema = $schemaRoot . '/' . $name . '.php';

    if(!file_exists($schema)) {
        return CommandLine::error($schema . ' not found. Aborting.');
    }

    //get the template data
    include_once $schema;

    $object = $this
        ->resolveShared(Schema::class)
        ->setDatabase($service)
        ->object($name);

    $database = SqlFactory::load($service);

    //check for table
    $tables = $database->getTables($name);

    $answer = 'i';
    if(in_array($name, $tables)) {
        $message = '%s was found. Alter(a), Install(i) or Cancel(c) ? (c)';
        $answer = CommandLine::input(sprintf($message, $name), 'c');
    }

    if($answer === 'i') {
        $queries = [
            'DROP TABLE IF EXISTS `' . $name . '`;',
            $object->getCreateSchema()
        ];

        print_r($object->getRelationSchema());

        foreach($object->getRelationSchema() as $table => $query) {
            $queries[] = 'DROP TABLE IF EXISTS `'.$name . '_' . $table . '`;';
            $queries[] = $query;
        }

        $message = "-- CREATE %s table\n```\n%s\n```";
        CommandLine::info(sprintf($message, $name, implode("\n", $queries)));

        foreach($queries as $query) {
            $database->query($query);
        }
    } else if($answer === 'a') {
        $queries = [$object->getAlterSchema()];

        $installed = $database->getTables($name.'_%');
        $relations = array_keys($object->getRelations());

        foreach($installed as $relation) {
            $relation = str_replace($name . '_', '', $relation);
            //uninstall if it's not in the schema
            if (!in_array($relation, $relations)) {
                $queries[] = 'DROP TABLE IF EXISTS `' . $name . '_' . $relation . '`;';
            }
        }

        foreach($object->getRelations() as $table => $relation) {
            //install if it's not installed
            if (!in_array($name . '_' . $table, $installed)) {
                $queries[] = $object->getRelationSchema([$table])[$table];
            }
        }

        $message = "-- Alter %s table\n```\n%s\n```";
        CommandLine::info(sprintf($message, $name, implode("\n", $queries)));

        foreach($queries as $query) {
            $database->query($query);
        }
    } else {
        return CommandLine::error('Generator Aborted.');
    }

    CommandLine::success('SQL was generated.');
};
