<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class HouseNumberExtension extends LocaleBasedRule implements Rule
{
    protected $countryRegexes = [
        'nl' => '/^(boven|beneden|onder|hs|bis|zw|rd|[\d]{,2}|[a-z]{1})$/',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($this->country)) {
            return false;
        }
        if (! isset($this->countryRegexes[$this->country])) {
            return false;
        }

        // Move out the dashes. This is actually valid but can cause strange effects
        $value = str_replace(' ', '', trim($value));

        return false != preg_match($this->countryRegexes[$this->country], $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.house_number_extension');
    }
}
