<?php //-->
/**
 * This file is part of the Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;

/**
 * CLI queue - cradle app/core queue auth-verify auth_slug=<email>
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-queue', function ($request, $response) {
    $data = $request->getStage();
    if(!isset($data[0])) {
        CommandLine::error('Not enough arguments. Usage: cradle package app/core queue event data');
    }

    $event = array_shift($data);

    $priority = 'low';
    if(isset($data['priority'])) {
        $priority = $data['priority'];
        unset($data['priority']);
    }

    $delay = false;
    if(isset($data['delay'])) {
        $delay = $data['delay'];
        unset($data['delay']);
    }

    if(!cradle('global')->queue($event, $data, $priority, $delay)) {
        CommandLine::error('Unable to queue, check config/services.php for correct connection information.');
    }
});
