<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Models\Questionnaire;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Console\Question\Question;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.cooperation.coordinator.questionnaires.index', compact('questionnaires'));
    }

    public function create()
    {
        return view('cooperation.admin.cooperation.coordinator.questionnaires.create');
    }

    /**
     * Set a questionnaire active status
     *
     * @param Request $request
     */
    public function setActive(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');
        $active = $request->get('questionnaire_active');

        if ($active == "true") {
            $active = true;
        } else {
            $active = false;
        }

        $questionnaire = Questionnaire::find($questionnaireId);
        $questionnaire->is_active = $active;
        $questionnaire->save();

    }
}
