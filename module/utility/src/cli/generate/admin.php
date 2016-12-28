<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;

return function($request, $response) {
    $cwd = $request->getServer('PWD');

    $schemaRoot = $cwd . '/schema';
    if(!is_dir($schemaRoot)) {
        return CommandLine::error('Schema folder not found. Generator Aborted.');
    }

    $appRoot = $cwd . '/app';
    if(!is_dir($appRoot)) {
        return CommandLine::error('App folder not found. Generator Aborted.');
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

    //Available apps
    $apps = [];
    $paths = scandir($appRoot, 0);
    foreach($paths as $path) {
        if($path === '.' || $path === '..' || !is_dir($appRoot . '/' . $path)) {
            continue;
        }

        $apps[] = pathinfo($path, PATHINFO_BASENAME);
    }

    if(empty($apps)) {
        return CommandLine::error('No apps found in ' . $appRoot);
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

    //determine the app
    $appName = $request->getStage('app');

    if(!$appName) {
        CommandLine::info('Available apps:');
        foreach($apps as $app) {
            CommandLine::info(' - ' . $app);
        }

        $appName = CommandLine::input('Which app to use?');
    }

    if(!in_array($appName, $apps)) {
        return CommandLine::error('Invalid app. Generator Aborted.');
    }

    $app = $appRoot . '/' . $appName;

    if(!is_dir($app)) {
        return CommandLine::error($app . ' not found. Aborting.');
    }

    CommandLine::system('Generating admin...');

    //get the template data
    $handlebars = include __DIR__ . '/helper/handlebars.php';
    $data = include __DIR__ . '/helper/data.php';

    //get all the files
    $sourceRoot = __DIR__ . '/template/admin';
    $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceRoot));
    foreach ($paths as $source) {
        //is it a folder ?
        if($source->isDir()) {
            continue;
        }

        //it's a file, determine the destination
        // if /template/module/src/events.php, then /path/to/file
        $destination = $app . substr($source->getPathname(), strlen($sourceRoot));
        $destination = str_replace('NAME', $schemaName, $destination);

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

        $contents = file_get_contents($source->getPathname());
        $template = $handlebars->compile($contents);

        $contents = $template($data);
        $contents = str_replace('{{ ', '{{', $contents);

        file_put_contents($destination, $contents);
    }

    CommandLine::success($schemaName . ' view was generated.');
};
