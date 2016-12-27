<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;

/**
 * CLI production connect
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $data = $request->getStage();
    if (!isset($data[0])) {
        CommandLine::error('Not enough arguments. Usage: cradle package project connect-to [key]');
    }

    $deploy = cradle('global')->config('deploy');

    if (!isset($deploy['servers'][$data[0]])) {
        CommandLine::error($data[0] . ' is not found in config/deploy.php');
    }

    $server = $deploy['servers'][$data[0]];

    $key = null;
    if (isset($deploy['key'])) {
        $key = $deploy['key'] . ' ';
    }

    $command = 'ssh -i %s%s@%s';

    print PHP_EOL;
    CommandLine::success(sprintf(
        $command,
        $key,
        $server['user'],
        $server['host']
    ));
    print PHP_EOL;

    /* NOT SURE HOW TO DO THIS
    exec(sprintf(
        $command,
        $key,
        $server['user'],
        $server['host']
    ));*/
};
