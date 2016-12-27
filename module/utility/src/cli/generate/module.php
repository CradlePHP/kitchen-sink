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

    CommandLine::system('Generating module...');

    //get the template data
    $handlebars = include __DIR__ . '/helper/handlebars.php';
    $data = include __DIR__ . '/helper/data.php';

    //get destination root
    $destinationRoot = $cwd . '/module/' . $schemaName;
    if(!is_dir($destinationRoot)) {
        mkdir($destinationRoot, 0777);
    }

    //get all the files
    $sourceRoot = realpath(__DIR__ . '/../template/module');
    $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceRoot));
    foreach ($paths as $source) {
        //is it a folder ?
        if($source->isDir()) {
            continue;
        }

        //it's a file, determine the destination
        // if /template/module/src/events.php, then /path/to/file
        $destination = $destinationRoot . substr($path->getPathname(), strlen($sourceRoot));

        //does it not exist?
        if(!is_dir(dirname($destination))) {
            //then make it
            mkdir(dirname($destination), 0777, true);
        }

        //if the destination exists
        if(file_exists($destination)) {
            //ask questions
            $overwrite = Command::input($destination .' exists. Overwrite?(n)');
            if($overwrite === 'n') {
                CommandLine::warning('Skipping...');
                continue;
            }
        }

        CommandLine::info('Making ' . $destination);

        $contents = file_get_contents($path->getPathname());
        $template = $handlebars->compile($contents);

        $contents = $template($data);
        $contents = str_replace('{{ ', '{{', $contents);

        file_put_contents($destination, $contents);
    }

    CommandLine::success($schemaName . ' module was generated.');
};
