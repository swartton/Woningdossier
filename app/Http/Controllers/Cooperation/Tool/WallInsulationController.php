<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculator;
use App\Helpers\Kengetallen;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingType;
use App\Models\ElementValue;
use App\Models\PresentWindow;
use App\Models\Step;
use App\Models\SurfacePaintedWall;
use App\Models\WallNeedImpregnation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WallInsulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $steps = Step::orderBy('order')->get();
        /** @var Building $building */
        $building = \Auth::user()->buildings()->first();

        $houseInsulation = $building->buildingElements()->where('element_id', 3)->first();

        /** @var BuildingElement $houseInsulation */
        //dd($houseInsulation->element->values);

        //$houseInsulations = PresentWindow::all();
        $surfacePaintedWalls = SurfacePaintedWall::all();
        $wallsNeedImpregnation = WallNeedImpregnation::all();
        return view('cooperation.tool.wall-insulation.index', compact('steps', 'building', 'houseInsulation', 'surfacePaintedWalls', 'wallsNeedImpregnation'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    public function calculate(Request $request){
	    //dd($request->all());
	    /**
	     * @var Building $building
	     */
	    $building = \Auth::user()->buildings()->first();

    	$cavityWall = $request->get('cavity_wall', -1);
		$elements = $request->get('element', []);
		$facadeSurface = $request->get('facade_surface', 0);

    	$result = [
    		'savings_gas' => 0,
	    ];

    	if ($cavityWall == 1){
    	    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.cavity-wall');
	    }
	    elseif ($cavityWall == 2){
		    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.facade-internal');
	    }
	    elseif($cavityWall == 0) {
		    $result['insulation_advice'] = trans('woningdossier.cooperation.tool.wall-insulation.insulation-advice.research');
	    }

	    $elementValueId = array_shift($elements);
	    $elementValue = ElementValue::find($elementValueId);
	    if ($elementValue instanceof ElementValue){
			/*if (isset($elementValue->calculate_value) && $elementValue->calculate_value < 3){
				$result['savings_gas'] = min(
					$facadeSurface * Kengetallen::ENERGY_SAVING_WALL_INSULATION,
					Calculator::maxGasSavings($building->buildingType)
				);
			}*/
			$result['savings_gas'] = Calculator::calculateGasSavings($building, $elementValue, $facadeSurface);
	    }

	    $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
	    $result['savings_money'] = Calculator::calculateMoneySavings($result['savings_gas']);

	    return response()->json($result);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
