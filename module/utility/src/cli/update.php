<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;
use Cradle\Module\Utility\Installer;

/**
 * CLI project update
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    //setup the configs
    CommandLine::system('Updating project...');

    $versions = Installer::install();

    foreach($versions as $path => $version) {
        CommandLine::success('Updated ' . $path . ' to v' . $version);
    }
};
