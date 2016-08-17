<?php //-->
require_once 'vendor/autoload.php';

//use the cradle function
Cradle\Framework\Decorator::DECORATE;

return cradle()
    //add bootstrap here
    ->preprocess(include('bootstrap/paths.php'))
    ->preprocess(include('bootstrap/debug.php'))
    ->preprocess(include('bootstrap/errors.php'))
    ->preprocess(include('bootstrap/services.php'))
    ->preprocess(include('bootstrap/i18n.php'))
    ->preprocess(include('bootstrap/timezone.php'))
    ->preprocess(include('bootstrap/session.php'))

    //add packages here
    ->register('cblanquera/cradle-handlebars')
    ->register('cblanquera/cradle-schema')
    ->register('cblanquera/cradle-auth')
    ->register('cblanquera/cradle-file')
    ->register('cblanquera/cradle-mail')
    ->register('cblanquera/cradle-queue')
    ->register('cblanquera/cradle-csrf')
    ->register('cblanquera/cradle-captcha')
    ->register('/app/schema')
    ->register('/app/jobs');
