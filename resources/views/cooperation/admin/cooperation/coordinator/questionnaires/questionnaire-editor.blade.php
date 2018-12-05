@extends('cooperation.layouts.app')
@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('content')
    <section class="section">
        <div class="container">
            <form action="{{route('cooperation.admin.cooperation.coordinator.questionnaires.store')}}" method="post">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-sm-6">
                        <a id="leave-creation-tool" href="{{route('cooperation.admin.cooperation.coordinator.questionnaires.index')}}" class="btn btn-warning">
                            @lang('woningdossier.cooperation.admin.cooperation.coordinator.index.create.leave-creation-tool')
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" href="{{route('cooperation.admin.cooperation.coordinator.questionnaires.index')}}" class="btn btn-primary pull-right">
                            Opslaan
                        </button>
                   sdf </div>
                </div>
                dsf
                sdasidugadfdfs
                dfsdfsdf
                <div class="row alert-top-space">
                    <div class="col-md-3">
                        <div id="tool-box" class="list-group">
                            <a href="#" id="short-answer" class="list-group-item"><i class="glyphicon glyphicon-align-left"></i> Kort antwoord</a>
                            <a href="#" id="long-answer" class="list-group-item"><i class="glyphicon glyphicon-align-justify"></i>  Alinea</a>
                            <a href="#" id="radio-button" class="list-group-item"><i class="glyphicon glyphicon-record"></i>  Meerkeuze</a>
                            <a href="#" id="checkbox" class="list-group-item"><i class="glyphicon glyphicon-unchecked"></i>  Selectievakjes</a>
                            <a href="#" id="dropdown" class="list-group-item"><i class="glyphicon glyphicon-collapse-down"></i>  Dropdownmenu</a>
                            <a href="#" id="date" class="list-group-item"><i class="glyphicon glyphicon-calendar"></i>  Datum</a>
                            <a href="#" id="time" class="list-group-item"><i class="glyphicon glyphicon-time"></i>  Tijd</a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel">
                            <div class="panel-body" >
                                <div id="sortable">
                                    @forelse($questionnaire->questions as $question)
                                        @component('cooperation.admin.cooperation.coordinator.questionnaires.layouts.form-build-panel', ['question' => $question])

                                        @endcomponent
                                    @empty

                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('css')
    <link href="{{asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
@push('js')


    <script src="{{asset('js/bootstrap-toggle.min.js')}}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        var formBuildPanel =
            '<div class="form-builder panel panel-default">' +
                '<div class="panel-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12" id="question">' +
                            // '<div class="form-group">' +

                            // '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="row validation-rules">' +

                    '</div>' +
                '</div>' +
                '<div class="panel-footer">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                            '<div class="pull-left">' +
                                '<a><i class="glyphicon glyphicon-trash"></i></a>' +
                            '</div>' +
                            '<div class="pull-right">' +

                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        var formBuildValidation =
            '<div class="col-sm-4">' +
                '<div class="form-group">' +
                    '<select class="validation form-control" name="questions[new][][validation]" id="">' +
                        '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)' +
                            '<option value="{{$rule}}">{{$translation}}</option>' +
                        '@endforeach' +
                    '</select>' +
                '</div>' +
            '</div>' +
            '<div class="col-sm-4">' +
                '<div class="form-group">' +
                    '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)' +
                        '<select class="form-control" name="questions[new][][validation-options]" id="{{$rule}}">' +
                        '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.optional-rules.".$rule) as $optionalRule => $optionalRuleTranslation)' +
                            '<option value="{{$optionalRule}}">{{$optionalRuleTranslation}}</option>' +
                        '@endforeach' +
                        '</select>' +
                    '@endforeach' +
                '</div>' +
            '</div>';


        var sortable = $('#sortable');
        var toolBox = $('#tool-box');
        var formGroupElement = '<div class="form-group"></div>';
        var inputGroupElement = '<div class="input-group"></div>';

        var requiredCheckboxLabel = $('<label>').addClass('control-label').text('Verplicht ');

        var supportedLocales = [];

        // created two pushes, but it works.
        @foreach(config('woningdossier.supported_locales') as $locale)
        supportedLocales.push('{{$locale}}');
        @endforeach

        // we will increment the questionId each time a new panel / question is added
        // so we can use it in the name off the input and retrieve it as array in the request later on
        var questionId = 0;

        // each question input name should be structured like
        // name=question[new][][questionType]
        // if the question has "options", for instance when it is a dropdown the name should be structured like this
        // name=question[new][][options][]

        // used input for the dropdown builder
        var dropdownMenuInputElement = '<input name="" placeholder="Optie toevoegen" type="text" class="option-text form-control">';

        toolBox.find('a').on('click', function (event) {
            // always add the empty form build panel
            // we add the input types after that
            sortable.prepend(formBuildPanel);
            event.preventDefault();
        });


        toolBox.find('#short-answer').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('#question');
            var panelFooter = questionPanel.find('.panel-footer');
            var questionType = "text";
            questionId++;


            // add the for attr to the label, so when a user clicks the label the checkbox will check
            requiredCheckboxLabel.attr({
                for: 'required-'+questionId
            });

            $(supportedLocales).each(function (index, locale) {
                var fullQuestionName = 'questions[new][][question]['+locale+']';
                var hiddenInputWithInputTypeName = 'questions[new][][question]['+locale+']';
                var formGroup = $($(formGroupElement).append(inputGroupElement)).appendTo(question);

                var hiddenInputWithInputType = $('<input>').attr({
                    name: hiddenInputWithInputTypeName,
                    type: 'hidden'
                });

                var textInput = $('<input>').addClass('form-control').attr({
                    placeholder: 'Vraag',
                    name: fullQuestionName,
                    type: questionType
                });

                formGroup.find('.input-group').append(textInput);
                formGroup.find('.input-group').append(hiddenInputWithInputType);
            });


            var requiredCheckbox = $('<input>').addClass('control-label').attr({
                id: 'required-'+questionId+'',
                type: 'checkbox',
                name: 'questions[new][][required]'
            });

            panelFooter.find('.pull-right').append(requiredCheckboxLabel);
            requiredCheckbox.appendTo(panelFooter.find('.pull-right > label'));


            sortable.sortable('refresh');
            $('input, select').trigger('change');

        });

        toolBox.find('#long-answer').on('click', function () {
            var question = sortable.find('.panel').first().find('#question');
            var formGroup = question.find('.form-group');

            formGroup.append("<input class='form-control' placeholder='Stel uw vraag waar een langer antwoord voor nodig is'>");
            question.parent().parent().find('.validation-rules').append(formBuildValidation);

            sortable.sortable('refresh');
            $('input, select').trigger('change');
        });

        toolBox.find('#dropdown').on('click', function () {

            var question = sortable.find('.panel').first().find('#question');
            // first we want to add one with a default value
            question.find('.form-group').append('<input name="" placeholder="Vraag" type="text" class="form-control"><br>');
            question.find('.form-group').append('<input name="" placeholder="Optie toevoegen" value="Optie..." type="text" class="option-text form-control">');

            // add a new form group with input that only has a placehodler
            question.append($(formGroupElement).append('<input name="" placeholder="Optie toevoegen"  type="text" class="option-text form-control">'));

            // now let it autofocus to the first option input
            question.find('.option-text').first().attr('autofocus', true);

            sortable.sortable('refresh')
            $('input, select').trigger('change');
        });



        $(document).on('focusout', 'input.option-text', function (event) {
            if($(this).val() === "") {
                $(this).val('Optie...')
            }
        });

        $(document).on('focus', 'input.option-text', function (event) {

            if ($(this).val() === "") {

                var formGroup = $(this).parent().parent();

                formGroup.append($(formGroupElement).append(dropdownMenuInputElement));
            }
        });

        $('body').on('change', 'select[name*=validation]', function () {
            var selectedMainRule = $(this);

            var validationRuleRow = selectedMainRule.parent().parent().parent();

            var optionalRuleThatIsNotSelected = validationRuleRow.find('select[name*=validation-options][id!='+selectedMainRule.val()+']');
            optionalRuleThatIsNotSelected.hide();

            var optionalRule = validationRuleRow.find('select[name*=validation-options][id='+selectedMainRule.val()+']');
            optionalRule.show();
        });

        $('body').on('click', '.glyphicon-trash', function (event) {
            event.preventDefault();
            $(this).parent().parent().parent().parent().parent().parent().remove();
            return false;
        });

        $('#leave-creation-tool').on('click', function (event) {
           if (confirm('@lang('woningdossier.cooperation.admin.cooperation.coordinator.index.create.leave-creation-tool-warning')')) {

           } else {
               event.preventDefault();
               return false;
           }
        });





        $(document).ready(function () {
            var blocks = [];
            var master = $('#sortable');

            // get the id's off the blocks / panels
            $('.form-builder').each(function () {
                blocks.push($(this).attr('id'));
            });

            // make it sortable
            master.sortable({

                update: function () {

                    var order = [];

                    $(".form-builder").each(function () {
                        order.push($(this).attr('id'));
                    });

                    // create a new array with the order of the item and the navId
                    var questionOrder = blocks.map(function (questionOrder, questionId) {
                        return questionOrder, order[questionId];
                    });
                    console.log(questionOrder);

                }
            });
        });

        $('input, select').trigger('change');
    </script>
@endpush