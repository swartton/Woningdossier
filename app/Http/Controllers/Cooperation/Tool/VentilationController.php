<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\BuildingElement;
use App\Models\BuildingService;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class VentilationController extends Controller
{
    /**
     * @var Step
     */
    protected $step;

    public function __construct(Request $request)
    {
        $slug       = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $building = HoomdossierSession::getBuilding(true);

        /** @var BuildingService $buildingVentilationService */
        $buildingVentilationService = $building->getBuildingService('house-ventilation',
            HoomdossierSession::getInputSource(true));
        /** @var ServiceValue $buildingVentilation */
        $buildingVentilation = $buildingVentilationService->serviceValue;

        return view('cooperation.tool.ventilation.index',
            compact('building', 'buildingVentilation'));
        //return view('cooperation.tool.ventilation-information.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());

        $building = HoomdossierSession::getBuilding(true);
        // Save progress
        StepHelper::complete($this->step, $building,
            HoomdossierSession::getInputSource(true));
        $cooperation = HoomdossierSession::getCooperation(true);

        $nextStep = StepHelper::getNextStep($building,
            HoomdossierSession::getInputSource(true), $this->step);
        $url      = $nextStep['url'];

        if ( ! empty($nextStep['tab_id'])) {
            $url .= '#'.$nextStep['tab_id'];
        }

        return redirect($url);
    }

    public function calculate(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);

        $step = Step::where('slug', '=', 'ventilation')->first();

        /** @var BuildingService $buildingVentilationService */
        $buildingVentilationService = $building->getBuildingService('house-ventilation',
            HoomdossierSession::getInputSource(true));
        /** @var ServiceValue $buildingVentilation */
        $buildingVentilation = $buildingVentilationService->serviceValue;

        $currentCrackSealing = $building->getBuildingElement('crack-sealing');

        $currentlyDemandDriven = $buildingVentilationService->extra['demand_driven'] ?? false;
        $currentlyHeatRecovery = $buildingVentilationService->extra['heat_recovery'] ?? false;

        $ventilationTypes = [
            1 => 'natural',
            2 => 'mechanic',
            3 => 'balanced',
            4 => 'decentral',
        ];

        $ventilationType = $ventilationTypes[$buildingVentilation->calculate_value];

        // Get all measures, will be conditionally unset
        $measures = [
            'ventilation-balanced-wtw',
            'ventilation-decentral-wtw',
            'ventilation-demand-driven',
            'crack-sealing',
        ];
        $measures = array_flip($measures);

        if ($ventilationType === 'natural') {
            // "different" type which returns early
            unset($measures['crack-sealing']);

            $advices = MeasureApplication::where('step_id', '=', $step->id)->whereIn('short', array_keys($measures))->get();

            return [
                'improvement' => 'Natuurlijke ventilatie is  niet zo goed voor het comfort en zorgt voor een hoog energiegebruik. Daarom worden de huizen steeds luchtdichter gemaakt en van goede isolatie voorzien. Om een gezond binnenklimaat te bereiken is hierbij een andere vorm van ventilatie nodig. De volgende opties kunt u overwegen:',
                'advices'     => $advices,
                'remark' => 'Om te bepalen welke oplossing voor uw woning de beste is wordt geadviseerd om dit door een specialist te laten beoordelen.',
            ];
        }
        if ($ventilationType === 'mechanic') {

            if ($currentlyDemandDriven){
                // if the ventilation is already demand driven, remove that advice
                unset($measures['ventilation-demand-driven']);
            }

            // If "No crack sealing" is NOT checked AND crack sealing element calculate value is 2, 3 or 4 ( >= 2..)
            // Crack sealing measure should be added.
            // As it's added on beforehand, it should be removed if:
            // "no crack sealing" is checked OR crack sealing element calculate value is 1 ( < 2)
            // because: either there is no crack sealing or it's all okay
            $currentCrackSealingCalculateValue = $currentCrackSealing->elementValue->calculate_value ?? 10;

            if (in_array('none', $request->input('building_ventilations.how', [])) || $currentCrackSealingCalculateValue < 2){
                unset($measures['crack-sealing']);
            }

            $improvement = 'Oude ventilatoren gebruiken soms nog wisselstroom en verbruiken voor dezelfde prestatie veel meer elektriciteit en maken meer geluid dan moderne gelijkstroom ventilatoren. De besparing op de gebruikte stroom kan oplopen tot ca. 80 %. Een installateur kan direct beoordelen of u nog een wisselstroom ventilator heeft.';
            $remark = 'Om te bepalen welke oplossing voor uw woning de beste is wordt geadviseerd om dit door een specialist te laten beoordelen.';
        }
        if ($ventilationType === 'balanced') {

            // always unset
            unset($measures['ventilation-decentral-wtw']);

            // if the ventilation already has heat recovery, remove that advice
            if ($currentlyHeatRecovery){
                unset($measures['ventilation-balanced-wtw']);
            }

            // if the ventilation is already demand driven, remove that advice
            if ($currentlyDemandDriven){
                unset($measures['ventilation-demand-driven']);
            }

            // If "No crack sealing" is NOT checked AND crack sealing element calculate value is 2, 3 or 4 ( >= 2..)
            // Crack sealing measure should be added.
            // As it's added on beforehand, it should be removed if:
            // "no crack sealing" is checked OR crack sealing element calculate value is 1 ( < 2)
            // because: either there is no crack sealing or it's all okay
            $currentCrackSealingCalculateValue = $currentCrackSealing->elementValue->calculate_value ?? 10;

            if (in_array('none', $request->input('building_ventilations.how', [])) || $currentCrackSealingCalculateValue < 2){
                unset($measures['crack-sealing']);
            }

            $improvement = 'Uw woning is voorzien van een energiezuinig en duurzaam ventilatiesysteem. Zorg voor goed onderhoud en goed gebruik zo dat de luchtkwaliteit in de woning optimaal blijft.';
            $remark = 'Om te bepalen welke oplossing voor uw woning de beste is wordt geadviseerd om dit door een specialist te laten beoordelen.';
        }
        if ($ventilationType === 'decentral') {

            // always unset
            unset($measures['ventilation-balanced-wtw']);

            // if the ventilation already has heat recovery, remove that advice
            if ($currentlyHeatRecovery){
                unset($measures['ventilation-decentral-wtw']);
            }

            // if the ventilation is already demand driven, remove that advice
            if ($currentlyDemandDriven){
                unset($measures['ventilation-demand-driven']);
            }

            // If "No crack sealing" is NOT checked AND crack sealing element calculate value is 2, 3 or 4 ( >= 2..)
            // Crack sealing measure should be added.
            // As it's added on beforehand, it should be removed if:
            // "no crack sealing" is checked OR crack sealing element calculate value is 1 ( < 2)
            // because: either there is no crack sealing or it's all okay
            $currentCrackSealingCalculateValue = $currentCrackSealing->elementValue->calculate_value ?? 10;

            if (in_array('none', $request->input('building_ventilations.how', [])) || $currentCrackSealingCalculateValue < 2){
                unset($measures['crack-sealing']);
            }

            $improvement = 'Uw woning is voorzien van een energiezuinig en duurzaam ventilatiesysteem. Zorg voor goed onderhoud en goed gebruik zo dat de luchtkwaliteit in de woning optimaal blijft.';
            $remark = 'Om te bepalen welke oplossing voor uw woning de beste is wordt geadviseerd om dit door een specialist te laten beoordelen.';
        }

        $advices = MeasureApplication::where('step_id', '=', $step->id)->whereIn('short', array_keys($measures))->get();

        $advices->each(function($advice){
            $advice->name = $advice->measure_name;
        });




        if (array_key_exists('crack-sealing', $measures)){
            // Crack sealing gives a percentage of savings. This is dependent on the application (place or replace)
            // and the gas usage (for heating)

            $result['crack_sealing'] = [
                'cost_indication' => 0,
                'savings_gas' => 0,
            ];

            // (we know that calculate_value is > 1, but for historic logic reasons..
            if ($currentCrackSealing instanceof BuildingElement && $currentCrackSealingCalculateValue > 1) {
                $gas = 0;
                $energyHabit = $building->user->energyHabit;
                if ($energyHabit instanceof UserEnergyHabit) {
                    $boiler = $building->getServiceValue('boiler', HoomdossierSession::getInputSource(true));
                    $usages = HighEfficiencyBoilerCalculator::calculateGasUsage($boiler, $energyHabit);
                    $gas = $usages['heating']['bruto'];
                }

                if (2 == $currentCrackSealingCalculateValue) {
                    $result['crack_sealing']['savings_gas'] = (Kengetallen::PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING / 100) * $gas;
                } else {
                    $result['crack_sealing']['savings_gas'] = (Kengetallen::PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING / 100) * $gas;
                }

                /** @var MeasureApplication $measureApplication */
                $measureApplication = MeasureApplication::where('short', 'crack-sealing')->first();

                $result['crack_sealing']['cost_indication'] = Calculator::calculateMeasureApplicationCosts($measureApplication, 1, null, false);
                $result['crack_sealing']['savings_co2'] = Calculator::calculateCo2Savings($result['crack_sealing']['savings_gas']);
                $result['crack_sealing']['savings_money'] = Calculator::calculateMoneySavings($result['crack_sealing']['savings_gas']);
                $result['crack_sealing']['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['crack_sealing']['cost_indication'], $result['crack_sealing']['savings_money']), 1);
            }
        }


        if (count($advices) > 0){
            $improvement .= '  Om de ventilatie verder te verbeteren kunt u de volgende opties overwegen:';
        }

        return compact('improvement', 'advices', 'remark', 'result');
    }
}
