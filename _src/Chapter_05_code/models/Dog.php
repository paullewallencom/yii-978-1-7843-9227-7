<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Dog extends Model
{
    const AGE_MULTIPLIER = 7;

    /** @var string name of the dog */
    public $name;
    /** @var string family name of the dog */
    public $familyName;
    // you can't have both the attribute *and* the setter/getter
    //public $age;

    /**
     * Setter for the age of the dog.
     * Will record the age in dog's years.
     *
     * @param int $age the human-computed age
     */
    public function setAge($age)
    {
        // let's record it in dog years
        $this->age = $age * self::AGE_MULTIPLIER;
    }

    /**
     * Getter for fullname, will concatenate name and family name.
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->name . ' ' . $this->familyName;
    }
}
