<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\App\Core\Model\App;
use Cradle\App\Core\Model\Auth;
use Cradle\App\Core\Model\Profile;
use Cradle\App\Core\Model\Session;
use Cradle\App\Core\Service;

use Cradle\Http\Request;
use Cradle\Http\Response;
use Cradle\CommandLine\Index as CommandLine;

/**
 * App Model
 */
$cradle->package('/app/core')->addMethod('model', function($name) {
    static $model = [];

    if(!isset($model[$name])) {
        $service = cradle()->resolve(Service::class, cradle());

        switch($name) {
            case 'app':
                $model[$name] = cradle()->resolve(App::class, $service);
                break;
            case 'auth':
                $model[$name] = cradle()->resolve(Auth::class, $service);
                break;
            case 'profile':
                $model[$name] = cradle()->resolve(Profile::class, $service);
                break;
            case 'session':
                $model[$name] = cradle()->resolve(Session::class, $service);
                break;
        }
    }

    return $model[$name];
});

/**
 * CLI job - cradle app/core job auth-verify "?auth_slug=<email>"
 *
 * @param *string $path
 * @param array   $data
 * @param array   $partial
 *
 * @return string
 */
$cradle->on('app-core-job', function ($cwd, $args) {
    if(!isset($args[3])) {
        CommandLine::error('Not enough arguments. Usage: cradle package app/core job event json');
    }

    $request = new Request();
    $request->load();

    $response = new Response();
    $response->load();

    $event = $args[3];

    $data = [];
    if(strpos($args[4], '?') === 0) {
        parse_str(substr($args[4], 1), $data);
    } else {
        $data = json_decode($args[4], true);
    }

    $request->setStage($data);

    $this->trigger($event, $request, $response);

    CommandLine::info('Results:');
    print_r($response->get('json'));
});

/**
 * CLI queue - cradle app/core queue auth-verify "?auth_slug=<email>"
 *
 * @param *string $path
 * @param array   $data
 * @param array   $partial
 *
 * @return string
 */
$cradle->on('app-core-queue', function ($cwd, $args) {
    if(!isset($args[3])) {
        CommandLine::error('Not enough arguments. Usage: cradle package app/core queue event json');
    }

    $event = $args[3];

    $data = [];
    if(strpos($args[4], '?') === 0) {
        parse_str(substr($args[4], 1), $data);
    } else {
        $data = json_decode($args[4], true);
    }

    if(!cradle('global')->queue($event, $data)) {
        CommandLine::error('Unable to queue, check config/services.php for correct connection information.');
    }
});

/**
 * Generic template method for app/wwww
 *
 * @param *string $path
 * @param array   $data
 * @param array   $partial
 *
 * @return string
 */
$cradle->package('/app/core')->addMethod('template', function ($path, array $data = [], $partials = []) {
    // get the root directory
    $root = __DIR__ . '/src/template/';

    //render
    $handlebars = cradle()->package('global')->handlebars();

    // check for partials
    if(!is_array($partials)) {
        $partials = [$partials];
    }

    foreach ($partials as $partial) {
        //Sample: product_comment => product/_comment
        //Sample: flash => _flash
        if(strpos($partial, '.') === false) {
            $partial .= '.html';
        }

        $file = str_replace('_', '/_', $partial);

        if(strpos($file, '_') === false) {
            $file = '_' . $file;
        }

        // register the partial
        $handlebars->registerPartial($partial, file_get_contents($root . $file));
    }

    // set the main template
    if(strpos($path, '.') === false) {
        $path .= '.html';
    }

    $template = $handlebars->compile(file_get_contents($root . $path));
    return $template($data);
});

include_once(__DIR__ . '/src/job/app.php');
include_once(__DIR__ . '/src/job/auth.php');
include_once(__DIR__ . '/src/job/profile.php');
include_once(__DIR__ . '/src/job/session.php');
