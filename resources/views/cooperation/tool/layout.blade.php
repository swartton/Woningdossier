@extends('cooperation.layouts.app')


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                @if (session('coaching'))
                    <div class="alert alert-success alert-dismissible show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        U vult nu de tool in voor {{\App\Models\User::find(session('user_id'))->first_name}}
                    </div>
                @endif
                @include('cooperation.tool.progress')
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @if($step->hasQuestionnaires())
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#main-tab" data-toggle="tab">{{$step->name}}</a></li>
                    @foreach($step->questionnaires as $questionnaire)
                        <li><a href="#questionnaire-{{$questionnaire->id}}" data-toggle="tab">{{$questionnaire->name}}</a></li>
                    @endforeach
                </ul>
                @endif

                <div class="tab-content">
                    @if($step->hasQuestionnaires())
                        @foreach($step->questionnaires as $questionnaire)
                            <div class="panel tab-pane panel-default" id="questionnaire-{{$questionnaire->id}}">
                                <div class="panel-heading">
                                    <h3>
                                        {{$questionnaire->name}}
                                    </h3>

                                        <button id="submit-form-top-right" class="pull-right btn btn-primary">
                                                @lang('default.buttons.next')
                                        </button>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="panel-body">
                                    @foreach($questionnaire->questions as $question)
                                        @switch($question->type)

                                            @case('text')
                                                @include('cooperation.tool.questionnaires.text', ['question' => $question])
                                                @break
                                            @case('textarea')
                                                @include('cooperation.tool.questionnaires.textarea', ['question' => $question])
                                                @break
                                            @case('select')
                                                @include('cooperation.tool.questionnaires.select', ['question' => $question])
                                                @break

                                        @endswitch
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="panel tab-pane active tab-pane panel-default" id="main-tab">
                        <div class="panel-heading">
                            <h3>
                                @yield('step_title', '')
                            </h3>

                            @if(!in_array(Route::currentRouteName(), ['cooperation.tool.index', 'cooperation.tool.my-plan.index']))
                                <button id="submit-form-top-right" class="pull-right btn btn-primary">
                                    @if(in_array(Route::currentRouteName(), ['cooperation.tool.ventilation-information.index', 'cooperation.tool.heat-pump.index']))
                                        @lang('default.buttons.next-page')
                                    @else
                                        @lang('default.buttons.next')
                                    @endif
                                </button>
                            @else
                                @if(in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']))
                                    <a href="{{ route('cooperation.tool.my-plan.export', ['cooperation' => $cooperation]) }}" class="pull-right btn btn-primary">
                                        @lang('woningdossier.cooperation.tool.my-plan.download')
                                    </a>
                                @endif
                            @endif
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body">
                            @yield('step_content', '')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $('#submit-form-top-right').click(function () {
            // There will only be 1 form inside the panel body, submit it
            $('.panel-body form button[type=submit]').click();
        })
    </script>
    <script src="{{ asset('js/are-you-sure.js') }}"></script>

    @if(!in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']))
    <script>
        $("form.form-horizontal").areYouSure();
    </script>
    @endif

@endpush