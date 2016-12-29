<?php //-->

include(__DIR__.'/../bootstrap.php');

return cradle()
    ->register('/app/admin')
    ->register('/app/api')
    ->register('/app/www')
    ->render();
