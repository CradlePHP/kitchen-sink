<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
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
$cradle->get('/developer/signup', function ($request, $response) {
    //redirect
    $redirect = urlencode('/developer/app/search');
    cradle('global')->redirect('/signup?redirect_uri='.$redirect);
});

/**
 * Render the Login Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/developer/login', function ($request, $response) {
    //redirect
    $redirect = urlencode('/developer/app/search');
    cradle('global')->redirect('/login?redirect_uri='.$redirect);
});

/**
 * Render the Account Page
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->get('/developer/account', function ($request, $response) {
    //redirect
    $redirect = urlencode('/developer/app/search');
    cradle('global')->redirect('/account?redirect_uri='.$redirect);
});
