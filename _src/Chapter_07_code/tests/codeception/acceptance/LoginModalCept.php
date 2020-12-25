<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('test modal login');

$I->amOnPage(Yii::$app->homeUrl);
$I->seeLink('Login');
$I->dontSeeLink('Logout');

$I->amGoingTo('test the login with empty credentials');
$I->click('Login');
$I->waitForElementVisible('.modal');
$I->fillField('#loginform-username', '');
$I->fillField('#loginform-password', '');
$I->click('#login-form button');
$I->see('Error', '.alert .error');

$I->amGoingTo('test the login with wrong credentials');
$I->fillField('#loginform-username', 'admin');
$I->fillField('#loginform-password', 'wrong password');
$I->click('#login-form button');
$I->see('Error', '.alert .error');

$I->amGoingTo('test the login with valid credentials');
$I->fillField('#loginform-username', 'admin');
$I->fillField('#loginform-password', 'admin');
$I->click('#login-form button');
$I->wait(3);
$I->dontSeeElement('.modal');
$I->seeLink('Logout');
$I->dontSeeLink('Login');

$I->amGoingTo('test logout');
$I->click('Logout');
$I->dontSeeLink('Logout');
$I->seeLink('Login');
