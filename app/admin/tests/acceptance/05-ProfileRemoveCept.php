<?php
$I = new AcceptanceTester($scenario);

$I->wantTo('Remove Profile');
$I->amOnPage('/');

$I->expect('that I am logged out');
$I->see('Login');

// login
$I->amGoingTo('go to the login page');
$I->click('Login');
$I->see('Login');

$I->amGoingTo('login');
$I->fillField('auth_slug', 'john@doe.com');
$I->fillField('auth_password', '123');
$I->click('form button');

$I->expect('Remove Profile');
$I->amOnPage('/admin/profile/search');
$I->expectTo('see profile list');
$I->see('Profiles');

$I->amGoingTo('search profile');
$I->fillField('q[]', 'newprofile@gmail.com');
$I->click('form button');

//remove profile
$I->click('.text-danger.remove');
