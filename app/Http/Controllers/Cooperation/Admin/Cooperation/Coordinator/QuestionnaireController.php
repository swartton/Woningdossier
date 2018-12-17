<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Http\Requests\Admin\Cooperation\Coordinator\QuestionnaireRequest;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::all();

        return view('cooperation.admin.cooperation.coordinator.questionnaires.index', compact('questionnaires'));
    }

    public function edit(Cooperation $cooperation, $questionnaireId)
    {
        $questionnaire = Questionnaire::find($questionnaireId);

        $this->authorize('edit', $questionnaire);

	    $steps = Step::orderBy('order')->get();

        return view('cooperation.admin.cooperation.coordinator.questionnaires.questionnaire-editor', compact('questionnaire', 'steps'));
    }

    public function create()
    {
	    $steps = Step::orderBy('order')->get();

        return view('cooperation.admin.cooperation.coordinator.questionnaires.create', compact('steps'));
    }


    /**
     * Return the validation for the current question
     *
     * @param array $requestQuestion
     * @param array $validation
     * @return array
     */
    protected function getValidationForCurrentQuestion(array $requestQuestion, array $validation) : array
    {
        // first check if the requestquestion has a guid
        if (array_key_exists('guid', $requestQuestion)) {
            // after that check if the guid exists in the validation
            if (array_key_exists($requestQuestion['guid'], $validation)) {
                return $validation[$requestQuestion['guid']];
            }
        } else if (array_key_exists('question_id', $requestQuestion)) {
            if (array_key_exists($requestQuestion['question_id'], $validation)) {
                return $validation[$requestQuestion['question_id']];
            }
        }

        return [];
    }


    /**
     * Returns the validation rule in a array
     *
     * @param array $requestQuestion
     * @param array $validation
     * @return array
     */
    protected function getValidationRule(array $requestQuestion, array $validation) : array
    {
        // get the validation for the current question
        $validationForCurrentQuestion = $this->getValidationForCurrentQuestion($requestQuestion, $validation);

        if (!empty($validationForCurrentQuestion)) {

            // built the validation rule array
            $validationRule = [
                $validationForCurrentQuestion['main-rule'] => [
                    $validationForCurrentQuestion['sub-rule']  => []
                ]
            ];

            // first check if there are sub rule check values
            if (array_key_exists('sub-rule-check-value', $validationForCurrentQuestion)) {

                // if so, push them inside the sub-rule array
                foreach ($validationForCurrentQuestion['sub-rule-check-value'] as $subRuleCheckValue) {
                    array_push($validationRule[$validationForCurrentQuestion['main-rule']][$validationForCurrentQuestion['sub-rule']], $subRuleCheckValue);
                }
            }
            return $validationRule;
        }

        return [];
    }

    /**
     * Create a question
     *
     * @param int $questionnaireId
     * @param array $requestQuestion
     * @param string $questionType
     * @param bool $questionHasOptions
     */
    protected function createQuestion(int $questionnaireId, array $requestQuestion, string $questionType, array $validation, $order, bool $questionHasOptions = false)
    {

        $required = false;

        if (array_key_exists('required', $requestQuestion)) {
            $required = true;
        }

        $uuid = Str::uuid();

        $createdQuestion = Question::create([
            'name' => $uuid,
            'type' => $questionType,
            'order' => $order,
            'required' => $required,
            'validation' => $this->getValidationRule($requestQuestion, $validation),
            'questionnaire_id' => $questionnaireId
        ]);

        // multiple translations can be available
        foreach ($requestQuestion['question'] as $locale => $question) {
            // the uuid we will put in the key for the translation and set in the question name column

            if (empty($question)) {
                $question = current(array_filter($requestQuestion['question']));
            }

            Translation::create([
                'key' => $uuid,
                'translation' => $question,
                'language' => $locale
            ]);
        }

        if ($questionHasOptions) {
            // create the options for the question
            foreach ($requestQuestion['options'] as $newOptions) {
                $this->createQuestionOptions($newOptions, $createdQuestion);
            }
        }

    }


    /**
     * Create the options for a question
     *
     * Creates question option and 2 translations
     *
     * @param array $newOptions
     * @param Question $question
     */
    protected function createQuestionOptions(array $newOptions, Question $question)
    {
        if (!$this->isEmptyTranslation($newOptions)) {

            $optionNameUuid = Str::uuid();
            // for every option we need to create a option input
            QuestionOption::create([
                'question_id' => $question->id,
                'name' => $optionNameUuid,
            ]);

            // for every translation we need to create a new, you wont guess! Translation.
            foreach ($newOptions as $locale => $translation) {

                if (empty($translation)) {
                    $translation = current(array_filter($newOptions));
                }

                Translation::create([
                    'key' => $optionNameUuid,
                    'translation' => $translation,
                    'language' => $locale
                ]);
            }
        }
    }


    /**
     * Update a question, if the question has options we will update the question options as well
     *
     * @param int $questionId
     * @param array $editedQuestion
     * @param array $validation
     * @param bool $questionHasOptions
     */
    protected function updateQuestion(int $questionId, array $editedQuestion, array $validation, $order, bool $questionHasOptions = false)
    {
        $required = false;

        if (array_key_exists('required', $editedQuestion)) {
            $required = true;
        }

        $currentQuestion = Question::find($questionId);

        $currentQuestion->update([
            'validation' => $this->getValidationRule($editedQuestion, $validation),
            'order' => $order,
            'required' => $required,
        ]);


        // multiple translations can be available
        foreach ($editedQuestion['question'] as $locale => $question) {
            if (empty($question)) {
                $question = current(array_filter($editedQuestion['question']));
            }
            $currentQuestion->updateTranslation('name', $question, $locale);
        }

        if ($questionHasOptions) {
            $this->updateQuestionOptions($editedQuestion, $currentQuestion);
        }
    }


    /**
     * Update the options from a question
     *
     * @param array $editedQuestion
     * @param Question $question
     */
    public function updateQuestionOptions(array $editedQuestion, $question)
    {
        // $questionOptionId will mostly contain the id of a QuestionOption
        // however, if a new option to a existing question is added, we set a uuid.
        // so if the $questionOptionId = a valid uuid we need to create a new QuestionOption and the translations.
        foreach ($editedQuestion['options'] as $questionOptionId => $translations) {

            if (Str::isValidUuid($questionOptionId) && $this->isNotEmptyTranslation($translations)) {

                // if the uuid is valid a pomp it to a array and create new question options
                $allNewOptions = collect($editedQuestion['options'])->filter(function ($value, $key) {
                    return Str::isValidUuid($key);
                })->toArray();

                // create the options
                foreach ($allNewOptions as $newOptions) {
                    $this->createQuestionOptions($newOptions, $question);
                }

            } elseif ($this->isNotEmptyTranslation($translations)) {
                // for every translation we need to create a new, you wont guess! Translation.
                foreach ($translations as $locale => $option) {
                    if (empty($option)) {
                        $option = current(array_filter($translations));
                    }
                    QuestionOption::find($questionOptionId)->updateTranslation('name', $option, $locale);
                }
            }
        }
    }


    /**
     * Update the questionnaire and questions
     * if there are new questions create those toes
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(QuestionnaireRequest $request)
    {

        // get the data for the questionnaire
        $questionnaireNameTranslations = $request->input('questionnaire.name');
        $stepId = $request->input('questionnaire.step_id');
        $questionnaireId = $request->input('questionnaire.id');

        // find the current questionnaire
        $questionnaire = Questionnaire::find($questionnaireId);


        $this->authorize('update', $questionnaire);
        // update the step
        $questionnaire->update([
            'step_id' => $stepId,
        ]);

        // and update the translations
        foreach ($questionnaireNameTranslations as $locale => $questionnaireNameTranslation) {
            if (empty($questionnaireNameTranslation)) {
                $questionnaireNameTranslation = current(array_filter($questionnaireNameTranslations));
            }
            $questionnaire->updateTranslation('name', $questionnaireNameTranslation, $locale);
        }

        $order = 0;

        $validation = $request->get('validation', []);

        if ($request->has('questions')) {

            foreach ($request->get('questions') as $key => $allRequestQuestion) {
                $order++;

                // if the key is a valid uuid, we know it is a new question
                // existing questions will have a questionId as key
                if (Str::isValidGuid($key)) {
                    $questionType = $allRequestQuestion['type'];
                    $requestQuestion = $allRequestQuestion;

                    switch ($questionType) {
                        case ('text'):
                            $this->createQuestion($questionnaireId, $requestQuestion, $questionType, $validation, $order);
                            break;
                        case('select'):
                            $this->createQuestion($questionnaireId, $requestQuestion, $questionType, $validation, $order, true);
                            break;
                        case('date'):
                            $this->createQuestion($questionnaireId, $requestQuestion, $questionType, $validation, $order);
                            break;
                        case('radio'):
                            $this->createQuestion($questionnaireId, $requestQuestion, $questionType, $validation, $order,true);
                            break;
                        case('checkbox'):
                            $this->createQuestion($questionnaireId, $requestQuestion, $questionType, $validation, $order,true);
                            break;
                        case('textarea'):
                            $this->createQuestion($questionnaireId, $requestQuestion, $questionType, $validation, $order);
                            break;
                    }
                } else {
                    $editedQuestionType = $allRequestQuestion['type'];
                    $questionId = $key;
                    $editedQuestion = $allRequestQuestion;

                    switch ($editedQuestionType) {
                        case ('text'):
                            $this->updateQuestion($questionId, $editedQuestion, $validation, $order);
                            break;
                        case ('select'):
                            $this->updateQuestion($questionId, $editedQuestion, $validation, $order, true);
                            break;
                        case ('date'):
                            $this->updateQuestion($questionId, $editedQuestion, $validation, $order);
                            break;
                        case ('radio'):
                            $this->updateQuestion($questionId, $editedQuestion, $validation, $order, true);
                            break;
                        case ('checkbox'):
                            $this->updateQuestion($questionId, $editedQuestion, $validation, $order, true);
                            break;
                        case ('textarea'):
                            $this->updateQuestion($questionId, $editedQuestion, $validation, $order);
                            break;
                    }
                }
            }
        }

        return redirect()
            ->route('cooperation.admin.cooperation.coordinator.questionnaires.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.edit.success'));
    }

    /**
     * Store a questionnaire, after this the user will get redirected to the edit page and he can add questions to the questionnaire
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(QuestionnaireRequest $request)
    {
        $this->authorize('store', Questionnaire::class);

        $questionnaireNameKey = Uuid::uuid4();

        $questionnaireNameTranslations = $request->input('questionnaire.name');
        $stepId = $request->input('questionnaire.step_id');

        $questionnaire = Questionnaire::create([
            'name' => $questionnaireNameKey,
            'step_id' => $stepId,
            'cooperation_id' => HoomdossierSession::getCooperation(),
            'is_active' => false
        ]);

        foreach ($questionnaireNameTranslations as $locale => $questionnaireNameTranslation) {
            if (empty($questionnaireNameTranslation)) {
                $questionnaireNameTranslation = current(array_filter($questionnaireNameTranslations));
            }
            Translation::create([
                'key' =>  $questionnaireNameKey,
                'language' => $locale,
                'translation' => $questionnaireNameTranslation,
            ]);
        }

        return redirect()->route('cooperation.admin.cooperation.coordinator.questionnaires.edit', ['id' => $questionnaire->id]);
    }

    /**
     * Detele a question (softdelete)
     *
     * @param Request $request
     * @return int
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $questionId = $request->question_id;
        Question::find($questionId)->delete();

        return 202;
    }

    /**
     * Check if the translations from the request are empty
     *
     * @param $translations
     * @return bool
     */
    protected function isEmptyTranslation(array $translations) : bool
    {
        foreach($translations as $locale => $translation) {
            if (!is_null($translation)) {
                return false;
            }
        }
        return true;
    }

    protected function isNotEmptyTranslation(array $translations) : bool
    {
        return !$this->isEmptyTranslation($translations);
    }


    /**
     * Set the active status from a questionnaire
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function setActive(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');
        $active = $request->get('questionnaire_active');
        $questionnaire = Questionnaire::find($questionnaireId);

        $this->authorize('setActiveStatus', $questionnaire);

        if ($active == "true") {
            $active = true;
        } else {
            $active = false;
        }

        $questionnaire->is_active = $active;
        $questionnaire->save();

        return $questionnaireId;

    }
}
