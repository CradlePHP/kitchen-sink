<?php //-->
/**
 * This file is part of the Dealcha Project.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Render the Signup Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/{{app}}/signup', function($request, $response) {
    //redirect
    $redirect = urlencode('/{{app}}');
    cradle('global')->redirect('/signup?redirect_uri='.$redirect);
});

/**
 * Render the Login Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/{{app}}/login', function($request, $response) {
    //redirect
    $redirect = urlencode('/{{app}}');
    cradle('global')->redirect('/login?redirect_uri='.$redirect);
});

/**
 * Render the Account Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/{{app}}/account', function($request, $response) {
    //redirect
    $redirect = urlencode('/{{app}}');
    cradle('global')->redirect('/account?redirect_uri='.$redirect);
});
