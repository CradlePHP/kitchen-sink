<?php //-->
/**
 * This file is part of the Dealcha Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Sql\SqlFactory;
use Cradle\Module\Utility\File;
use Cradle\Module\Utility\Installer;
use Cradle\Module\Utility\ServiceFactory;

use Cradle\CommandLine\Index as CommandLine;

use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Predis\Connection\ConnectionException;

/**
 * CLI help menu
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('project-help', function ($request, $response) {
    CommandLine::success('bin/cradle project-server');
    CommandLine::info(' - Details: Starts up the PHP server (dev mode)');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-install');
    CommandLine::info(' - Details: Installs Project');
    CommandLine::info(' - Example: bin/cradle project-install');
    CommandLine::info(' - Example: bin/cradle project-install --force --populate-sql');
    CommandLine::info(' - Example: bin/cradle project-install testing_db -h 127.0.0.1 -u root -p root --force');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-update');
    CommandLine::info(' - Details: Updates Project with versioning install scripts');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-flush-sql');
    CommandLine::info(' - Details: Clears SQL database');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-build-sql');
    CommandLine::info(' - Details: Builds SQL schema on database');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-flush-elastic');
    CommandLine::info(' - Details: Clears the ElasticSearch index');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-map-elastic');
    CommandLine::info(' - Details: Builds an ElasticSearch schema map');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-populate-elastic');
    CommandLine::info(' - Details: Populates ElasticSearch index');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-populate-sql');
    CommandLine::info(' - Details: Populates SQL database');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-flush-redis');
    CommandLine::info(' - Details: Clears the Redis cache');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-queue [event] [data]');
    CommandLine::info(' - Details: Queues any event');
    CommandLine::info(' - Example: bin/cradle project queue auth-verify-mail auth_id=1 host=127.0.0.1');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-work');
    CommandLine::info(' - Details: Starts a worker');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-connect-to');
    CommandLine::info(' - Details: Gives the command to connect to a production server');
    CommandLine::info('   You need ask the project owner for the private key');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::info('   see: https://gist.github.com/cblanquera/3ff60b4c9afc92be1ac0a9d57afceb17#file-instructions-md');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-deploy-production');
    CommandLine::info(' - Details: Deploys code to production servers');
    CommandLine::info('   You need to setup config/deploy.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;

    CommandLine::success('bin/cradle project-deploy-cdn');
    CommandLine::info(' - Details: Deploys public assets to CDN');
    CommandLine::info('   You need to setup config/services.php');
    CommandLine::warning('   Use with caution.');
    echo PHP_EOL;
});

/**
 * CLI queue - bin/cradle project queue auth-verify auth_slug=<email>
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$cradle->on('project-queue', function ($request, $response) {
    $data = $request->getStage();
    if (!isset($data[0])) {
        CommandLine::error('Not enough arguments. Usage: cradle package project queue event data');
    }

    $event = array_shift($data);

    $priority = 'low';
    if (isset($data['priority'])) {
        $priority = $data['priority'];
        unset($data['priority']);
    }

    $delay = false;
    if (isset($data['delay'])) {
        $delay = $data['delay'];
        unset($data['delay']);
    }

    if (!cradle('global')->queue($event, $data, $priority, $delay)) {
        CommandLine::error('Unable to queue, check config/services.php for correct connection information.');
    }
});

/**
 * CLI starts worker
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-work', function ($request, $response) {
    static $channel = null;

    //get the channel
    if (is_null($channel)) {
        //add a logger
        $this->addLogger(function ($message) {
            echo '[cradle] ' . $message . PHP_EOL;
        });

        $channel = $this
            ->package('global')
            ->service('rabbitmq-main')
            ->channel();
    }

    //get the queue name
    $settings = $this->package('global')->config('settings');
    $name = 'queue';
    if (isset($settings['queue']) && trim($settings['queue'])) {
        $name = $settings['queue'];
    }

    // notify its up
    $this->log('Waiting for tasks.');

    // define the job
    $job = function ($message) use ($name, $request, $response) {
        // notify once a task is received
        $this->log('A task is received.');

        // get the data
        $data = json_decode($message->body, true);

        // extract the job to perform
        if (!isset($data['__TASK__'])) {
            // once an exception is encountered, notify that task is not done
            $this->log('Task is not done.');

            // set or flag that the task is not done and the worker is free
            $message
                ->delivery_info['channel']
                ->basic_nack($message->delivery_info['delivery_tag']);

            // set or flag that the task is not done and the worker is free and requeue task
            //$message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag'], false, true);
        }

        $task = $data['__TASK__'];
        unset($data['__TASK__']);

        try {
            //start
            $this->log($task . ' is running');

            $request->setStage($data);

            $this->triggerEvent($task, $request, $response);

            //if there was an error
            if ($response->get('json', 'error')) {
                $error = $response->getDot('json.message');

                $this->log('Task is not done.');
                $this->log($error);
                $this->log(json_encode($data));

                //an exception didn't trigger
                //it just refused to do it
                //so why try it again ?
            } else {
                $this->log($task . ' was performed');
                $this->log(json_encode($data));

                // once done, notify again, that it is done
                $this->log('Task is done.');
            }

            // set or flag that the worker is free
            $message
                ->delivery_info['channel']
                ->basic_ack($message->delivery_info['delivery_tag']);
        } catch (Throwable $e) {
            // once an exception is encountered, notify that task is not done
            $this->log('Task is not done.');
            $this->log($e->getMessage());

            // set or flag that the task is not done and the worker is free
            $message
                ->delivery_info['channel']
                ->basic_nack($message->delivery_info['delivery_tag']);
        }
    };

    // worker consuming tasks from queue
    $channel->basic_qos(null, 1, null);

    // now we need to catch the channel exception
    // when task does not exists in our queue
    try {
        // comsume messages on queue
        $channel->basic_consume(
            $name,
            '',
            false,
            false,
            false,
            false,
            $job->bindTo($this)
        );
    } catch (AMQPProtocolChannelException $e) {
        // notify that task does not exists
        $this->log('Task does not exists, creating task. Please re-run the worker.');

        // create the init queue
        $this->package('global')->queue('init');
    }

    while (count($channel->callbacks)) {
        $channel->wait();
    }
});

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-deploy-production', function ($request, $response) {
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
});

/**
 * CLI Deploy
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-deploy-cdn', function ($request, $response) {
    $cdn = cradle('global')->service('s3-main');

    if (!$cdn) {
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
        if (preg_match('/(\.htaccess)|(\.php)|(DS_Store)|(bower_components)/', $file)) {
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
            'Bucket'        => $cdn['bucket'],
            'ACL'           => 'public-read',
            'ContentType'   => $mime,
            'Key'           => 'web/'.$path,
            'Body'          => $pipe,
            'CacheControl'  => 'max-age=43200'
        ));

        if (is_resource($pipe)) {
            fclose($pipe);
        }
    }
});

/**
 * CLI production connect
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-connect-to', function ($request, $response) {
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
});

/**
 * CLI clear cache
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-flush-redis', function ($request, $response) {
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
});

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-flush-elastic', function ($request, $response) {
    $service = $this->package('global')->service('elastic-main');

    if (!$service) {
        CommandLine::error('ElasticSearch is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Flushing ElasticSearch...');

    try {
        $service->indices()->delete(['index' => '*']);
    } catch (Missing404Exception $e) {
    } catch (NoNodesAvailableException $e) {
        //because there is no reason to continue
        CommandLine::warning('No index server found. Aborting...');
    }
});

/**
 * CLI map index
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-map-elastic', function ($request, $response) {
    $index = $this->package('global')->service('elastic-main');

    if (!$index) {
        CommandLine::error('ElasticSearch is not enabled. Check config/services.php');
        return;
    }

    $database = SqlFactory::load($this->package('global')->service('sql-main'));

    CommandLine::system('Mapping Index...');

    //in this iteration
    //we need to get a flat version of all
    //the column meta from every table
    $tables = $database->query('show tables;');
    foreach ($tables as $i => $table) {
        $table = array_values($table);
        $tables[$i] = $table[0];
    }

    $meta = [];

    //in this iteration we will form the meta
    foreach ($tables as $table) {
        $columns = $database->getColumns($table);

        foreach ($columns as $i => $column) {
            $type = $column['Type'];

            if (strpos($type, '(')) {
                list($type, $tmp) = explode('(', $type);
                if (strpos($tmp, ')')) {
                    list($length, $tmp) = explode(')', $tmp);
                }
            }

            switch ($type) {
                case 'text':
                    $meta[$column['Field']]['type'] = 'text';
                    break;
                case 'json':
                    $meta[$column['Field']]['type'] = 'object';
                    //get a sample
                    $row = $database
                        ->search($table)
                        ->addFilter($column['Field'] . ' IS NOT NULL')
                        ->setRange(1)
                        ->getRow();

                    //find out what kind of object it is
                    $json = $row[$column['Field']];
                    if (strpos($json, '[{') === 0) {
                        $meta[$column['Field']]['type'] = 'nested';
                    } else if (strpos($json, '[') === 0 && $json !== '[]') {
                        $meta[$column['Field']]['type'] = 'string';
                    }
                    break;
                case 'float':
                    $meta[$column['Field']]['type'] = 'float';
                    break;
                case 'int':
                    $meta[$column['Field']]['type'] = 'integer';
                    if ($length && $length === 1) {
                        $meta[$column['Field']]['type'] = 'small';
                    }

                    if ($length && $length > 9) {
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

            if ($column['Key']) {
                $meta[$column['Field']]['fields']['keyword']['type'] = 'keyword';
            }
        }
    }

    //in this iteration we will get the first data
    foreach ($tables as $table) {
        $sql = ServiceFactory::get($table, 'sql');

        if (!$sql) {
            continue;
        }

        $results = $sql->search(['range' => 1]);

        if (!$results['total'] || !isset($results['rows'][0])) {
            CommandLine::warning('No sample detail found in ' . $table . '. Skipping...');
            continue;
        }

        $map = [];
        foreach ($results['rows'][0] as $column => $value) {
            //if is object
            if (is_array($value) && !isset($meta[$column])) {
                $meta[$column]['type'] = 'object';
            }

            //if it's not found in the meta
            if (!isset($meta[$column])) {
                //we cant auto map this
                continue;
            }

            $map[$column] = $meta[$column];
        }

        if ($request->hasStage('o')) {
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
        } catch (NoNodesAvailableException $e) {
            //because there is no reason to continue;
            CommandLine::warning('No index server found. Aborting...');
            return;
        } catch (BadRequest400Exception $e) {
            //already mapped
            CommandLine::warning($e->getMessage());
        }
    }
});

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-populate-elastic', function ($request, $response) {
    $service = $this->package('global')->service('elastic-main');

    if (!$service) {
        CommandLine::error('ElasticSearch is not enabled. Check config/services.php');
        return;
    }

    CommandLine::system('Building ElasticSearch...');

    $objects = array_keys(ServiceFactory::get('elastic'));

    foreach ($objects as $object) {
        CommandLine::info('Indexing ' . $object . '...');

        $sql = ServiceFactory::get($object, 'sql');
        $elastic = ServiceFactory::get($object, 'elastic');

        $i = 0;
        do {
            CommandLine::info('  - Indexing ' . $object . ': ' . $i . '-' . ($i + 100));

            $results = $sql->search([
                'start' => $i,
                'range' => 100
            ]);

            $rows = $results['rows'];
            $total = $results['total'];

            foreach ($rows as $row) {
                $primary = $object . '_id';

                if ($object === 'review') {
                    $primary = 'comment_id';
                }

                if ($elastic->create($row[$primary]) === false) {
                    //because there is no reason to continue;
                    CommandLine::warning('No index server found. Aborting...');
                    return;
                }
            }

            $i += 100;
        } while ($i < $total);
    }
});

/**
 * CLI clear index
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-flush-sql', function ($request, $response) {
    CommandLine::system('Flushing SQL...');

    $database = SqlFactory::load($this->package('global')->service('sql-main'));

    //truncate all tables
    $tables = $database->getTables();
    foreach ($tables as $table) {
        $database->query('TRUNCATE TABLE `' . $table . '`;');
    }
});

/**
 * CLI populates database with dummy data
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-build-sql', function ($request, $response) {
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
});

/**
 * CLI populates database with dummy data
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-populate-sql', function ($request, $response) {
    CommandLine::system('Populating SQL...');

    $path = $this->package('global')->path('module');
    $folders = scandir($path, 0);

    foreach ($folders as $folder) {
        if ($folder === '.' || $folder === '..' || !is_dir($path . '/' . $folder)) {
            continue;
        }

        $file = $path . '/' . $folder . '/placeholder.sql';

        if (!file_exists($file)) {
            continue;
        }

        $query = file_get_contents($file);
        $this->package('global')->service('sql-main')->query($query);
    }
});

/**
 * CLI project installation
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-install', function ($request, $response) {
    //whether to ask questions
    $force = $request->hasStage('f') || $request->hasStage('force');

    //setup the configs
    if(!$request->hasStage('skip-configs')) {
        CommandLine::system('Setting up config files...');
        $cwd = $request->getServer('PWD');

        $configs = [
            'deploy',
            'services',
            'settings',
            'test',
            'version'
        ];

        foreach ($configs as $config) {
            $source = $cwd . '/config/' . $config . '.sample.php';
            $destination = $cwd . '/config/' . $config . '.php';

            if (!file_exists($source)) {
                continue;
            }

            if (file_exists($destination) && !$force) {
                $answer = CommandLine::input('Overwrite config/' . $config . '.php?(y)', 'y');
                if ($answer !== 'y') {
                    CommandLine::system('Skipping...');
                    continue;
                }
            }

            copy($source, $destination);
        }
    }

    //name
    $name = 'testing_db';
    if ($request->hasStage(0)) {
        $name = $request->getStage(0);
    }

    if (!$name) {
        if (!$force) {
            $name = CommandLine::input('What is the name of the SQL database to install?(testing_db)', 'testing_db');
        } else {
            $name = 'testing_db';
        }
    }

    //host
    $host = false;
    if ($request->hasStage('h')) {
        $host = $request->getStage('h');
    } else if ($request->hasStage('host')) {
        $host = $request->getStage('host');
    }

    if (!$host) {
        if (!$force) {
            $host = CommandLine::input('What is the SQL server address?(127.0.0.1)', '127.0.0.1');
        } else {
            $host = '127.0.0.1';
        }
    }

    //user
    $user = false;
    if ($request->hasStage('u')) {
        $user = $request->getStage('u');
    } else if ($request->hasStage('user')) {
        $user = $request->getStage('user');
    }

    if (!$user) {
        if (!$force) {
            $user = CommandLine::input('What is the SQL server user name?(root)', 'root');
        } else {
            $user = 'root';
        }
    }

    //pass
    $pass = false;
    if ($request->hasStage('p')) {
        $pass = $request->getStage('p');
    } else if ($request->hasStage('password')) {
        $pass = $request->getStage('password');
    }

    if (!$pass) {
        if (!$force) {
            $pass = CommandLine::input('What is the SQL server password?(enter for none)', '');
        } else {
            $pass = '';
        }
    }

    //services
    if(!$request->hasStage('skip-configs')) {
        $contents = file_get_contents($cwd . '/config/services.php');
        $contents = str_replace('dbname=salaaap_v6', 'dbname=' . $name, $contents);
        $contents = str_replace(':host=127.0.0.1', ':host='.$host, $contents);
        $contents = str_replace("'root', ''", "'" . $user . "', '" . $pass . "'", $contents);

        if (!$force) {
            if (strpos($contents, '<AWS TOKEN>') !== false) {
                $awsToken = CommandLine::input('What is the AWS S3 token?(enter to skip)', '<AWS TOKEN>');
                $contents = str_replace('<AWS TOKEN>', $awsToken, $contents);
            }

            if (strpos($contents, '<AWS SECRET>') !== false) {
                $awsSecret = CommandLine::input('What is the AWS S3 secret?(enter to skip)', '<AWS SECRET>');
                $contents = str_replace('<AWS SECRET>', $awsSecret, $contents);
            }

            if (strpos($contents, '<S3 BUCKET>') !== false) {
                $awsBucket = CommandLine::input('What is the AWS S3 bucket?(enter to skip)', '<S3 BUCKET>');
                $contents = str_replace('<S3 BUCKET>', $awsBucket, $contents);
            }

            if (strpos($contents, '<EMAIL ADDRESS>') !== false) {
                $mailAddress = CommandLine::input('What is the notifier email address?(enter to skip)', '<EMAIL ADDRESS>');
                $contents = str_replace('<EMAIL ADDRESS>', $mailAddress, $contents);
            }

            if (strpos($contents, '<EMAIL PASSWORD>') !== false) {
                $mailPassword = CommandLine::input('What is the notifier email password?(enter to skip)', '<EMAIL PASSWORD>');
                $contents = str_replace('<EMAIL PASSWORD>', $mailAddress, $contents);
            }

            if (strpos($contents, '<GOOGLE CAPTCHA TOKEN>') !== false) {
                $captchaToken = CommandLine::input('What is the Google Captcha token?(enter to skip)', '<GOOGLE CAPTCHA TOKEN>');
                $contents = str_replace('<GOOGLE CAPTCHA TOKEN>', $captchaToken, $contents);
            }

            if (strpos($contents, '<GOOGLE CAPTCHA SECRET>') !== false) {
                $captchaSecret = CommandLine::input('What is the Google Captcha secret?(enter to skip)', '<GOOGLE CAPTCHA SECRET>');
                $contents = str_replace('<GOOGLE CAPTCHA SECRET>', $captchaSecret, $contents);
            }
        }

        file_put_contents($cwd . '/config/services.php', $contents);
    }

    //SQL
    CommandLine::system('Setting up SQL...');

    //connection
    $build = SqlFactory::load(new PDO('mysql:host=' . $host, $user, $pass));
    $exists = $build->query("SHOW DATABASES LIKE '" . $name . "';");

    $continue = false;
    if (!empty($exists) && !$force) {
        $answer = CommandLine::input('This will override your existing database. Are you sure?(y)', 'y');
        if ($answer === 'y') {
            $continue = true;
        }
    }

    if ($continue || $force) {
        CommandLine::system('Installing Database...');

        $build->query('CREATE DATABASE IF NOT EXISTS `' . $name . '`;');

        $database = SqlFactory::load(new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass));

        //drop all tables
        $tables = $database->getTables();
        foreach ($tables as $table) {
            $database->query('DROP TABLE `' . $table . '`;');
        }
    }

    //now run the update
    $this->trigger('project-update', $request, $response);

    //now populate
    $populateSql = false;
    if ($request->hasStage('populate-sql')) {
        $populateSql = $request->getStage('populate-sql');
    }

    if ($populateSql === false && !$force) {
        $answer = CommandLine::input('Do you want to populate the SQL database?(y)', 'y');
        if ($answer === 'y') {
            $populateSql = true;
        }
    }

    if ($populateSql) {
        $this->trigger('project-populate-sql', $request, $response);
    }

    if ($this->package('global')->service('elastic-main')) {
        $populateElastic = false;
        if ($request->hasStage('populate-elastic')) {
            $populateElastic = $request->getStage('populate-elastic');
        }

        if ($populateElastic === false && !$force) {
            CommandLine::warning('Make sure ElasticSearch service is running or enter (n) for the following question.');
            $answer = CommandLine::input('Do you want to populate the ElasticSearch Index?(y)', 'y');
            if ($answer === 'y') {
                $populateElastic = true;
            }
        }

        if ($populateElastic) {
            $this->trigger('project-flush-elastic', $request, $response);
            $this->trigger('project-map-elastic', $request, $response);
            $this->trigger('project-populate-elastic', $request, $response);
        }
    }
});

/**
 * CLI project update
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-update', function ($request, $response) {
    //setup the configs
    CommandLine::system('Updating project...');

    $version = Installer::install();

    CommandLine::success('Updated to v' . $version);
});

/**
 * CLI project server
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('project-server', function ($request, $response) {
    //setup the configs
    CommandLine::system('Starting Server...');
    CommandLine::info('Press Ctrl-C to quit.');

    $cwd = $request->getServer('PWD');
    system('php -S 127.0.0.1:8888 -t ' . $cwd . '/public');
});
