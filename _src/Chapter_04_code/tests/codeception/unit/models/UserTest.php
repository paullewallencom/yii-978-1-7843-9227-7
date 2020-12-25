<?php

namespace tests\codeception\unit\models;

use app\models\User;
use app\tests\codeception\unit\fixtures\UserFixture;
use yii\base\InvalidParamException;
use yii\codeception\TestCase;
use Yii;

class UserTest extends TestCase
{

    /** @var User */
    private $_user = null;

    public function globalFixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }

    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@app/tests/codeception/unit/fixtures/data/userModels.php'
            ]
        ];
    }

    protected function setUp()
    {
        parent::setUp();
        $this->_user = new User;
    }

    public function testValidateReturnsFalseIfParametersAreNotSet()
    {
        $this->assertFalse($this->_user->validate());
    }

    public function testValidateReturnsTrueIfParametersAreSet()
    {
        $this->_user->attributes = [
            'username' => 'a valid username',
            'password' => 'a valid password',
            'authkey' => 'a valid authkey'
        ];

        $this->assertTrue($this->_user->validate());
    }

    /* getId() */

    public function testGetIdReturnsTheExpectedId()
    {
        $expectedId = 123;
        $this->_user->id = $expectedId;

        $this->assertEquals($expectedId, $this->_user->getId());
    }

    /* getAuthKey() */

    public function testGetAuthKeyReturnsTheExpectedAuthKey()
    {
        $expectedAuthkey = 'valid authkey';
        $this->_user->authkey = $expectedAuthkey;

        $this->assertEquals($expectedAuthkey, $this->_user->getAuthKey());
    }

    /* findIdentity() */

    /**
     * @dataProvider validFixturesKeysDataProvider
     */
    public function testFindIdentityReturnsTheExpectedObject($fixtureKey) {
        $expectedAttrs = $this->user[$fixtureKey];

        /** @var User $user */
        $user = User::findIdentity($expectedAttrs['id']);

        $this->assertNotNull($user);
        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
        $this->assertEquals($expectedAttrs['id'], $user->id);
        $this->assertEquals($expectedAttrs['username'], $user->username);
        $this->assertEquals($expectedAttrs['password'], $user->password);
        $this->assertEquals($expectedAttrs['authkey'], $user->authkey);
    }

    /**
     * @dataProvider nonExistingIdsDataProvider
     */
    public function testFindIdentityReturnsNullIfUserIsNotFound(
        $invalidId
    ) {
        $this->assertNull(User::findIdentity($invalidId));
    }

    public function nonExistingIdsDataProvider() {
        return [[-1], [null], [300]];
    }

    /* findIdentityByAccessToken() */

    /**
     * @expectedException yii\base\NotSupportedException
     */
    public function testFindIdentityByAccessTokenReturnsTheExpectedObject()
    {
        User::findIdentityByAccessToken('anyAccessToken');
    }

    /* findByUsername() */

    /**
     * @dataProvider validFixturesKeysDataProvider
     */
    public function testFindByUsernameReturnsTheExpectedObject($fixtureKey)
    {
        $expectedUsername = $this->user[$fixtureKey]['username'];

        /** @var User $user */
        $user = User::findByUsername($expectedUsername);

        $this->assertInstanceOf('yii\web\IdentityInterface', $user);
        $this->assertEquals($expectedUsername, $user->username);
    }

    /**
     * @dataProvider nonExistingUsernamesDataProvider
     */
    public function testFindByUsernameReturnsNullIfUserNotFound(
        $invalidUsername
    ) {
        $this->assertNull(User::findByUsername($invalidUsername));
    }

    public function nonExistingUsernamesDataProvider() {
        return [[3], [-1], [null], ['not found']];
    }

    /* validateAuthkey() */

    public function testValidateAuthkeyReturnsFalseIfAuthkeyIsDifferent() {
        $this->_user->authkey = 'some auth key';

        $this->assertFalse($this->_user->validateAuthKey('wrong auth key'));
    }

    public function testValidateAuthkeyReturnsTrueIfAuthkeyIsEqual() {
        $expectedAuthkey = 'valid auth key';
        $this->_user->authkey = $expectedAuthkey;

        $this->assertTrue($this->_user->validateAuthKey($expectedAuthkey));
    }

    /* validatePassword() */

    public function testValidatePasswordReturnsTrueIfPasswordIsCorrect() {
        $expectedPassword = 'valid password';
        $this->_mockYiiSecurity($expectedPassword);

        $this->_user->password = Yii::$app->getSecurity()->generatePasswordHash($expectedPassword);

        $this->assertTrue($this->_user->validatePassword($expectedPassword));
    }

    /**
     * @expectedException yii\base\InvalidParamException
     */
    public function testValidatePasswordThrowsInvalidParamExceptionIfPasswordIsIncorrect() {
        $password = 'some password';
        $otherPassword = 'some other password';
        $this->_mockYiiSecurity($password, $otherPassword);

        $this->_user->password = $password;
        $this->_user->validatePassword($otherPassword);
    }

    public function validFixturesKeysDataProvider()
    {
        return [
            ['user_basic'], ['user_accessToken'], ['user_id']
        ];
    }
}
