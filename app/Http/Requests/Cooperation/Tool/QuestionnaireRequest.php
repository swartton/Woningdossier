<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireRequest extends FormRequest
{
    protected $redirect;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Customize the error messages.
     *
     * @return array
     */
    public function attributes()
    {
        $request = $this->request;
        $questions = $request->get('questions');

        $attributes = [];

        if (is_array($questions) && ! empty($questions)) {
            foreach ($questions as $questionId => $questionAnswer) {
                $currentQuestion = Question::find($questionId);

                // instead of using the array key as name in validation we give a "dynamic" name
                $attributes['questions.'.$questionId] = "vraag '$currentQuestion->name'";
            }
        }

        return $attributes;
    }

    /**
     * Make the rules for the questions.
     *
     * @return array
     */
    public function makeRules()
    {
        $this->redirect = url()->previous().'/'.$this->request->get('tab_id', 'main-tab');

        $request = $this->request;
        $questions = $request->get('questions');
        $validationRules = [];

        if (is_array($questions) && ! empty($questions)) {
            // loop through the questions
            foreach ($questions as $questionId => $questionAnswer) {
                // get the current question and the validation for that question
                $currentQuestion = Question::find($questionId);
                $validation = $currentQuestion->validation;

                // nullable is still needed, in some cases the strings will be converted to null
                // if that happens sometimes would not work
                // see ConvertEmptyStringsToNull middleware class
                $rule = 'sometimes|nullable|';
                // if its required add the required rule
                if ($currentQuestion->isRequired()) {
                    $rule .= 'required|';
                }
                foreach ($validation as $mainRule => $rules) {
                    // check if there is validation for the question
                    if (! empty($validation)) {
                        // let the concat start
                        $rule .= "{$mainRule}|";

                        foreach ($rules as $subRule => $subRuleCheckValues) {
                            $rule .= "{$subRule}:";
                            foreach ($subRuleCheckValues as $subRuleCheckValue) {
                                $rule .= "{$subRuleCheckValue},";
                            }
                        }

                        // remove the last "," from the rule and replace it with a pipe
                        $rule = rtrim($rule, ',');
                        $rule .= '|';
                    }
                }
                $validationRules['questions.'.$questionId] = $rule;
            }
        }

        return $validationRules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = $this->makeRules();

        return  $rules;
    }
}
