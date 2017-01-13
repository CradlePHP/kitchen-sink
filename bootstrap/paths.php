<?php //-->
return function ($request, $response) {
    $root = dirname(__DIR__);

    $paths = [
        'root' => $root,
        'boostrap' => $root . '/bootstrap',
        'config' => $root . '/config',
        'module' => $root . '/module',
        'compiled' => $root . '/compiled',
        'public' => $root . '/public',
        'upload' => $root . '/public/upload',
        'template' => $root . '/template',
        'vendor' => $root . '/vendor'
    ];

    //to make things faster, let's cache what is requested
    $cache = [];

    //create some global methods
    $this->package('global')

    /**
     * Sets or gets a path
     *
     * @param *string     $key         The name of the path
     * @param string|null $destination The path if you want to set it
     *
     * @return Package|string|null
     */
    ->addMethod('path', function ($key, $destination = null) use (&$paths) {
        if (is_string($destination)) {
            $paths[$key] = $destination;
            return $this;
        }

        if (isset($paths[$key])) {
            return $paths[$key];
        }

        return null;
    })

    /**
     * Gets a configuration file
     *
     * @param *string $key The name of the configuration path
     *
     * @return mixed
     */
    ->addMethod('config', function ($path, $key = null) use (&$cache) {
        //is it already in memory?
        if (!isset($cache[$path])) {
            $config = $this->path('config');
            $file = $config.'/' . $path . '.php';

            if (!file_exists($file)) {
                $cache[$path] = [];
            } else {
                //get the data and cache
                $cache[$path] = include($file);
            }
        }

        if (is_null($key)) {
            //return the data
            return $cache[$path];
        }

        if (!isset($cache[$path][$key])) {
            return null;
        }

        return $cache[$path][$key];
    });
};
