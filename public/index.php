<?php //-->

include(__DIR__.'/../bootstrap.php');

return cradle()
    ->register('/app/api')
    ->register('/app/www')
    ->render();
