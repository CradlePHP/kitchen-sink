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
 * CLI project installation
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
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
};
