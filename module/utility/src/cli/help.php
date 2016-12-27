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
 * CLI help menu
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
return function ($request, $response) {
    CommandLine::success('bin/cradle project install');
    CommandLine::info(' - Details: Installs Project');
    CommandLine::info(' - Example: bin/cradle project install');
    CommandLine::info(' - Example: bin/cradle project install --force --populate-sql');
    CommandLine::info(' - Example: bin/cradle project install testing_db -h 127.0.0.1 -u root -p root --force');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project update');
    CommandLine::info(' - Details: Updates Project with versioning install scripts');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project server');
    CommandLine::info(' - Details: Starts up the PHP server (dev mode)');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project generate-app');
    CommandLine::info(' - Details: Generates a new app folder');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project generate-module');
    CommandLine::info(' - Details: Generates a new module given schema');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project generate-view');
    CommandLine::info(' - Details: Generates a new view given schema');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project generate-sql');
    CommandLine::info(' - Details: Generates SQL given schema');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project flush-sql');
    CommandLine::info(' - Details: Clears SQL database');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project build-sql');
    CommandLine::info(' - Details: Builds SQL schema on database');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project flush-elastic');
    CommandLine::info(' - Details: Clears the ElasticSearch index');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project map-elastic');
    CommandLine::info(' - Details: Builds an ElasticSearch schema map');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project populate-elastic');
    CommandLine::info(' - Details: Populates ElasticSearch index');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project populate-sql');
    CommandLine::info(' - Details: Populates SQL database');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project flush-redis');
    CommandLine::info(' - Details: Clears the Redis cache');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project queue [event] [data]');
    CommandLine::info(' - Details: Queues any event');
    CommandLine::info(' - Example: bin/cradle project queue auth-verify-mail auth_id=1 host=127.0.0.1');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project work');
    CommandLine::info(' - Details: Starts a worker');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project connect-to');
    CommandLine::info(' - Details: Gives the command to connect to a production server');
    CommandLine::info('   You need ask the project owner for the private key');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::info('   see: https://gist.github.com/cblanquera/3ff60b4c9afc92be1ac0a9d57afceb17#file-instructions-md');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project deploy-production');
    CommandLine::info(' - Details: Deploys code to production servers');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project deploy-s3');
    CommandLine::info(' - Details: Deploys public assets to AWS S3');
    CommandLine::info('   You need to setup config/services.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;
};
