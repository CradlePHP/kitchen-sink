<?php
$I = new AcceptanceTester($scenario);

$I->wantTo('Search Profile');
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

$I->expect('Admin Profile Search');
$I->amOnPage('/admin/profile/search');
$I->expectTo('see profile list');
$I->see('Profiles');


$I->amGoingTo('search profile');
$I->fillField('q[]', 'john@doe.com');
$I->click('form button');

$I->expectTo('see the name');
$I->see('John Doe');
