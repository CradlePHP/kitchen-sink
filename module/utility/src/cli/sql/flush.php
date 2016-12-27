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
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    CommandLine::system('Flushing SQL...');

    $database = SqlFactory::load($this->package('global')->service('sql-main'));

    //truncate all tables
    $tables = $database->getTables();
    foreach ($tables as $table) {
        $database->query('TRUNCATE TABLE `' . $table . '`;');
    }
};
