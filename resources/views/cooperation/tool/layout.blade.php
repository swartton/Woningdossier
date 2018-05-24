@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                @include('cooperation.tool.progress')
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @yield('step_title', '')

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
                                <a href="{{route('cooperation.tool.my-plan.export', ['cooperation' => $cooperation]) }}" class="pull-right btn btn-primary">
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
@endsection

@push('js')
    <script>
        $('#submit-form-top-right').click(function () {
            // There will only be 1 form inside the panel body, submit it
            $('.panel-body form').submit();
        })
    </script>
    <script src="{{ asset('js/are-you-sure.js') }}"></script>
    <script>
        $("form.form-horizontal").areYouSure();
    </script>
@endpush