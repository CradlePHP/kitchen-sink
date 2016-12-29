<?php //-->
include_once __DIR__ . '/src/events.php';

use Cradle\Module\Profile\Service;
use Cradle\Module\Utility\ServiceFactory;

ServiceFactory::register('profile', Service::class);
