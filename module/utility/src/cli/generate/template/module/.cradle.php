<?php //-->
include_once __DIR__ . '/src/events.php';

use Cradle\Module\{{camel name 1}}\Service;
use Cradle\Module\Utility\ServiceFactory;
use Cradle\Module\Utility\Installer;

Installer::register(__DIR__ . '/install');
ServiceFactory::register('{{name}}', Service::class);
