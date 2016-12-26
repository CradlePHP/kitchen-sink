<?php //-->
return function($request, $response) {
    //case for test injections
    $host = $request->getServer('HTTP_HOST');

    //create some global methods
    $this->package('global')

    /**
     * Gets a service from config
     *
     * @param *string name
     *
     * @return mixed
     */
    ->addMethod('service', function($name) {
        static $services = null;

        if(is_null($services)) {
            $services = cradle()->package('global')->config('services');
        }

        if(!isset($services[$name])) {
            return null;
        }

        return $services[$name];
    });
};
