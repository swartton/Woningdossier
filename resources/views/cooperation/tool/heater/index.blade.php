@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('heater.title.title'))


@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.heater.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        @include('cooperation.tool.includes.interested', ['type' => 'service'])
        <div id="heater">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('heater.title.title')}}</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.water_comfort_id', 'translation' => 'heater.comfort-level-warm-tap-water', 'required' => false])
                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $comfortLevels, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'water_comfort_id'])
                            <select id="user_energy_habits_water_comfort_id" class="form-control"
                                    name="user_energy_habits[water_comfort_id]">
                                @foreach($comfortLevels as $comfortLevel)
                                    <option @if(old('user_energy_habits.water_comfort_id', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'water_comfort_id')) == $comfortLevel->id) selected
                                            @endif value="{{ $comfortLevel->id }}">{{ $comfortLevel->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>

            <div class="col-sm-4">
                @component('cooperation.tool.components.step-question', ['id' => 'building_heaters.pv_panel_orientation_id', 'translation' => 'heater.pv-panel-orientation-id', 'required' => false])
                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $collectorOrientations, 'userInputValues' => $currentHeatersForMe, 'userInputColumn' => 'pv_panel_orientation_id'])
                        <select id="building_heaters_pv_panel_orientation_id" class="form-control"
                                name="building_heaters[pv_panel_orientation_id]">
                            @foreach($collectorOrientations as $collectorOrientation)
                                <option @if(old('building_heaters.pv_panel_orientation_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->heater(), 'pv_panel_orientation_id')) == $collectorOrientation->id) selected="selected"
                                        @endif value="{{ $collectorOrientation->id }}">{{ $collectorOrientation->name }}</option>
                                {{--<option @if(old('building_heaters.pv_panel_orientation_id') == $collectorOrientation->id || ($currentHeater instanceof \App\Models\BuildingHeater && $currentHeater->pv_panel_orientation_id == $collectorOrientation->id)) selected @endif value="{{ $collectorOrientation->id }}">{{ $collectorOrientation->name }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>

            <div class="col-sm-4">
                @component('cooperation.tool.components.step-question', ['id' => 'building_heaters.angle', 'translation' => 'heater.angle', 'required' => false])
                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => \App\Helpers\KeyFigures\Heater\KeyFigures::getAngles(), 'userInputValues' => $currentHeatersForMe, 'userInputColumn' => 'angle'])
                        <span class="input-group-addon">&deg;</span>
                        <select id="building_heaters_angle" class="form-control" name="building_heaters[angle]">
                            @foreach(\App\Helpers\KeyFigures\Heater\KeyFigures::getAngles() as $angle)
                                <option @if(old('building_heaters.angle', \App\Helpers\Hoomdossier::getMostCredibleValue($building->heater(), 'angle')) == $angle) selected
                                        @endif value="{{ $angle }}">{{ $angle }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent

            </div>

        </div>
        <div class="row">
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question', ['id' => 'comment', 'translation' => 'general.specific-situation', 'required' => false])

                    <textarea id="additional-info" class="form-control"
                              name="comment">{{old('comment', isset($currentHeater) ? $currentHeater->comment : '')}}</textarea>
                @endcomponent
            </div>
        </div>
        @include('cooperation.tool.includes.comment', [
            'collection' => $currentHeatersForMe,
            'commentColumn' => 'comment',
            'translation' => [
              'title' => 'general.specific-situation.title',
              'help' => 'general.specific-situation.help'
            ]
        ])


        <div id="estimated-usage">
            <hr>
            <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('heater.estimated-usage.title.title')}}</h4>

            <div id="consumption" class="row">

                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'consumption-water', 'translation' => 'heater.consumption-water', 'required' => false])
                        <div class="input-group">
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.liter.title') }} / {{\App\Helpers\Translation::translate('general.unit.day.title')}}</span>
                            <input type="text" id="consumption_water" class="form-control disabled" disabled=""
                                   value="0">
                        </div>
                    @endcomponent
                </div>

                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'consumption-gas', 'translation' => 'heater.consumption-gas', 'required' => false])
                        <div class="input-group">
                            <span class="input-group-addon">m<sup>3</sup> / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                            <input type="text" id="consumption_gas" class="form-control disabled" disabled="" value="0">
                        </div>
                    @endcomponent
                </div>
            </div>
        </div>


        <div id="system-specs">
            <hr>
            <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('heater.system-specs.title')}}</h4>

            <div id="consumption" class="row">

                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'size-boiler', 'translation' => 'heater.size-boiler', 'required' => false])
                        <div class="input-group">
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.liter.title')}}</span>
                            <input type="text" id="size_boiler" class="form-control disabled" disabled=""
                                   value="0">
                        </div>
                    @endcomponent
                </div>

                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'size-collector', 'translation' => 'heater.size-collector', 'required' => false])
                        <div class="input-group">
                            <span class="input-group-addon">m<sup>2</sup></span>
                            <input type="text" id="size_collector" class="form-control disabled" disabled=""
                                   value="0">
                        </div>
                    @endcomponent
                </div>

            </div>
        </div>

        <div id="indication-for-costs">
            <hr>
            <h4 style="margin-left: -5px">{{\App\Helpers\Translation::translate('heater.indication-for-costs.title.title')}}</h4>

            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group add-space">
                        @component('cooperation.tool.components.step-question', ['id' => 'production-heat', 'translation' => 'heater.indication-for-costs.production-heat', 'required' => false])
                            <div class="input-group">
                                <span class="input-group-addon">kWh / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                                <input type="text" id="production_heat" class="form-control disabled" disabled=""
                                       value="0">
                            </div>
                        @endcomponent
                    </div>
                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'percentage-consumption', 'translation' => 'heater.indication-for-costs.percentage-consumption', 'required' => false])
                        <div class="input-group">
                            <span class="input-group-addon">%</span>
                            <input type="text" id="percentage_consumption" class="form-control disabled"
                                   disabled="" value="0">
                        </div>
                    @endcomponent
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-sm-4">
                @include('cooperation.layouts.indication-for-costs.gas', ['step' => $currentStep->slug])
            </div>
            <div class="col-sm-4">
                @include('cooperation.layouts.indication-for-costs.co2', ['step' => $currentStep->slug])
            </div>
            <div class="col-sm-4">
                @include('cooperation.layouts.indication-for-costs.savings-in-euro')
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                @include('cooperation.layouts.indication-for-costs.indicative-costs')
            </div>
            <div class="col-sm-6">
                @include('cooperation.layouts.indication-for-costs.comparable-rent')
            </div>
        </div>

        <div class="row system-performance">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <div class="alert show" role="alert">
                    <p id="performance-text"></p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                {{--<div class="panel panel-primary">--}}
                {{--<div class="panel-heading">@lang('default.buttons.download')</div>--}}
                {{--<div class="panel-body">--}}
                {{--<ol>--}}
                {{--<li><a download="" href="{{asset('storage/hoomdossier-assets/maatregelblad_warmtepomp.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/maatregelblad_warmtepomp.pdf')))))}}</a></li>--}}
                {{--</ol>--}}
                {{--</div>--}}
                {{--</div>--}}
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download=""
                                   href="{{asset('storage/hoomdossier-assets/Maatregelblad_Zonneboiler.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Zonneboiler.pdf')))))}}</a>
                            </li>
                        </ol>
                    </div>
                </div>
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left"
                           href="{{route('cooperation.tool.solar-panels.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {


            $("select, input[type=radio], input[type=text]").change(formChange);

            function formChange() {
                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.heater.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {
                        if (data.hasOwnProperty('consumption')) {
                            $("input#consumption_water").val(Math.round(data.consumption.water));
                            $("input#consumption_gas").val(Math.round(data.consumption.gas));
                        }
                        if (data.hasOwnProperty('specs')) {
                            $("input#size_boiler").val(Math.round(data.specs.size_boiler));
                            $("input#size_collector").val(data.specs.size_collector);
                        }
                        if (data.hasOwnProperty('production_heat')) {
                            $("input#production_heat").val(Math.round(data.production_heat));
                        }
                        if (data.hasOwnProperty('percentage_consumption')) {
                            $("input#percentage_consumption").val(Math.round(data.percentage_consumption));
                        }
                        if (data.hasOwnProperty('savings_gas')) {
                            $("input#savings_gas").val(Math.round(data.savings_gas));
                        }
                        if (data.hasOwnProperty('savings_co2')) {
                            $("input#savings_co2").val(Math.round(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')) {
                            $("input#savings_money").val(Math.round(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')) {
                            $("input#cost_indication").val(Math.round(data.cost_indication));
                        }
                        if (data.hasOwnProperty('interest_comparable')) {
                            $("input#interest_comparable").val(data.interest_comparable);
                        }
                        if (data.hasOwnProperty('performance')) {
                            $("#performance-text").html("<strong>" + data.performance.text + "</strong>");
                            $(".system-performance .alert").removeClass("alert-danger");
                            $(".system-performance .alert").removeClass("alert-warning");
                            $(".system-performance .alert").removeClass("alert-info");
                            $(".system-performance .alert").addClass("alert-" + data.performance.alert);
                            $(".system-performance").show();
                        } else {
                            $("#performance-text").html("");
                            $(".system-performance").hide();
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            }

            $('form').find('*').filter(':input:visible:first').trigger('change');

        });
    </script>
@endpush

