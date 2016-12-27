<?php //-->
include_once __DIR__ . '/src/events.php';

use Cradle\Module\{{capital name}}\Service;
use Cradle\Module\Utility\ServiceFactory;
use Cradle\Module\Utility\Installer;

Installer::register(__DIR__ . '/install');
ServiceFactory::register('{{name}}', Service::class);
