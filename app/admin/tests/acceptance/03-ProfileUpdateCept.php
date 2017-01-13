<?php //-->
$I = new AcceptanceTester($scenario);

$I->wantTo('Update a Profile');
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

$I->expect('Update Profile');
$I->amOnPage('/admin/profile/update/1');

$I->amGoingTo('update a profile');
$I->fillField('profile_phone', '123456');
$I->click('form button');

$I->expect('Profile Updated');
$I->see('Profile was Updated');
