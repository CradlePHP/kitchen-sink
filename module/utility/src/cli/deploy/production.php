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
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    $deploy = cradle('global')->config('deploy');

    if (empty($deploy)) {
        CommandLine::warning('Deploy is not setup. Check config/deploy.php. Aborting.');
        return;
    }

    $deployConfig = [];
    foreach ($deploy['servers'] as $name => $server) {
        if (isset($server['deploy'])) {
            if (!$server['deploy']) {
                continue;
            }

            unset($server['deploy']);
        }

        $command = 'ssh -i /tmp/travis_rsa %s@%s -o "StrictHostKeyChecking no" exit';
        exec(sprintf($command, $server['user'], $server['host']));

        $deployConfig[] = '[' . $name . ']';
        foreach ($server as $key => $value) {
            $deployConfig[] = $key . ' ' . $value;
        }

        //make it readable
        $deployConfig[] = '';
    }

    //write to tmp
    file_put_contents('/tmp/deploy.conf', implode("\n", $deployConfig));

    //run the deploys
    foreach ($deploy['servers'] as $name => $server) {
        exec('./deploy -c /tmp/deploy.conf ' . $name);
    }

    exec('composer update');
    exec('composer install');
};
