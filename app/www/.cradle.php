<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Render Blank Web Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('render-www-blank', function($request, $response) {
    $content = cradle('/app/www')->template('_blank', [
        'page' => $response->getPage(),
        'results' => $response->getResults(),
        'content' => $response->getContent()
    ]);

    $response->setContent($content);
});

/**
 * Render Web Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('render-www-page', function($request, $response) {
    //protocol
    $protocol = 'http';
    if($_SERVER['SERVER_PORT'] != 80) {
        $protocol = 'https';
    }

    //url and base
    $base = $url = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if(strpos($url, '?') !== false) {
        $base = substr($url, 0, strpos($url, '?') + 1);
    }

    $response->addMeta('url', $url)->addMeta('base', $base);

    //path
    $path = $request->getPath('string');
    if(strpos($path, '?') !== false) {
        $path = substr($path, 0, strpos($path, '?'));
    }

    $response->addMeta('path', $path);

    $content = cradle('/app/www')->template(
        '_page',
        [
            'page' => $response->getPage(),
            'results' => $response->getResults(),
            'content' => $response->getContent(),
            'i18n' => $request->getSession('i18n')
        ],
        [
            'head',
            'foot'
        ]
    );

    $response->setContent($content);
});

/**
 * 404 and 500 page
 *
 * @param Request $request
 * @param Response $response
 * @param Throwable $error
 */
$cradle->error(function($request, $response, $error) {
    return;
    //if this error has already been handled
    if($response->hasContent()) {
        return;
    }

    //if it was a call for an actual file
    $path = $request->getPath('string');
    if(preg_match('/\.[a-zA-Z0-9]{1,4}$/', $path)) {
        return;
    }

    if($response->getCode() === 404) {
        $body = cradle()->package('/app/www')->template('404');
        $class = 'page-404 page-error';
        $title = cradle('global')->translate('Oops...');

        //Set Content
        $response
            ->setPage('title', $title)
            ->setPage('class', $class)
            ->setContent($body);

        $this->trigger('render-web-page', $request, $response);

        return true;
    }

    $config = cradle('global')->config('settings');
    if($config['environment'] === 'production' && $response->getCode() === 500) {
        $body = cradle()->package('/app/www')->template('500');
        $class = 'page-500 page-error';
        $title = cradle('global')->translate('Error');

        //Set Content
        $response
            ->setPage('title', $title)
            ->setPage('class', $class)
            ->setContent($body);

        $this->trigger('render-web-page', $request, $response);

        return true;
    }
});

/**
* Generic template method for app/wwww
*
* @param *string $path
* @param array   $data
* @param array   $partial
*
* @return string
*/
$cradle->package('/app/www')->addMethod('template', function ($path, array $data = [], $partials = []) {
   // get the root directory
   $root = __DIR__ . '/src/template/';

   //render
   $handlebars = cradle()->package('global')->handlebars();

   // check for partials
   if(!is_array($partials)) {
       $partials = [$partials];
   }

   foreach ($partials as $partial) {
       //Sample: product_comment => product/_comment
       //Sample: flash => _flash
       $file = str_replace('_', '/_', $partial) . '.html';

       if(strpos($file, '_') === false) {
           $file = '_' . $file;
       }

       // register the partial
       $handlebars->registerPartial($partial, file_get_contents($root . $file));
   }

   // set the main template
   $template = $handlebars->compile(file_get_contents($root . $path . '.html'));
   return $template($data);
});

//include the other routes
include_once(__DIR__ . '/src/controller/static.php');
include_once(__DIR__ . '/src/controller/auth.php');
