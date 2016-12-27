<?php //-->
/**
 * This file is part of the Salaaap Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

require_once __DIR__.'/bootstrap.php';

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
    ->preprocess(function($request, $response) {
        // we want to install the db
        $request->setStage('force', true);
        $this->trigger('project-build-sql', $request, $response);

        $this->trigger('project-populate-sql', $request, $response);

        // we want to build the index
        if($this->package('global')->service('elastic-main')) {
            $this->trigger('project-flush-elastic', $request, $response);

            $this->trigger('project-map-elastic', $request, $response);

            $this->trigger('project-populate-elastic', $request, $response);
        }

        // we want to clear the cache
        if($this->package('global')->service('redis-main')) {
            $this->trigger('project-flush-redis', $request, $response);
        }
    })
    //prepare will call the preprocssors
    ->prepare();
