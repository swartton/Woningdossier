<?php

namespace App\Http\Controllers\Cooperation\Tool\Information;

use App\Models\Cooperation;
use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VentilationController extends Controller
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
        $steps = Step::orderBy('order')->get();

        return view('cooperation.tool.ventilation-information.index', compact('steps'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Save progress
        Auth::user()->complete($this->step);
        $cooperation = Cooperation::find($request->session()->get('cooperation'));

        return redirect()->route('cooperation.tool.wall-insulation.index', ['cooperation' => $cooperation]);
    }

}