<?php $questionsApplicableForValidation = ['text', 'textarea']; ?>
<div class="form-builder ui-sortable-handle panel panel-default" @isset($id) id="{{$id}}" @endisset>
    <div class="panel-heading">
        @lang('cooperation/admin/cooperation/questionnaires.shared.types.'.$question->type.'.label')
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                {{$slot}}
            </div>
            <div class="col-sm-12 question">
                <input type="hidden" name="questions[{{$question->id}}][type]" value="{{$question->type}}">
                <input type="hidden" name="questions[{{$question->id}}][question_id]" class="question_id" value="{{$question->id}}">
                @switch($question->type)

                    @case('text')
                        @include('cooperation.admin.cooperation.questionnaires.layouts.inputs.text', ['question' => $question])
                        @break
                    @case('textarea')
                        @include('cooperation.admin.cooperation.questionnaires.layouts.inputs.text', ['question' => $question])
                        @break
                    @case('date')
                        @include('cooperation.admin.cooperation.questionnaires.layouts.inputs.text', ['question' => $question])
                        @break
                    @case('select')
                        @include('cooperation.admin.cooperation.questionnaires.layouts.inputs.select', ['question' => $question])
                        @break
                    @case('radio')
                        @include('cooperation.admin.cooperation.questionnaires.layouts.inputs.select', ['question' => $question])
                        @break
                    @case('checkbox')
                        @include('cooperation.admin.cooperation.questionnaires.layouts.inputs.select', ['question' => $question])
                        @break

                @endswitch


            </div>
        </div>
        <div class="row validation-inputs">
            @if(in_array($question->type, $questionsApplicableForValidation))
                @include('cooperation.admin.cooperation.questionnaires.layouts.validation-options', ['question' => $question])
            @endif
        </div>

        <div class="row">
            <div class="col-sm-12">
                @if($question->hasNoValidation() && in_array($question->type, $questionsApplicableForValidation))
                    <a class="btn btn-primary add-validation">@lang('cooperation/admin/cooperation/questionnaires.edit.add-validation')</a>
                @endif
                @if(\App\Services\QuestionnaireService::hasQuestionOptions($question->type))
                    <a class="btn btn-primary add-option" data-id="{{$question->id}}">@lang('cooperation/admin/cooperation/questionnaires.edit.add-option')</a>
                @endif
            </div>
        </div>

    </div>
    <div class="panel-footer">
        @include('cooperation.admin.cooperation.questionnaires.layouts.form-build-panel-footer', ['question' => $question])
    </div>
</div>

