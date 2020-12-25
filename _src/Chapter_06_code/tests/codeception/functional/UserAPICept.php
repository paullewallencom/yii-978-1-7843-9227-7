<?php

$I = new FunctionalTester($scenario);
$I->wantTo('test the user REST API');

$userFixtures = $I->getFixture('user');
$user = $userFixtures['basic'];
$userPassword = 'something';

$I->amGoingTo('authenticate to search for my own user');
$I->amHttpAuthenticated($user['username'], $userPassword);
$I->sendGET('users/search/'.$user['username']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains($user['username']);
$I->seeResponseContains('password');
$I->seeResponseContains('id');
$userId = $I->grabDataFromResponseByJsonPath('$.id')[0];

$I->amGoingTo('ensure OPTIONS works');
$I->sendOPTIONS('users');
$I->seeResponseCodeIs(200);
$I->seeHttpHeader('Allow');

$I->amGoingTo('fetch my own information');
$I->sendGET('users/'.$userId);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains($user['username']);
$I->seeResponseContains('password');

$I->amGoingTo('ensure viewing someone else is forbidden');
$I->sendGET('users/'.($user['id']+1));
$I->seeResponseCodeIs(403);

$I->amGoingTo('ensure changing my own username is forbidden');
$I->sendPUT('users/'.$userId, ['username' => 'atoll']);
$I->seeResponseCodeIs(403);

$I->amGoingTo('authenticate to update my own password');
$newPassword = 'something else';
$I->sendPUT(
    'users/' . $userId,
    ['password' => $newPassword, 'authkey' => 'updated']
);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->seeResponseCodeIs(200);

$I->amGoingTo('authenticate to check my new password works');
$I->amHttpAuthenticated($user['username'], $newPassword);
$I->sendHEAD('users/'.$userId);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);
