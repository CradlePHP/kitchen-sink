<?php //-->
$I = new AcceptanceTester($scenario);

$I->wantTo('Update Account Settings');
$I->amOnPage('/');

$I->expect('that I am logged out');
$I->see('Login');

// login
$I->amGoingTo('go to the login page');
$I->click('Login');
$I->see('Login');

$I->amGoingTo('login');
$I->fillField('auth_slug', 'newuser@gmail.com');
$I->fillField('auth_password', 'password');
$I->click('form button');

$I->expect('Homepage');
$I->seeInCurrentUrl('/');
$I->see('Cradle PHP');

$I->amGoingTo('go to the account settings page.');
$I->click('Account Settings');

$I->expect('account settings page.');
$I->see('Account Settings');

// update the form
$I->amGoingTo('update my account settings');
$I->fillField('profile_name', 'New User Updated');
$I->click('form button');

$I->see('Update Successful');
