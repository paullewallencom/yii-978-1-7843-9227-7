<?php

namespace tests\codeception\unit\models;

use app\models\Dog;
use yii\codeception\TestCase;
use Yii;

class DogTest extends TestCase
{
    public function testAgeIsRecordedInDogYears()
    {
        $age = 8;
        $dog = new Dog;
        $dog->age = $age;

        $this->assertEquals(
            $age * Dog::AGE_MULTIPLIER,
            $dog->age
        );
    }

    public function testGetFullnameReturnsTheCorrectValue()
    {
        $dog = new Dog;
        $dog->name = 'Fido';
        $dog->familyName = 'Smith';

        $this->assertEquals(
            $dog->name . ' ' . $dog->familyName,
            $dog->fullname
        );
    }

    /**
     * @expectedException yii\base\InvalidCallException
     */
    public function testSetFullnameThrowsException()
    {
        $dog = new Dog;
        $dog->name = 'Fido';
        $dog->familyName = 'Smith';

        $dog->fullname = 'Something Else';
    }
}