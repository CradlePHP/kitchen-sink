<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

//include the other routes
include_once __DIR__ . '/src/controller/rest/auth.php';
include_once __DIR__ . '/src/controller/rest/profile.php';

include_once __DIR__ . '/src/controller/dialog/auth.php';
include_once __DIR__ . '/src/controller/developer/auth.php';
include_once __DIR__ . '/src/controller/developer/app.php';

//include globals, events, methods
include_once __DIR__ . '/src/package/methods.php';
include_once __DIR__ . '/src/package/events.php';
