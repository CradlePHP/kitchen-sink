<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

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
