<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WallInsulationRequest;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WallInsulationController extends Controller
{
    protected $step;

    public function __construct(Request $request)
    {
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

        $steps = Step::orderBy('order')->get();
        /** @var Building $building */
        $building = Building::find(HoomdossierSession::getBuilding());

        $facadeInsulation = $building->buildingElements()->where('element_id', 3)->first();
        $buildingFeature = $building->buildingFeatures;

        $buildingFeaturesForMe = BuildingFeature::withoutGlobalScope(GetValueScope::class)->forMe()->get();

        /** @var BuildingElement $houseInsulation */
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();

        $interests = Interest::orderBy('order')->get();

        return view('cooperation.tool.wall-insulation.index', compact(
            'steps', 'building', 'facadeInsulation',
            'surfaces', 'buildingFeature', 'interests', 'typeIds',
            'facadePlasteredSurfaces', 'facadeDamages', 'buildingFeaturesForMe'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(WallInsulationRequest $request)
    {

        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;
        $buildingId = $building->id;
        $inputSourceId = HoomdossierSession::getInputSource();

        $interests = $request->input('interest', []);
        UserInterest::saveUserInterests($user, $interests);

        // Get all the values from the form
        $wallInsulationQualities = $request->get('element', '');
        $plasteredWallSurface = $request->get('facade_plastered_surface_id', '');
        $damagedPaintwork = $request->get('facade_damaged_paintwork_id', 0);
        $wallJoints = $request->get('wall_joints', '');
        $wallJointsContaminated = $request->get('contaminated_wall_joints', '');
        $wallSurface = $request->get('wall_surface', 0);
        $insulationWallSurface = $request->get('insulation_wall_surface', 0);
        $additionalInfo = $request->get('additional_info', '');
        $cavityWall = $request->get('cavity_wall', '');
        $facadePlasteredOrPainted = $request->get('facade_plastered_painted', '');

        // Element id's and values
        $elementId = key($wallInsulationQualities);
        $elementValueId = reset($wallInsulationQualities);

        // Save the wall insulation
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
                'element_id' => $elementId,

            ],
            [
                'element_value_id' => $elementValueId,
            ]
        );

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $buildingId,
                'input_source_id' => $inputSourceId,
            ],
            [
                'facade_plastered_surface_id' => $plasteredWallSurface,
                'wall_joints' => $wallJoints,
                'cavity_wall' => $cavityWall,
                'contaminated_wall_joints' => $wallJointsContaminated,
                'wall_surface' => $wallSurface,
                'insulation_wall_surface' => $insulationWallSurface,
                'facade_damaged_paintwork_id' => $damagedPaintwork,
                'additional_info' => $additionalInfo,
                'facade_plastered_painted' => $facadePlasteredOrPainted,
            ]
        );


        // Save progress
        $this->saveAdvices($request);
        \Auth::user()->complete($this->step);
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        return redirect()->route(StepHelper::getNextStep($this->step), ['cooperation' => $cooperation]);
    }

    protected function saveAdvices(Request $request)
    {
        $user = Building::find(HoomdossierSession::getBuilding())->user;
        /** @var JsonResponse $results */
        $results = $this->calculate($request);
        $results = $results->getData(true);

        // Remove old results
        UserActionPlanAdvice::forMe()->where('input_source_id', HoomdossierSession::getInputSource())->forStep($this->step)->delete();

        if (isset($results['insulation_advice']) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::translated('measure_name', $results['insulation_advice'], 'nl')->first(['measure_applications.*']);
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication']; // only outlier
                $actionPlanAdvice->user()->associate($user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($this->step);
                $actionPlanAdvice->save();
            }
        }

        $keysToMeasure = [
            'paint_wall' => 'paint-wall',
            'repair_joint' => 'repair-joint',
            'clean_brickwork' => 'clean-brickwork',
            'impregnate_wall' => 'impregnate-wall',
        ];

        foreach ($keysToMeasure as $key => $measureShort) {
            if (isset($results[$key]['costs']) && $results[$key]['costs'] > 0) {
                $measureApplication = MeasureApplication::where('short', $measureShort)->first();
                if ($measureApplication instanceof MeasureApplication) {
                    $actionPlanAdvice = new UserActionPlanAdvice($results[$key]);
                    $actionPlanAdvice->user()->associate($user);
                    $actionPlanAdvice->measureApplication()->associate($measureApplication);
                    $actionPlanAdvice->step()->associate($this->step);
                    $actionPlanAdvice->save();
                }
            }
        }
    }

    public function calculate(WallInsulationRequest $request)
    {
        /**
         * @var Building
         */
        $building = Building::find(HoomdossierSession::getBuilding());
        $user = $building->user;
        $energyHabits = $user->energyHabit;

        $cavityWall = $request->get('cavity_wall', -1);
        $elements = $request->get('element', []);
        //$facadeSurface = NumberFormatter::reverseFormat($request->get('wall_surface', 0));
        $facadeSurface = $request->get('insulation_wall_surface', 0);

        $result = [
            'savings_gas' => 0,
            'paint_wall' => [
                'costs' => 0,
                'year' => 0,
            ],
        ];

        $advice = Temperature::WALL_INSULATION_JOINTS;
        if (1 == $cavityWall) {
            $advice = Temperature::WALL_INSULATION_JOINTS;
            //$result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.cavity-wall');
	        //$result['insulation_advice'] = MeasureApplication::byShort($advice)->measure_name;
        } elseif (2 == $cavityWall) {
            $advice = Temperature::WALL_INSULATION_FACADE;
            //$result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.facade-internal');
	        //$result['insulation_advice'] = MeasureApplication::byShort($advice)->measure_name;
        } elseif (0 == $cavityWall) {
            $advice = Temperature::WALL_INSULATION_RESEARCH;
            //$result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.research');
	        //$result['insulation_advice'] = MeasureApplication::byShort($advice)->measure_name;
        }
        $insulationAdvice = MeasureApplication::byShort($advice);
	    $result['insulation_advice'] = $insulationAdvice->measure_name;

        $elementValueId = array_shift($elements);
        $elementValue = ElementValue::find($elementValueId);
        if ($elementValue instanceof ElementValue) {
            $result['savings_gas'] = Calculator::calculateGasSavings($building, $elementValue, $energyHabits, $facadeSurface, $advice);
        }

        $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
        $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
        $result['cost_indication'] = Calculator::calculateCostIndication($facadeSurface, $insulationAdvice);
        $result['interest_comparable'] = NumberFormatter::format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

        $measureApplication = MeasureApplication::where('short', '=', 'repair-joint')->first();
        //$measureApplication = MeasureApplication::translated('measure_name', 'Reparatie voegwerk', 'nl')->first(['measure_applications.*']);
        $surfaceId = $request->get('wall_joints', 1);
        $wallJointsSurface = FacadeSurface::find($surfaceId);
        $number = 0;
        $year = null;
        if ($wallJointsSurface instanceof FacadeSurface) {
            $number = $wallJointsSurface->calculate_value;
            $year = Carbon::now()->year + $wallJointsSurface->term_years;
        }
        $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year, false);
        $result['repair_joint'] = compact('costs', 'year');

        $measureApplication = MeasureApplication::where('short', '=', 'clean-brickwork')->first();
        //$measureApplication = MeasureApplication::translated('measure_name', 'Reinigen metselwerk', 'nl')->first(['measure_applications.*']);
        $surfaceId = $request->get('contaminated_wall_joints', 1);
        $wallJointsSurface = FacadeSurface::find($surfaceId);
        $number = 0;
        $year = null;
        if ($wallJointsSurface instanceof FacadeSurface) {
            $number = $wallJointsSurface->calculate_value;
            $year = Carbon::now()->year + $wallJointsSurface->term_years;
        }
        $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year, false);
        $result['clean_brickwork'] = compact('costs', 'year');

        $measureApplication = MeasureApplication::where('short', '=', 'impregnate-wall')->first();
        //$measureApplication = MeasureApplication::translated('measure_name', 'Impregneren gevel', 'nl')->first(['measure_applications.*']);
        $surfaceId = $request->get('contaminated_wall_joints', 1);
        $wallJointsSurface = FacadeSurface::find($surfaceId);
        $number = 0;
        $year = null;
        if ($wallJointsSurface instanceof FacadeSurface) {
            $number = $wallJointsSurface->calculate_value;
            $year = Carbon::now()->year + $wallJointsSurface->term_years;
        }
        $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year, false);
        $result['impregnate_wall'] = compact('costs', 'year');

        // Note: these answer options are hardcoded in template
        $isPlastered = 2 != (int) $request->get('facade_plastered_painted', 2);

        if ($isPlastered) {

        	$measureApplication = MeasureApplication::where('short', '=', 'paint-wall')->first();
            //$measureApplication = MeasureApplication::translated('measure_name', 'Gevelschilderwerk op stuk- of metselwerk', 'nl')->first(['measure_applications.*']);
            $surfaceId = $request->get('facade_plastered_surface_id');
            $facadePlasteredSurface = FacadePlasteredSurface::find($surfaceId);
            $damageId = $request->get('facade_damaged_paintwork_id');
            $facadeDamagedPaintwork = FacadeDamagedPaintwork::find($damageId);
            $number = 0;
            $year = null;
            if ($facadePlasteredSurface instanceof FacadePlasteredSurface && $facadeDamagedPaintwork instanceof FacadeDamagedPaintwork) {
                $number = $facadePlasteredSurface->calculate_value;
                //$year = Carbon::now()->year + $facadePlasteredSurface->term_years;
                $year = Carbon::now()->year + $facadeDamagedPaintwork->term_years;
            }
            $costs = Calculator::calculateMeasureApplicationCosts($measureApplication,
                $number,
                $year, false);
            $result['paint_wall'] = compact('costs', 'year');
        }

        return response()->json($result);
    }
}
