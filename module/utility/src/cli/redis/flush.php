<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;
use Predis\Connection\ConnectionException;

/**
 * CLI clear cache
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $service = $this->package('global')->service('redis-main');

    if (!$service) {
        CommandLine::error('Cache is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Flushing Redis...');

    try {
        $service->flushAll();
    } catch (ConnectionException $e) {
        //because there is no reason to continue
        CommandLine::warning('No cache server found. Aborting...');
    }
};
