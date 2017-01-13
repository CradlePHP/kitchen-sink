<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
*/

/**
 * Generic template method for app/admin
 *
 * @param *string $path
 * @param array   $data
 * @param array   $partial
 *
 * @return string
 */
$cradle->package('/app/admin')->addMethod('template', function (
    $path,
    array $data = array(),
    $partials = array()
) {

    // get the root directory
    $root = __DIR__ . '/template/';

    //render
    $handlebars = cradle('global')->handlebars();

    // check for partials
    if (!is_array($partials)) {
        $partials = array($partials);
    }

    foreach ($partials as $partial) {
        //Sample: product_comment => product/_comment
        //Sample: flash => _flash
        $file = str_replace('_', '/_', $partial) . '.html';

        if (strpos($file, '_') === false) {
            $file = '_' . $file;
        }

        // register the partial
        $handlebars->registerPartial($partial, file_get_contents($root . $file));
    }

    // set the main template
    $template = $handlebars->compile(file_get_contents($root . $path . '.html'));
    return $template($data);
});
