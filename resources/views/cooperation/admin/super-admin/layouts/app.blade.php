@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div id="sidebar" class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#sidebar-main" href="#sidebar-main">@lang('woningdossier.cooperation.admin.super-admin.side-nav.label') <span class="glyphicon glyphicon-text  @if(str_replace(['.coach.index', '.buildings.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span></a>
                                </h4>
                            </div>
                            <ul id="sidebar-main" class="sidebar list-group panel-collapse open collapse in collapse " aria-expanded="true">
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.index'])) active @endif"><a href="{{route('cooperation.admin.super-admin.index')}}">@lang('woningdossier.cooperation.admin.super-admin.side-nav.home')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.index'])) active @endif"><a href="{{route('cooperation.admin.super-admin.cooperations.index')}}">@lang('woningdossier.cooperation.admin.super-admin.side-nav.cooperations')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.example-buildings.index', 'cooperation.admin.super-admin.example-buildings.edit', 'cooperation.admin.super-admin.example-buildings.create'])) active @endif"><a href="{{route('cooperation.admin.super-admin.example-buildings.index')}}">@lang('woningdossier.cooperation.admin.super-admin.side-nav.example-buildings')</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                @yield('super_admin_content')
            </div>
        </div>
    </div>
@endsection

@prepend('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/responsive.bootstrap.min.css')}}">
@endprepend

@prepend('js')

    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('js/disable-auto-fill.js') }}"></script>
    <script src="{{asset('js/select2.js')}}"></script>

    <script>
        $(document).ready(function () {

            $('.collapse').on('shown.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
            }).on('hidden.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
            });
        });

        $.extend( true, $.fn.dataTable.defaults, {
            language: {
                url: "{{asset('js/datatables-dutch.json')}}"
            }
        });
    </script>
@endprepend