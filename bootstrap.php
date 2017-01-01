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
    ->preprocess(include('bootstrap/timezone.php'))
    ->preprocess(include('bootstrap/session.php'))
    ->preprocess(include('bootstrap/i18n.php'))
    ->preprocess(include('bootstrap/handlebars.php'))

    //add packages here
    ->register('cblanquera/cradle-handlebars')
    ->register('cblanquera/cradle-queue')
    ->register('cblanquera/cradle-csrf')
    ->register('cblanquera/cradle-captcha')
    ->register('cradlephp/sink-faucet')

    ->register('/module/oauth')
    ->register('/module/profile')
    ->register('/module/utility');
