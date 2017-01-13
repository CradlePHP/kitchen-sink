<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
*/

/**
 * Render admin page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('render-admin-page', function ($request, $response) {
    $content = cradle('/app/admin')->template(
        '_page',
        array(
            'page' => $response->getPage(),
            'results' => $response->getResults(),
            'content' => $response->getContent()
        ),
        array(
            'head',
            'foot'
        )
    );

    $response->setContent($content);
});
