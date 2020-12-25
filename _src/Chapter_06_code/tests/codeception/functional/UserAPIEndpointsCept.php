<?php

$I = new FunctionalTester($scenario);
$I->wantTo('test unauthorised and forbidden user REST API endpoints');

$userFixtures = $I->getFixture('user');
$user = $userFixtures['admin'];
$userPassword = 'admin';

$I->amGoingTo('ensure I cannot search for users');
$I->sendGET('users/search/'.$user['id']);
$I->seeResponseCodeIs(401);

$I->amGoingTo('ensure I cannot list any user');
$I->sendHEAD('users/'.$user['id']);
$I->seeResponseCodeIs(401);

$I->amGoingTo('ensure I cannot update any user');
$I->sendPUT('users/'.$user['id']);
$I->seeResponseCodeIs(401);

$I->wantTo('ensure the disabled actions are not usable');

$I->amGoingTo('ensure list users is not allowed');
$I->amHttpAuthenticated($user['username'], $userPassword);
$I->sendHEAD('users');
$I->seeResponseCodeIs(405);
$I->sendGET('users');
$I->seeResponseCodeIs(405);

$I->amGoingTo('ensure create user is not allowed');
$I->sendPOST('users');
$I->seeResponseCodeIs(405);

$I->amGoingTo('ensure deleting my own user is not allowed');
$I->sendDELETE('users/'.$user['id']);
$I->seeResponseCodeIs(405);

$I->amGoingTo('ensure delete user is not allowed');
$I->sendDELETE('users/'.($user['id']+1));
$I->seeResponseCodeIs(405);
