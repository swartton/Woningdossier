<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\InsulatedGlazingCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\NumberFormatter;
use App\Http\Requests\InsulatedGlazingFormRequest;
use App\Models\Building;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Models\WoodRotStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InsulatedGlazingController extends Controller
{

	public function __construct(Request $request) {
		$slug = str_replace('/tool/', '', $request->getRequestUri());
		$this->step = Step::where('slug', $slug)->first();
		$myStep = Step::where('slug', $this->step->slug)->first();
		$prev = Step::where('order', $myStep->order - 1)->first();
		if (!\Auth::user()->hasCompleted($prev)){
			return redirect('/tool/' . $prev->slug . '/')->with(['cooperation' => $request->get('cooperation')]);
		}
	}

    /**
     * Display a listing of the resource.s
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    /**
	     * @var Building $building
	     */
    	$building = \Auth::user()->buildings->first();
        $steps = Step::orderBy('order')->get();

	    $interests = Interest::orderBy('order')->get();

        $insulatedGlazings = InsulatingGlazing::all();
		$crackSealing = Element::where('short', 'crack-sealing')->first();
		$frames = Element::where('short', 'frames')->first();
		$woodElements = Element::where('short', 'wood-elements')->first();
		$heatings = BuildingHeating::all();
		$paintworkStatuses = PaintworkStatus::orderBy('order')->get();
		$woodRotStatuses = WoodRotStatus::orderBy('order')->get();

		// nl names
		$measureApplicationNames = [
			'Glas in lood vervangen',
			'Plaatsen van HR++ glas (alleen het glas)',
			'Plaatsen van HR++ glas (inclusief kozijn)',
			'Plaatsen van drievoudige HR beglazing (inclusief kozijn)',
		];

		$buildingInsulatedGlazings = [];
	    $userInterests = [];

		foreach($measureApplicationNames as $measureApplicationName){
			$measureApplication = MeasureApplication::translated('measure_name', $measureApplicationName, 'nl')->first(['measure_applications.*']);

			if ($measureApplication instanceof MeasureApplication) {
				// get current situation
				$currentInsulatedGlazing = $building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id)->first();
				if ($currentInsulatedGlazing instanceof BuildingInsulatedGlazing){
					$buildingInsulatedGlazings[$measureApplication->id] = $currentInsulatedGlazing;
				}
				// get interests for the measure
				$measureInterest = \Auth::user()->interests()
				                                ->where('interested_in_type', 'measure_application')
												->where('interested_in_id', $measureApplication->id)
				                                ->get();
				if ($measureInterest instanceof UserInterest){
					// We only have to check on the interest ID, so we don't put
					// full objects in the array
					$userInterests[$measureApplication->id] = $measureInterest->interest_id;
				}

				$measureApplications [] = $measureApplication;
			}
		}

        return view('cooperation.tool.insulated-glazing.index', compact(
        	'building', 'steps', 'interests',
            'heatings', 'measureApplications', 'insulatedGlazings', 'buildingInsulatedGlazings',
	        'userInterests', 'crackSealing', 'frames', 'woodElements',
	        'paintworkStatuses', 'woodRotStatuses'
        ));
    }

	public function calculate(Request $request) {
		/**
		 * @var Building $building
		 */
		$user     = \Auth::user();
		$building = $user->buildings()->first();

		$result = [
			'savings_gas' => 0,
			'savings_co2' => 0,
			'savings_money' => 0,
			'cost_indication' => 0,
		];

		$userInterests = $request->get('user_interests', []);

		foreach($request->get('building_insulated_glazings', []) as $measureApplicationId => $buildingInsulatedGlazingsData) {
			$measureApplication = MeasureApplication::find($measureApplicationId);
			$buildingHeatingId = array_key_exists('building_heating_id', $buildingInsulatedGlazingsData) ? $buildingInsulatedGlazingsData['building_heating_id'] : 0;
			$buildingHeating = BuildingHeating::find($buildingHeatingId);
			$insulatedGlazingId = array_key_exists('insulated_glazing_id', $buildingInsulatedGlazingsData) ? $buildingInsulatedGlazingsData['insulated_glazing_id'] : 0;
			$insulatedGlazing = InsulatingGlazing::find($insulatedGlazingId);
			$interestId = array_key_exists($measureApplicationId, $userInterests) ? $userInterests[$measureApplicationId] : 0;
			$interest = Interest::find($interestId);

			if ($measureApplication instanceof MeasureApplication &&
			    $buildingHeating instanceof BuildingHeating &&
			    $interest instanceof Interest &&
			    array_key_exists($measureApplicationId, $userInterests) && $userInterests[$measureApplicationId] <= 3) {

				$gasSavings = InsulatedGlazingCalculator::calculateGasSavings(
					$buildingInsulatedGlazingsData['windows'], $measureApplication,
					$buildingHeating, $insulatedGlazing
				);

				$result['cost_indication'] += InsulatedGlazingCalculator::calculateCosts($measureApplication, $interest, (int) $buildingInsulatedGlazingsData['m2'], (int) $buildingInsulatedGlazingsData['windows']);
				$result['savings_gas'] += $gasSavings;

				$result['savings_co2']   += Calculator::calculateCo2Savings( $gasSavings );
				$result['savings_money'] += Calculator::calculateMoneySavings( $gasSavings );
			}
		}

		$result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

		$result['paintwork'] = [
			'cost' => 0,
			'year' => null,
		];


		$buildingElements = $request->get('building_elements', []);
		$framesValueId = array_key_exists('frames', $buildingElements) ? $buildingElements['frames'] : 0;
		$frameElementValue = ElementValue::find($framesValueId);

		// only applies for wooden frames
		if ($frameElementValue instanceof ElementValue && $frameElementValue->element->short == 'frames') {

			$windowSurface =    $request->get('window_surface', 0);
			// frame type use used as ratio (e.g. wood + some others -> use 70% of surface)
			$woodElementValues = [];

			foreach($buildingElements as $short => $ids){
				if ($short == 'wood-elements'){
					foreach(array_keys($ids) as $id) {
						$woodElementValue = ElementValue::find($id);

						if ($woodElementValue->element->short == $short){
							$woodElementValues []= $woodElementValue;
						}
					}
				}
			}

			$measureApplication = MeasureApplication::translated( 'measure_name',
				'Schilderwerk houten geveldelen',
				'nl' )->first( [ 'measure_applications.*' ] );

			$number = InsulatedGlazingCalculator::calculatePaintworkSurface($frameElementValue, $woodElementValues, $windowSurface);

			$buildingPaintworkStatuses = $request->get('building_paintwork_statuses', []);
			$paintworkStatus = null;
			$woodRotStatus = null;
			$lastPaintedYear = 2000;
			if (array_key_exists('paintwork_status_id', $buildingPaintworkStatuses)){
				$paintworkStatus = PaintworkStatus::find($buildingPaintworkStatuses['paintwork_status_id']);
			}
			if (array_key_exists('wood_rot_status_id', $buildingPaintworkStatuses)){
				$woodRotStatus = WoodRotStatus::find($buildingPaintworkStatuses['wood_rot_status_id']);
			}
			if (array_key_exists('last_painted_year', $buildingPaintworkStatuses)){
				$lastPaintedYear = $buildingPaintworkStatuses['last_painted_year'];
			}

			$year = InsulatedGlazingCalculator::determineApplicationYear($measureApplication, $paintworkStatus, $woodRotStatus, $lastPaintedYear);

			$costs                  = Calculator::calculateMeasureApplicationCosts( $measureApplication,
				$number,
				$year );
			$result['paintwork'] = compact( 'costs', 'year' );
			if ( $costs > 0 ) {
				UserActionPlanAdvice::updateOrCreate( [
					'user_id'                => \Auth::user()->id,
					'measure_application_id' => $measureApplication->id,
				],
					[
						'year' => $year,
					] );
			}
		}

		$result['crack-sealing'] = [
			'cost' => 0,
			'savings' => 0,
		];

		$crackSealingId = $request->get('building_elements.crack-sealing', 0);
		$crackSealingElement = ElementValue::find($crackSealingId);
		if ($crackSealingElement instanceof ElementValue && $crackSealingElement->element->short == 'crack-sealing' && $crackSealingElement->calculate_value > 1){
			$energyHabit = \Auth::user()->energyHabits;
			$gas = 0;
			if ($energyHabit instanceof UserEnergyHabit){
				$gas = $energyHabit->amount_gas;
			}
			if ($crackSealingElement->calculate_value == 2){
				$result['crack-sealing']['savings'] = (Kengetallen::PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING / 100) * $gas;
			}
			else {
				$result['crack-sealing']['savings'] = (Kengetallen::PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING / 100) * $gas;
			}

			$measureApplication = MeasureApplication::translated( 'measure_name',
				'Kierdichting verbeteren',
				'nl' )->first( [ 'measure_applications.*' ] );

			$result['crack-sealing']['costs'] = Calculator::calculateMeasureApplicationCosts($measureApplication, 1);
		}

		return response()->json($result);
	}


    /**
     * Store the incoming request and redirect to the next step.
     *
     * @param InsulatedGlazingFormRequest   $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InsulatedGlazingFormRequest $request)
    {
        $cooperation = Cooperation::all();
        $steps = Step::orderBy('order')->get();

        return redirect()->route('cooperation.tool.floor-insulation.index', ['cooperation' => $cooperation]);
    }


}
