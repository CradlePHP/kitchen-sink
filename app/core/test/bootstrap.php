<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
$cradle = require_once __DIR__.'/../../../bootstrap.php';

$cradle
    //use the test configurations instead.
    ->preprocess(function($request, $response) {
        $services = $this->package('global')->config('test');

        //create a sudo method
        $this->package('global')->addMethod('service', function($name) use (&$services) {
            if(!isset($services[$name])) {
                return null;
            }

            return $services[$name];
        });
    })
    //next we want to install the db
    ->preprocess(function($request, $response) {
        $schema = file_get_contents(__DIR__ . '/../schema.sql');
        $database = Cradle\Sql\SqlFactory::load($this->package('global')->service('sql-main'));
        $database->query($schema);
    })
    //prepare will call the preprocssors
    ->prepare();
