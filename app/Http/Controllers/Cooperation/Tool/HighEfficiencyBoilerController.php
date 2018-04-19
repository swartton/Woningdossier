<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Requests\HighEfficiencyBoilerFormRequest;
use App\Models\Cooperation;
use App\Models\Step;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\NumberFormatter;
use App\Models\Building;
use App\Models\BuildingService;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HighEfficiencyBoilerController extends Controller
{

    protected $step;

    public function __construct(Request $request) {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$user = \Auth::user();
	    /** @var Building $building */
    	$building = $user->buildings()->first();
    	$habit = $user->energyHabit;
	    $steps = Step::orderBy('order')->get();
	    // NOTE: building element hr-boiler tells us if it's there
	    $boiler = Service::where('short', 'boiler')->first();
		$boilerTypes = $boiler->values()->orderBy('order')->get();

		$installedBoiler = BuildingService::where('service_id', $boiler->id)->first();

        return view('cooperation.tool.hr-boiler.index', compact(
        	'habit', 'boiler', 'boilerTypes', 'installedBoiler',
	        'steps'));
    }

    public function calculate(Request $request){

    	$user = \Auth::user();

	    $result = [
		    'savings_gas' => 0,
		    'savings_co2' => 0,
		    'savings_money' => 0,
		    'cost_indication' => 0,
		    'interest_comparable' => 0,
	    ];

		$services = $request->input('building_services', []);
		// (there's only one..)
	    foreach($services as $serviceId => $options){
	    	$boilerService = Service::find($serviceId);

			if (array_key_exists('service_value_id', $options)){
				/** @var ServiceValue $boilerType */
				$boilerType = ServiceValue::where('service_id', $boilerService->id)
					->where('id', $options['service_value_id'])
					->first();

				$boilerEfficiency = $boilerType->keyFigureBoilerEfficiency;
				if ($boilerEfficiency->heating > 95){
					$result['boiler_advice'] = __('woningdossier.cooperation.tool.boiler.already-efficient');
				}

				if (array_key_exists('extra', $options)){
					$year = $options['extra'];

					$measure = MeasureApplication::translated('measure_name', 'Vervangen cv ketel', 'nl')->first(['measure_applications.*']);

					$amountGas = $request->input('habit.gas_usage', null);

					$result['savings_gas'] = HighEfficiencyBoilerCalculator::calculateGasSavings($boilerType, $user->energyHabit, $amountGas);
					$result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
					$result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
					//$result['cost_indication'] = Calculator::calculateCostIndication(1, $measure->measure_name);
					$result['replace_year'] = HighEfficiencyBoilerCalculator::determineApplicationYear($measure, $year);
					$result['cost_indication'] = Calculator::calculateMeasureApplicationCosts( $measure, 1, $result['replace_year'] );
					$result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);
				}
			}
	    }

	    return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HighEfficiencyBoilerFormRequest $request)
    {
        // Save the building service
        $buildingServices = $request->input('building_services', '');
        $buildingServiceId = key($buildingServices);

        $serviceValue = isset($buildingServices[$buildingServiceId]['service_value_id']) ? $buildingServices[$buildingServiceId]['service_value_id'] : "";
        $extra = isset($buildingServices[$buildingServiceId]['extra']) ? $buildingServices[$buildingServiceId]['extra'] : "";
        $comment = $request->input('comment', '');

        BuildingService::updateOrCreate(
            [
                'building_id' => Auth::user()->buildings()->first()->id,
                'service_id' => $buildingServiceId,
            ],
            [
                'service_value_id' => $serviceValue,
                'extra' => ['date' => $extra, 'comment' => $comment],
            ]
        );

        // save the habits
        $habits = $request->input('habit', '');
        $gasUsage = isset($habits['gas_usage']) ? $habits['gas_usage'] : "";
        $residentCount = isset($habits['resident_count']) ? $habits['resident_count'] : "";

        UserEnergyHabit::updateOrCreate(
            [
                'user_id' => Auth::id(),
            ],
            [
                'resident_count' => $residentCount,
                'amount_gas' => $gasUsage,
            ]
        );

        // Save progress
	    $this->saveAdvices($request);
        Auth::user()->complete($this->step);
        $cooperation = Cooperation::find(\Session::get('cooperation'));
        return redirect()->route('cooperation.tool.solar-panels.index', ['cooperation' => $cooperation]);
    }

	protected function saveAdvices(Request $request){
		/** @var JsonResponse $results */
		$results = $this->calculate($request);
		$results = $results->getData(true);

		// Remove old results
		UserActionPlanAdvice::forMe()->forStep($this->step)->delete();

		if (isset($results['cost_indication']) && $results['cost_indication'] > 0){
			$measureApplication = MeasureApplication::where('short', 'high-efficiency-boiler-replace')->first();
			if ($measureApplication instanceof MeasureApplication){
				$actionPlanAdvice = new UserActionPlanAdvice($results);
				$actionPlanAdvice->costs = $results['cost_indication'];
				$actionPlanAdvice->year = $results['replace_year'];
				$actionPlanAdvice->user()->associate(Auth::user());
				$actionPlanAdvice->measureApplication()->associate($measureApplication);
				$actionPlanAdvice->step()->associate($this->step);
				$actionPlanAdvice->save();
			}
		}
	}

}
