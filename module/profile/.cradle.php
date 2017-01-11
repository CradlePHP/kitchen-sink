<?php //-->
include_once __DIR__ . '/src/events.php';

use Cradle\Module\Profile\Service as ProfileService;
use Cradle\Module\Utility\ServiceFactory;

ServiceFactory::register('profile', ProfileService::class);
