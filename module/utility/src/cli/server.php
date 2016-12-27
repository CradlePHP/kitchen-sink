<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;

return function ($request, $response) {
    $port = 8888;

    if($request->hasStage('port')) {
        $port = $request->getStage('port');
    } else if($request->hasStage('p')) {
        $port = $request->getStage('p');
    }

    //setup the configs
    CommandLine::system('Starting Server...');
    CommandLine::info('Listening on 127.0.0.1:'.$port);
    CommandLine::info('Press Ctrl-C to quit.');

    $cwd = $request->getServer('PWD');
    system('php -S 127.0.0.1:' . $port . ' -t ' . $cwd . '/public');
};
