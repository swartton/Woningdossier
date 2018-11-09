<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class WallInsulationRequest extends FormRequest
{
    use DecimalReplacementTrait;
    use ValidatorTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    public function getValidatorInstance()
    {
        $this->decimals(['wall_surface', 'insulation_wall_surface']);

        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'element' => 'exists:element_values,id',
            // radio buttons
            'facade_plastered_painted' => 'required|between:1,3',
            'cavity_wall' => 'required|between:0,2',
            // inputs
            'damage_paintwork' => 'exists:facade_damaged_paintworks,id',
            'facade_plastered_surface_id' => 'exists:facade_plastered_surfaces,id',
            'wall_joints' => 'exists:facade_surfaces,id',
            'contaminated_wall_joints' => 'exists:facade_surfaces,id',
            'wall_surface' => 'nullable|numeric',
            'insulation_wall_surface' => ['nullable', 'numeric', 'needs_to_be_lower_or_same_as:wall_surface'],
            'additional_info' => 'nullable',
        ];
    }
}
