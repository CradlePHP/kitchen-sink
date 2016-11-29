<?php //-->
/**
 * This file is part of the Custom Project.
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
    $root = __DIR__ . '/../template/';

    //render
    $handlebars = cradle('global')->handlebars();

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
