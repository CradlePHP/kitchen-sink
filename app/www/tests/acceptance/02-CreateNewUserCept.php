<?php
$I = new AcceptanceTester($scenario);

$I->wantTo('Signup a new account.');
$I->amOnPage('/');

$I->amGoingTo('go to the signup page');
$I->click('Signup');
$I->see('Signup');

$I->amGoingTo('signup form');
$I->fillField('profile_name', 'New User');
$I->fillField('profile_email', 'newuser@gmail.com');
$I->fillField('auth_password', 'password');
$I->fillField('confirm', 'password');
$I->click('form button');

$I->expect('Login');
