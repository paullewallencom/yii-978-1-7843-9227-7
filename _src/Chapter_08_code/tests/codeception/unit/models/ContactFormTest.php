<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;

class ContactFormTest extends TestCase
{
    use Specify;

    protected function setUp()
    {
        parent::setUp();
        Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_message.eml';
        };
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testContactReturnsFalseIfModelDoesNotValidate()
    {
        $model = $this->getMock('app\models\ContactForm', ['validate']);
        $model->expects($this->any())
              ->method('validate')
              ->will($this->returnValue(false));

        $this->specify('contact should not send', function () use (&$model) {
            expect($model->contact(null), false);
            expect($model->contact('admin@example.com'), false);
        });

    }

    public function testContactReturnsTheCorrectEmail()
    {
        $model = $this->getMock('app\models\ContactForm', ['validate']);
        $model->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));

        $model->attributes = [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'subject' => 'very important letter subject',
            'body' => 'body of current message',
        ];

        $model->contact('admin@example.com');

        $this->specify('email should be sent', function () {
            expect('email file should exist', file_exists($this->getMessageFile()))->true();
        });

        $this->specify('message should contain correct data', function () use ($model) {
            $emailMessage = file_get_contents($this->getMessageFile());

            expect('email should contain user name', $emailMessage)->contains($model->name);
            expect('email should contain sender email', $emailMessage)->contains($model->email);
            expect('email should contain subject', $emailMessage)->contains($model->subject);
            expect('email should contain body', $emailMessage)->contains($model->body);
        });

        unlink($this->getMessageFile());
    }

    private function getMessageFile()
    {
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_message.eml';
    }

}
