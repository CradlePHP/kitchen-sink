<?php //-->
/**
 * This file is part of the Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\App\Core\File;
use Cradle\Sql\SqlFactory;
use Cradle\CommandLine\Index as CommandLine;

use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Predis\Connection\ConnectionException;

/**
 * CLI help menu
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-help', function ($request, $response) {
    CommandLine::success('cradle app/core queue [event] [data]');
    CommandLine::info(' - Details: Queues any event');
    CommandLine::info(' - Example: cradle app/core queue auth-verify-mail auth_id=1 host=127.0.0.1');
    echo PHP_EOL;
    CommandLine::success('cradle app/core deploy-production');
    CommandLine::info(' - Details: Deploys code to production servers');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;
    CommandLine::success('cradle app/core deploy-cdn');
    CommandLine::info(' - Details: Deploys public assets to CDN');
    CommandLine::info('   You need to setup config/services.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;
    CommandLine::success('cradle app/core connect-to');
    CommandLine::info(' - Details: Gives the command to connect to a production server');
    CommandLine::info('   You need ask the project owner for the private key');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::info('   see: https://gist.github.com/cblanquera/3ff60b4c9afc92be1ac0a9d57afceb17#file-instructions-md');
    echo PHP_EOL;
    CommandLine::success('cradle app/core clear-cache');
    CommandLine::info(' - Details: Clears the Redis cache');
    echo PHP_EOL;
    CommandLine::success('cradle app/core clear-index');
    CommandLine::info(' - Details: Clears the ElasticSearch index');
    echo PHP_EOL;
    CommandLine::success('cradle app/core map-index');
    CommandLine::info(' - Details: Builds an ElasticSearch schema map');
    echo PHP_EOL;
    CommandLine::success('cradle app/core build-index');
    CommandLine::info(' - Details: Populates ElasticSearch index');
    echo PHP_EOL;
    CommandLine::success('cradle app/core build-database');
    CommandLine::info(' - Details: Populates SQL database');
    CommandLine::info(' - Example: cradle app/core build-database');
    CommandLine::info(' - Example: cradle app/core build-database testing_db -h 127.0.0.1 -u root -p root --force');
    echo PHP_EOL;
});

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

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-deploy-production', function ($request, $response) {
    $deploy = cradle('global')->config('deploy');

    if(empty($deploy)) {
        CommandLine::warning('Deploy is not setup. Check config/deploy.php. Aborting.');
        return;
    }

    $deployConfig = [];
    foreach($deploy['servers'] as $name => $server) {
        if(isset($server['deploy'])) {
            if(!$server['deploy']) {
                continue;
            }

            unset($server['deploy']);
        }

        $command = 'ssh -i /tmp/travis_rsa %s@%s -o "StrictHostKeyChecking no" exit';
        exec(sprintf($command, $server['user'], $server['host']));

        $deployConfig[] = '[' . $name . ']';
        foreach($server as $key => $value) {
            $deployConfig[] = $key . ' ' . $value;
        }

        //make it readable
        $deployConfig[] = '';
    }

    //write to tmp
    file_put_contents('/tmp/deploy.conf', implode("\n", $deployConfig));

    //run the deploys
    foreach($deploy['servers'] as $name => $server) {
        exec('./deploy -c /tmp/deploy.conf ' . $name);
    }

    exec('composer update');
    exec('composer install');
});

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-deploy-cdn', function ($request, $response) {
    $cdn = cradle('global')->service('cdn-main');

    if(!$cdn) {
        CommandLine::warning('CDN is not setup. Check config/services.php. Aborting.');
        return;
    }

    // load s3
    $s3 = S3Client::factory([
        'version' => 'latest',
        'region'  => $cdn['region'], //example ap-southeast-1
        'credentials' => [
            'key'    => $cdn['token'],
            'secret' => $cdn['secret'],
        ]
    ]);

    //get the public path
    $public = cradle('global')->path('public');

    //get all the files
    $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($public));

    foreach ($paths as $path) {
        //if it's a directory
        if ($path->isDir()) {
            continue;
        }

        //get the file string
        $file = $path->getPathname();

        //there's no point pushing these things
        if(preg_match('/(\.htaccess)|(\.php)|(DS_Store)|(bower_components)/', $file)) {
            continue;
        }

        // if /foo/bar/repo/public/path/to/file, then /path/to/file
        $path = substr($file, strlen($public) + 1);

        //there's no better way to get a mime
        $mime = File::getMimeFromLink($file);

        //open a pipe
        $pipe = fopen($file, 'r');

        print sprintf("\033[36m%s\033[0m", '[cradle] * pushing '.$path);
        print PHP_EOL;

     	$s3->putObject(array(
            'Bucket' 		=> $cdn['bucket'],
            'ACL'    		=> 'public-read',
            'ContentType'  	=> $mime,
            'Key'    		=> 'web/'.$path,
            'Body'   		=> $pipe,
            'CacheControl' 	=> 'max-age=43200'
        ));

        if(is_resource($pipe)) {
            fclose($pipe);
        }
    }
});

/**
 * CLI production connect
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-connect-to', function ($request, $response) {
    $data = $request->getStage();
    if(!isset($data[0])) {
        CommandLine::error('Not enough arguments. Usage: cradle package app/core connect-to [key]');
    }

    $deploy = cradle('global')->config('deploy');

    if(!isset($deploy['servers'][$data[0]])) {
        CommandLine::error($data[0] . ' is not found in config/deploy.php');
    }

    $server = $deploy['servers'][$data[0]];

    $key = null;
    if(isset($deploy['key'])) {
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
});

/**
 * CLI clear cache
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-clear-cache', function ($request, $response) {
    $service = $this->package('global')->service('cache-main');

    if(!$service) {
        CommandLine::error('Cache is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Clearing Cache...');

    try {
        $service->del(Cradle\App\Core\Model\App::CACHE_SEARCH);
        $service->del(Cradle\App\Core\Model\App::CACHE_DETAIL);

        $service->del(Cradle\App\Core\Model\Auth::CACHE_SEARCH);
        $service->del(Cradle\App\Core\Model\Auth::CACHE_DETAIL);

        $service->del(Cradle\App\Core\Model\Profile::CACHE_SEARCH);
        $service->del(Cradle\App\Core\Model\Profile::CACHE_DETAIL);

        $service->del(Cradle\App\Core\Model\Session::CACHE_SEARCH);
        $service->del(Cradle\App\Core\Model\Session::CACHE_DETAIL);

        //ADD YOUR CUSTOM MODELS HERE
    } catch(ConnectionException $e) {
        //because there is no reason to continue
        CommandLine::warning('No cache server found. Aborting...');
    }
});

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-clear-index', function ($request, $response) {
    $service = $this->package('global')->service('index-main');

    if(!$service) {
        CommandLine::error('Index is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Clearing Index...');

    $objects = [
        'app',
        'auth',
        'profile',
        'session',
        //ADD YOUR CUSTOM MODELS HERE
    ];

    foreach($objects as $object) {
        try {
            $response = $service->indices()->delete(['index' => $object]);
        } catch(Missing404Exception $e) {
        } catch(NoNodesAvailableException $e) {
            //because there is no reason to continue
            CommandLine::warning('No index server found. Aborting...');
            return;
        }
    }
});

/**
 * CLI map index
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-map-index', function ($request, $response) {
    $index = $this->package('global')->service('index-main');

    if(!$index) {
        CommandLine::error('Index is not enabled. Check config/services.php');
        return;
    }

    $database = SqlFactory::load($this->package('global')->service('sql-main'));

    CommandLine::system('Mapping Index...');

    $tables = [
        'app',
        'auth',
        'profile',
        'session',
        //ADD YOUR CUSTOM MODELS HERE
    ];

    $meta = [];

    //in this iteration we will form the meta
    foreach($tables as $table) {
        $columns = $database->getColumns($table);

        foreach ($columns as $i => $column) {
            $type = $column['Type'];

            if(strpos($type, '(')) {
                list($type, $tmp) = explode('(', $type);
                if(strpos($tmp, ')')) {
                    list($length, $tmp) = explode(')', $tmp);
                }
            }

            switch($type) {
                case 'text':
                    $meta[$column['Field']]['type'] = 'text';
                    break;
                case 'json':
                    $meta[$column['Field']]['type'] = 'object';
                    break;
                case 'float':
                    $meta[$column['Field']]['type'] = 'float';
                    break;
                case 'int':
                    $meta[$column['Field']]['type'] = 'integer';
                    if($length && $length === 1) {
                        $meta[$column['Field']]['type'] = 'small';
                    }

                    if($length && $length > 9) {
                        $meta[$column['Field']]['type'] = 'long';
                    }
                    break;
                case 'date':
                    $meta[$column['Field']]['type'] = 'date';
                    $meta[$column['Field']]['format'] = 'yyyy-MM-dd';
                    break;
                case 'time':
                    $meta[$column['Field']]['type'] = 'date';
                    $meta[$column['Field']]['format'] = 'HH:mm:ss';
                    break;
                case 'datetime':
                    $meta[$column['Field']]['type'] = 'date';
                    $meta[$column['Field']]['format'] = 'yyyy-MM-dd HH:mm:ss';
                    break;
                case 'varchar':
                default:
                    $meta[$column['Field']]['type'] = 'string';
                    break;
            }

            if($column['Key']) {
                $meta[$column['Field']]['fields']['keyword']['type'] = 'keyword';
            }
        }
    }

    //in this iteration we will get the first data
    foreach($tables as $table) {
        $model = $this->package('/app/core')->model($table);
        $results = $model->databaseSearch(['range' => 1]);

        if(!$results['total'] || !isset($results['rows'][0])) {
            CommandLine::warning('No sample detail found in ' . $table . '. Skipping...');
            continue;
        }

        $map = [];
        foreach($results['rows'][0] as $column => $value) {
            //if is object
            if(is_array($value)) {
                $meta[$column]['type'] = 'object';
            }

            //if it's not found in the meta
            if(!isset($meta[$column])) {
                //we cant auto map this
                continue;
            }

            $map[$column] = $meta[$column];

            //if is object
            if($meta[$column]['type'] === 'object') {
                //find out what kind of object it is
                $json = json_encode($value);
                if(strpos($json, '[{') === 0) {
                    $map[$column]['type'] = 'nested';
                } else if(strpos($json, '[') === 0) {
                    $map[$column]['type'] = 'string';
                }
            }
        }

        if($request->hasStage('o')) {
            echo json_encode([
                'mappings' => [
                    'main' => [
                        'properties' => $map
                    ]
                ]
            ], JSON_PRETTY_PRINT);
        }

        //now map
        try {
            $index->indices()->create(['index' => $table]);
            $index->indices()->putMapping([
                'index' => $table,
                'type' => 'main',
                'body' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => $map
                ]
            ]);
        } catch(NoNodesAvailableException $e) {
            //because there is no reason to continue;
            CommandLine::warning('No index server found. Aborting...');
            return;
        }
    }
});

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-build-index', function ($request, $response) {
    $service = $this->package('global')->service('index-main');

    if(!$service) {
        CommandLine::error('Index is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Building Index...');

    $objects = [
        'app',
        'auth',
        'profile',
        'session',
        //ADD YOUR CUSTOM MODELS HERE
    ];

    foreach($objects as $object) {
        CommandLine::info('Indexing ' . $object . '...');

        $model = $this->package('/app/core')->model($object);

        $i = 0;
        do {
            CommandLine::info('  - Indexing ' . $object . ': ' . $i . '-' . ($i + 100));

            $results = $model->databaseSearch([
                'start' => $i,
                'range' => 100
            ]);

            $rows = $results['rows'];
            $total = $results['total'];

            foreach($rows as $row) {
                $primary = $object . '_id';

                if($object === 'review') {
                    $primary = 'comment_id';
                }

                if($model->indexCreate($row[$primary]) === false) {
                    //because there is no reason to continue;
                    CommandLine::warning('No index server found. Aborting...');
                    return;
                }
            }

            $i += 100;
        } while($i < $total);
    }
});

/**
 * CLI build database
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('app-core-build-database', function ($request, $response) {
    //name
    $name = $request->getStage(0);

    if(!$name) {
        $name = CommandLine::input('What is the name of the database you want to build to?(testing_db)', 'testing_db');
    }

    //host
    $host = '127.0.0.1';
    if($request->hasStage('h')) {
        $host = $request->getStage('h');
    } else if($request->hasStage('host')) {
        $host = $request->getStage('host');
    }

    //user
    $user = 'root';
    if($request->hasStage('u')) {
        $user = $request->getStage('u');
    } else if($request->hasStage('user')) {
        $user = $request->getStage('user');
    }

    //pass
    $pass = '';
    if($request->hasStage('p')) {
        $pass = $request->getStage('p');
    } else if($request->hasStage('password')) {
        $pass = $request->getStage('password');
    }

    //connection
    $build = SqlFactory::load(new PDO('mysql:host=' . $host, $user, $pass));
    $exists = $build->query("SHOW DATABASES LIKE '" . $name . "';");

    if(!empty($exists) && !$request->hasStage('f') && !$request->hasStage('force')) {
        $answer = CommandLine::input('This will override your existing database. Are you sure?(y)', 'y');
        if($answer !== 'y') {
            CommandLine::system('Aborting...');
            return;
        }
    }

    CommandLine::system('Installing Database...');

    $build->query('CREATE DATABASE IF NOT EXISTS `' . $name . '`;');

    $database = SqlFactory::load(new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass));

    //drop all tables
    $tables = $database->getTables();
    foreach($tables as $table) {
        $database->query('DROP TABLE `' . $table . '`;');
    }

    //then run the schema
    $schema = file_get_contents(__DIR__ . '/../../schema.sql');
    $database->query($schema);

    //then populate data
    if(file_exists(__DIR__ . '/../../placeholder.sql')) {
        $fixtures = file_get_contents(__DIR__ . '/../../placeholder.sql');
        $database->query($fixtures);
    }

    //report
    $tables = $database->getTables();

    foreach($tables as $table) {
        CommandLine::info('- ' . $table . ' installed.');
    }
});
