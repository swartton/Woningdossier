<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\InputSource;
use App\Models\User;
use Illuminate\Support\Arr;

abstract class ToolHelper
{
    /** @var User */
    public $user;

    /** @var InputSource */
    public $inputSource;

    /** @var \App\Models\Building */
    public $building;

    /**
     * What values the controller expects.
     *
     * @var array
     */
    public $values;

    public function __construct(User $user, InputSource $inputSource)
    {
        $this->user = $user;
        $this->inputSource = $inputSource;
        $this->building = $user->building;
    }

    public function setValues(array $values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get the values or a direct value from the value array.
     *
     * @param null $key
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getValues($key = null)
    {
        if (is_null($key)) {
            return $this->values;
        }

        return Arr::get($this->values, $key);
    }

    /**
     * Must set the values, according to the given user and InputSource.
     */
    abstract public function createValues(): ToolHelper;

    /**
     * Must save the step data.
     */
    abstract public function saveValues(): ToolHelper;

    /**
     * Must clear and create the user action plan advices.
     */
    abstract public function createAdvices(): ToolHelper;
}
