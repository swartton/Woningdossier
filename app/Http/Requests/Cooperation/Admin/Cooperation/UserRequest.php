<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation;

use App\Rules\AlphaSpace;
use App\Rules\HouseNumber;
use App\Rules\PostalCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        // collecting the rules for the fields that need to be required if the role field contains value 5 / resident
        // finding a cleaner way would be nice

        return [
            'first_name' => ['required', new AlphaSpace()],
            'last_name' => ['required', new AlphaSpace()],
            'password' => 'nullable|min:6',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|exists:roles,id',
            'coach_id' => ['nullable', Rule::exists('users', 'id')],

            'postal_code' => [new PostalCode()],
            'number' => [new HouseNumber()],
            'street' => 'required|string',
            'city' => 'required|string',
        ];
    }
}