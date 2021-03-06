<?php

namespace App\Http\Requests;

use App\Rules\HouseNumber;
use App\Rules\HouseNumberExtension;
use App\Rules\PhoneNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;

class MyAccountSettingsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user.first_name' => 'required|string|max:255',
            'user.last_name' => 'required|string|max:255',
            'user.phone_number' => ['nullable', new PhoneNumber()],

            'building.postal_code' => ['required', new PostalCode('nl')],
            'building.house_number' => ['required', 'numeric', new HouseNumber('nl')],
            'building.house_number_extension' => ['nullable', new HouseNumberExtension('nl')],
            'building.street' => 'required|string|max:255',
            'building.city' => 'required|string|max:255',
        ];
    }
}
