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

    $appRoot = $cwd . '/app';
    if(!is_dir($appRoot)) {
        return CommandLine::error('App folder not found. Generator Aborted.');
    }

    $appName = $request->getStage('name');

    if(!$appName) {
        $appName = CommandLine::input('What is the name of the app?');
    }

    if(!$appName) {
        return CommandLine::error('Generator Aborted.');
    }

    $app = $appRoot . '/' . $appName;

    if(is_dir($app)) {
        return CommandLine::error($app . ' already exists. Aborting.');
    }

    $handlebars = include __DIR__ . '/helper/handlebars.php';

    //get all the files
    $sourceRoot = realpath(__DIR__ . '/template/app');
    $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceRoot));
    foreach ($paths as $source) {
        //is it a folder ?
        if($source->isDir()) {
            continue;
        }

        //it's a file, determine the destination
        // if /template/module/src/events.php, then /path/to/file
        $destination = $app . substr($source->getPathname(), strlen($sourceRoot));

        //does it not exist?
        if(!is_dir(dirname($destination))) {
            //then make it
            mkdir(dirname($destination), 0777, true);
        }

        //if the destination exists
        if(file_exists($destination)) {
            //ask questions
            $overwrite = CommandLine::input($destination .' exists. Overwrite?(n)', 'n');
            if($overwrite === 'n') {
                CommandLine::warning('Skipping...');
                continue;
            }
        }

        CommandLine::info('Making ' . $destination);

        $contents = file_get_contents($source->getPathname());
        $template = $handlebars->compile($contents);

        $contents = $template(['app' => $appName]);
        $contents = str_replace('{{ ', '{{', $contents);

        file_put_contents($destination, $contents);
    }

    //add to public/index.php
    $indexFile = $cwd . '/public/index.php';
    if(file_exists($indexFile)) {
        $flag = 'return cradle()';
        $add = '->register(\'/app/' . $appName . '\')';

        $contents = file_get_contents($indexFile);
        if(strpos($contents, $flag) !== false && strpos($contents, $add) === false) {
            $contents = str_replace($flag, $flag . "\n    " . $add, $contents);
        }

        file_put_contents($indexFile, $contents);
    }

    CommandLine::success('App was generated.');
};
