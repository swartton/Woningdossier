<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Models\BuildingCurrentHeating;
use App\Models\Cooperation;
use App\Models\HeatSource;
use App\Models\PresentHeatPump;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HeatPumpController extends Controller
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
        $heatpumpTypes = PresentHeatPump::all();
        $buildingCurrentHeatings = BuildingCurrentHeating::all();
        $heatSources = HeatSource::all();
        $steps = Step::orderBy('order')->get();
        return view('cooperation.tool.heat-pump.index', compact('heatpumpTypes', 'steps', 'heatSources', 'buildingCurrentHeatings'));
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
        Auth::user()->complete($this->step);
        $cooperation = Cooperation::find($request->session()->get('cooperation'));

        return redirect()->route('cooperation.tool.solar-panels.index', ['cooperation' => $cooperation]);
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
