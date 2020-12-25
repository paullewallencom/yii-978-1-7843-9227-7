<?php

namespace tests\codeception\unit\models;

use app\models\User;
use app\tests\codeception\unit\fixtures\UserFixture;
use yii\base\InvalidParamException;
use yii\codeception\TestCase;
use Codeception\Specify;
use Yii;

class UserTest extends TestCase
{
    use Specify;

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

    /* VALIDATION RULES */

    public function testValidationRules()
    {
        $this->specify(
            'user should validate if all attributes are set',
            function () {
                $this->_user->attributes = [
                    'username'=>'valid username',
                    'password'=>'valid password',
                    'authkey' =>'valid authkey'
                ];
                verify_that($this->_user->validate());
            }
        );

        $this->specify(
            'user should not validate if no attribute is set',
            function () {
                verify_not($this->_user->validate());
            }
        );

        $this->specify(
            'user with username too long should not validate',
            function () {
                $this->_user->username = 'this is a username longer than 24 characters';

                verify_not($this->_user->validate('username'));
                verify($this->_user->getErrors('username'))->notEmpty();
            }
        );

        $this->specify(
            'user with password too long should not validate',
            function () {
                $this->_user->password = 'this is an password longer than 128 characters this is an password longer than 128 characters this is an password longer than 128 characters';

                verify_not($this->_user->validate('password'));
                verify($this->_user->getErrors('password'))->notEmpty();
            }
        );

        $this->specify(
            'user with authkey too long should not validate',
            function () {
                $this->_user->authkey = 'this is an authkey longer than 255 characters this is an authkey longer than 255 characters this is an authkey longer than 255 characters this is an authkey longer than 255 characters this is an authkey longer than 255 characters this is an authkey longer than 255 characters';

                verify_not($this->_user->validate('authkey'));
                verify($this->_user->getErrors('authkey'))->notEmpty();
            }
        );
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

    /* setPassword() */

    public function testSetPasswordEncryptsThePasswordCorrectly()
    {
        $clearTextPassword = 'some password';
        $encryptedPassword = 'encrypted password';
        $this->_mockYiiSecurity($encryptedPassword, false, $clearTextPassword);

        $this->_user->setPassword($clearTextPassword);

        $this->assertNotEquals($clearTextPassword, $this->_user->password);
        $this->assertEquals($encryptedPassword, $this->_user->password);
    }

    public function testSetPasswordCallsGeneratePasswordHash()
    {
        $clearTextPassword = 'some password';

        $security = $this->getMock(
            'yii\base\Security',
            ['generatePasswordHash']
        );
        $security->expects($this->once())
            ->method('generatePasswordHash')
            ->with($this->equalTo($clearTextPassword));
        Yii::$app->set('security', $security);

        $this->_user->setPassword($clearTextPassword);
    }

    /**
     * Mocks the Yii Security module so we can make it return what we need
     *
     * @param string $expectedPassword the password used for encoding
     *                                 also used for validating if the second parameter is not set
     * @param mixed  $wrongPassword    if passed, validatePassword will throw exception InvalidParamException
     *                                 when presenting this pass
     * @param null $actualPassword     if passed it will set the actual password
     *                                 otherwise it will use the expected password
     */
    private function _mockYiiSecurity($expectedPassword, $wrongPassword = false, $actualPassword = null)
    {
        if ($actualPassword === null) {
            $actualPassword = $expectedPassword;
        }

        $security = $this->getMock(
            'yii\base\Security',
            ['validatePassword', 'generatePasswordHash']
        );
        if ($wrongPassword) {
            $security->expects($this->any())
                ->method('validatePassword')
                ->with($wrongPassword)
                ->willThrowException(new InvalidParamException());
        } else {
            $security->expects($this->any())
                ->method('validatePassword')
                ->with($actualPassword)
                ->willReturn(true);
        }
        $security->expects($this->any())
            ->method('generatePasswordHash')
            ->with($actualPassword)
            ->willReturn($expectedPassword);

        Yii::$app->set('security', $security);
    }
}
