<?php //-->
return function($request, $response) {
    //get handlebars
    $handlebars = $this->package('global')->handlebars();

    //add cache folder
    //$handlebars->setCache(__DIR__.'/../compiled');
};
