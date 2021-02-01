<label for="">@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-label')</label>
@foreach(config('hoomdossier.supported_locales') as $locale)
    <?php $translation = $question->getTranslation('name', $locale) instanceof \App\Models\Translation ? $question->getTranslation('name', $locale)->translation : ''; ?>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][question][{{$locale}}]"
                   placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')"
                   type="text" value="{{old("questions.{$question->id}.question.{$locale}", $translation)}}"
                   class="form-control">
        </div>
    </div>
@endforeach

<?php $questionOptionCount = 0; ?>
@foreach($question->questionOptions as $questionOption)
    <?php $questionOptionCount++; ?>
    <label for="">@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-option') {{$questionOptionCount}}</label>
    <div class="option-group">
        @foreach(config('hoomdossier.supported_locales') as $locale)
            <?php $translation = $questionOption->getTranslation('name', $locale) instanceof \App\Models\Translation ? $questionOption->getTranslation('name', $locale)->translation : ''; ?>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">{{$locale}}</span>
                    <input name="questions[{{$question->id}}][options][{{$questionOption->id}}][{{$locale}}]"
                           placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')"
                           type="text" value="{{old("questions.{$question->id}.options.{$questionOption->id}.{$locale}", $translation)}}"
                           class="form-control">
                </div>
            </div>
        @endforeach
    </div>
@endforeach

{{-- For every existing question, we want to add a new option group field --}}

{{-- quick maths--}}
<label for="">@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-option') {{$questionOptionCount + 1}} @lang('cooperation/admin/cooperation/questionnaires.shared.add')</label>
<?php $uuid = \Ramsey\Uuid\Uuid::uuid4(); ?>
@foreach(config('hoomdossier.supported_locales') as $locale)
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][options][{{$uuid}}][{{$locale}}]"
                   placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')"
                   type="text" class="form-control option-text">
        </div>
    </div>
@endforeach
