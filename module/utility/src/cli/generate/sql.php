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

use Cradle\Module\Utility\Installer;

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

    //Available schemas
    $schemas = [];
    $paths = scandir($schemaRoot, 0);
    foreach($paths as $path) {
        if($path === '.' || $path === '..' || substr($path, -4) !== '.php') {
            continue;
        }

        $schemas[] = pathinfo($path, PATHINFO_FILENAME);
    }

    if(empty($schemas)) {
        return CommandLine::error('No schemas found in ' . $schemaRoot);
    }

    //determine the schema
    $schemaName = $request->getStage('schema');

    if(!$schemaName) {
        CommandLine::info('Available schemas:');
        foreach($schemas as $schema) {
            CommandLine::info(' - ' . $schema);
        }

        $schemaName = CommandLine::input('Which schema to use?');
    }

    if(!in_array($schemaName, $schemas)) {
        return CommandLine::error('Invalid schema. Generator Aborted.');
    }

    $schema = $schemaRoot . '/' . $schemaName . '.php';

    if(!file_exists($schema)) {
        return CommandLine::error($schema . ' not found. Aborting.');
    }

    CommandLine::system('Generating SQL...');

    //get the template data
    $data = include __DIR__ . '/helper/data.php';

    $database = SqlFactory::load($service);

    //pre build the create, alter and placeholders
    $create = include __DIR__ . '/helper/sql/create.php';
    $alter = include __DIR__ . '/helper/sql/alter.php';
    $placeholders = include __DIR__ . '/helper/sql/placeholders.php';

    //check for table
    $tables = $database->getTables($data['name']);

    $answer = 'i';
    if(in_array($data['name'], $tables)) {
        $message = '%s was found in database. Alter(a), Install(i) or Cancel(c) ? (c)';
        $answer = CommandLine::input(sprintf($message, $data['name']), 'c');
    }

    if($answer === 'i') {
        $queries = $create;
        $message = "-- CREATE %s table\n```\n%s\n```";
    } else if($answer === 'a') {
        $queries = $alter;
        $message = "-- Alter %s table\n```\n%s\n```";
    } else {
        return CommandLine::error('Generator Aborted.');
    }

    //if nada
    if(empty($queries) && !$placeholders) {
        //dont continue
        return CommandLine::error('Nothing to add or change.');
    }

    //placeholders
    if($placeholders) {
        CommandLine::system('Updating placeholders...');
        $destination = $cwd . '/module/' . $schemaName . '/placeholder.sql';
        file_put_contents($destination, implode("\n\n", $placeholders));

        $message = "-- Data for %s table\n```\n%s\n```";
        CommandLine::info(sprintf($message, $data['name'], implode("\n\n", $placeholders)));
    }

    if(!empty($queries)) {
        CommandLine::system('Updating schema...');

        //determine the next version
        $moduleInstaller = $cwd . '/module/' . $schemaName . '/install';

        if(!is_dir($moduleInstaller)) {
            mkdir($moduleInstaller, 0777, true);
        }

        $version = Installer::getNextVersion($schemaName);

        $destination = $moduleInstaller . '/' . $version . '.sql';
        file_put_contents($destination, implode("\n\n", $queries));

        $destination = $cwd . '/module/' . $schemaName . '/schema.sql';
        file_put_contents($destination, implode("\n\n", $create));

        try {
            Installer::install($schemaName);
        } catch (Exception $e) {
            CommandLine::warning($e->getMessage());
            $truncate = CommandLine::input('Try repopulating the database?(n):', 'n');

            if($truncate !== 'n') {
                $this->trigger('project-flush-sql', $request, $response);
                Installer::install($schemaName);
                $this->trigger('project-populate-sql', $request, $response);
            }
        }

        CommandLine::info(sprintf($message, $data['name'], implode("\n\n", $queries)));
    }

    CommandLine::success('SQL files were generated.');
};
