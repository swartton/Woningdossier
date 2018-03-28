<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GeneralDataFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        // This will retrieve all the array keys and value's from the interested input fields
        foreach($this->request->get('interested') as $key => $interest) {
            // This will put every interested input on required
            $interestedRules['interested.' . $key] = 'required|exists:interests,id';


        }
        // Remove the sun_panel_interested field,
        // Cause the sun panel itselft isn't required.
        unset($interestedRules['interested.sun_panel']);

        // Add the remaining rules
        $remainingRules = [
            // start
            'example_building_type' => 'required|exists:example_buildings,id',
            'building_type_id' => 'required|exists:building_types,id',
            'build_year' => 'required|numeric',
            'surface' => 'required|numeric',
            'monument' => 'numeric|digits_between:0,2',
	        'energy_label_id' => 'required|exists:energy_labels,id',
            'building_layers' => 'numeric|digits_between:1,999',
	        'roof_type_id' => 'required|exists:roof_types,id',

	        'element.*' => 'required|exists:element_values,id',
	        'user_interest_element.*' => 'required|exists:interests,id',
            // Energy measures
            //'windows_in_living_space' => 'required|exists:present_windows,id',
            //'windows_in_sleeping_spaces' => 'required|exists:present_windows,id',
            //'facade_insulation' => 'required|exists:qualities,id',
            //'floor_insulation' => 'required|exists:qualities,id',
            //'roof_insulation' => 'required|exists:qualities,id',
            'hr_cv_boiler' => 'required|exists:central_heating_ages,id',
            'hybrid_heatpump' => 'required|exists:present_heat_pumps,id',
            'monovalent_heatpump' => 'required|exists:present_heat_pumps,id',
            'sun_boiler' => 'required|exists:solar_water_heaters,id',
            'house_ventilation' => 'required|exists:ventilations,id',
            'house_ventilation_placed_date' => 'nullable|required_if:house_ventilation,2|date',
            'sun_panel' => 'nullable|numeric',
            'interested.sun_panel' => 'nullable|exists:interests,id',
            'sun_panel_placed_date' => 'nullable|date',

            // data about usage of the building
            'resident_count' => 'required|numeric',
            'cooked_on_gas' => 'numeric',
            'thermostat_highest' => 'nullable|numeric',
            'thermostat_lowest' => 'nullable|numeric|digits_between:0,'.$this->request->get('thermostat_highest'),
            'electricity_consumption_past_year' => 'nullable|numeric',
            'gas_usage_past_year' => 'nullable|numeric',


        ];

        $validationRules = array_merge($interestedRules, $remainingRules);
        
        return $validationRules;
    }
}
