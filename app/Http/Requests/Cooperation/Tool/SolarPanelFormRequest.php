<?php

namespace App\Http\Requests\Cooperation\Tool;

use Illuminate\Foundation\Http\FormRequest;

class SolarPanelFormRequest extends FormRequest
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
            'building_pv_panels.peak_power' => 'required|numeric|between:260,330',
            'building_pv_panels.number' => 'required|numeric|min:0|max:50',
            'building_pv_panels.angle' => 'required|numeric',
            'building_pv_panels.pv_panel_orientation_id' => 'required|exists:pv_panel_orientations,id',

            'user_energy_habits.amount_electricity' => 'required|numeric',
        ];
    }
}
