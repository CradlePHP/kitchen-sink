<?php //-->
include_once __DIR__ . '/src/App/events.php';
include_once __DIR__ . '/src/Auth/events.php';
include_once __DIR__ . '/src/Session/events.php';

use Cradle\Module\Oauth\App\Service as AppService;
use Cradle\Module\Oauth\Auth\Service as AuthService;
use Cradle\Module\Oauth\Session\Service as SessionService;

use Cradle\Module\Utility\ServiceFactory;
use Cradle\Module\Utility\Installer;

Installer::register(__DIR__ . '/install');
ServiceFactory::register('app', AppService::class);
ServiceFactory::register('auth', AuthService::class);
ServiceFactory::register('session', SessionService::class);
