<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
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
$cradle->on('render-www-blank', function ($request, $response) {
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
$cradle->on('render-www-page', function ($request, $response) {
    //protocol
    $protocol = 'http';
    if ($_SERVER['SERVER_PORT'] != 80) {
        $protocol = 'https';
    }

    //url and base
    $base = $url = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if (strpos($url, '?') !== false) {
        $base = substr($url, 0, strpos($url, '?') + 1);
    }

    $response->addMeta('url', $url)->addMeta('base', $base);

    //path
    $path = $request->getPath('string');
    if (strpos($path, '?') !== false) {
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
