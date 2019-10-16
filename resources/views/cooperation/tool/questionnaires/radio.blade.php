<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <br>
            @component('cooperation.tool.questionnaires.components.input-group',
            ['inputType' => 'radio', 'inputValues' => $question->questionOptions, 'userInputValues' => $question->questionAnswers()->forMe()->get(), 'userInputColumn' => 'id'])
            @foreach($question->questionOptions as $option)
                <label class="radio-inline">
                    <input type="radio" name="questions[{{$question->id}}]" value="{{$option->id}}" @if(old('questions.'.$question->id, \App\Helpers\Hoomdossier::getMostCredibleValue($question->questionAnswers()->where('building_id', \App\Helpers\HoomdossierSession::getBuilding()), 'answer')) == $option->id) checked @endif>
                    {{$option->name}}
                </label>
            @endforeach
            @endcomponent
            @if ($errors->has('questions.'.$question->id))
                <span class="help-block">
                    <strong>{{ $errors->first('questions.'.$question->id) }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
