<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

require_once __DIR__.'/../../../../bootstrap.php';

cradle()
    //use the test configurations instead.
    ->preprocess(function($request, $response) {
        echo 'Overide global->service with test configs...' . PHP_EOL;

        //create a sudo method
        $this->package('global')->addMethod('service', function($name) {
            static $services = null;

            if(is_null($services)) {
                $services = cradle()->package('global')->config('test');
            }

            if(!isset($services[$name])) {
                return null;
            }

            return $services[$name];
        });
    })
    // we want to install the db
    ->preprocess(function($request, $response) {
        // we want to install the db
        $request->setStage('force', true);
        $request->setStage(0, 'testing_db');
        $this->trigger('app-core-build-database', $request, $response);

        // we want to build the index
        if($this->package('global')->service('index-main')) {
            $this->trigger('app-core-clear-index', $request, $response);

            $this->trigger('app-core-map-index', $request, $response);

            $this->trigger('app-core-build-index', $request, $response);
        }

        // we want to clear the cache
        if($this->package('global')->service('cache-main')) {
            $this->trigger('app-core-clear-cache', $request, $response);
        }
    })
    //prepare will call the preprocssors
    ->prepare();
