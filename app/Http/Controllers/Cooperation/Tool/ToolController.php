<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Step;
use App\Models\Translation;
use App\Services\CsvExportService;
use Illuminate\Http\Request; use App\Scopes\GetValueScope;
use Ramsey\Uuid\Uuid;

class ToolController extends Controller
{
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Redirect to the general data step since the tool view has no content.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {
        $cooperation = Cooperation::find(\Session::get('cooperation'));

        return redirect(route('cooperation.tool.general-data.index', ['cooperation' => $cooperation]));
    }

}
