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

/**
 * CLI populates database with dummy data
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    //whether to ask questions
    $force = $request->hasStage('f') || $request->hasStage('force');

    CommandLine::system('Building SQL...');

    $database = SqlFactory::load($this->package('global')->service('sql-main'));
    $tables = $database->getTables();

    $continue = true;
    if (!empty($tables) && !$force) {
        $answer = CommandLine::input('This will override your existing database. Are you sure?(y)', 'y');
        if ($answer !== 'y') {
            $continue = false;
        }
    }

    if (!$continue) {
        CommandLine::warning('Aborting...');
        return;
    }

    //drop all tables
    foreach ($tables as $table) {
        $database->query('DROP TABLE `' . $table . '`;');
    }

    $path = $this->package('global')->path('module');
    $folders = scandir($path, 0);

    foreach ($folders as $folder) {
        if ($folder === '.' || $folder === '..' || !is_dir($path . '/' . $folder)) {
            continue;
        }

        $file = $path . '/' . $folder . '/schema.sql';

        if (!file_exists($file)) {
            continue;
        }

        $query = file_get_contents($file);
        $this->package('global')->service('sql-main')->query($query);
    }
};
