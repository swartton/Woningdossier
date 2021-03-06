@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('solar-panels.title.title'))

@section('step_content')
    <form  method="POST" action="{{ route('cooperation.tool.solar-panels.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}

        @include('cooperation.tool.includes.interested', [
            'translation' => 'solar-panels.index.interested-in-improvement', 'interestedInType' => \App\Models\Step::class, 'interestedInId' => $currentStep->id,
        ])
        <div id="solar-panels">
            <div class="row">
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.amount_electricity', 'translation' => 'solar-panels.electra-usage', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $energyHabitsOrderedOnInputSourceCredibility, 'userInputColumn' => 'amount_electricity'])
                            <span class="input-group-addon">kWh / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                            <input type="number" min="0" class="form-control" name="user_energy_habits[amount_electricity]"
                                   value="{{ old('user_energy_habits.amount_electricity', Hoomdossier::getMostCredibleValueFromCollection($energyHabitsOrderedOnInputSourceCredibility, 'amount_electricity', 0)) }}"/>
                            {{--<input type="number" min="0" class="form-control" name="user_energy_habits[amount_electricity]" value="{{ old('user_energy_habits.amount_electricity', $amountElectricity) }}" />--}}
                        @endcomponent
                    @endcomponent


                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_pv_panels.peak_power', 'translation' => 'solar-panels.peak-power', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => \App\Helpers\KeyFigures\PvPanels\KeyFigures::getPeakPowers(), 'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility, 'userInputColumn' => 'peak_power'])
                            <span class="input-group-addon">Wp</span>
                            <select id="building_pv_panels_peak_power" class="form-control"
                                    name="building_pv_panels[peak_power]">
                                @foreach(\App\Helpers\KeyFigures\PvPanels\KeyFigures::getPeakPowers() as $peakPower)
                                    <option @if(old('building_pv_panels.peak_power', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'peak_power') == $peakPower)) selected
                                            @endif value="{{ $peakPower }}">{{ $peakPower }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                    @endcomponent

                </div>
            </div>

            <div class="row advice">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert alert-info show" role="alert">
                        <p id="solar-panels-advice"></p>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-4">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_pv_panels.number', 'translation' => 'solar-panels.number', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility, 'userInputColumn' => 'number'])
                            <span class="input-group-addon">@lang('general.unit.pieces.title')</span>
                            <input type="text" class="form-control" name="building_pv_panels[number]"
                                   value="{{ old('building_pv_panels.number', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'number', 0)) }}"/>
                        @endcomponent

                    @endcomponent

                </div>

                <div class="col-sm-4">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_pv_panels.pv_panel_orientation_id', 'translation' => 'solar-panels.pv-panel-orientation-id', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $pvPanelOrientations, 'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility, 'userInputColumn' => 'pv_panel_orientation_id'])
                            <select id="building_pv_panels_pv_panel_orientation_id" class="form-control"
                                    name="building_pv_panels[pv_panel_orientation_id]">
                                @foreach($pvPanelOrientations as $pvPanelOrientation)
                                    <option @if(old('building_pv_panels.pv_panel_orientation_id', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'pv_panel_orientation_id')) == $pvPanelOrientation->id) selected="selected"
                                            @endif value="{{ $pvPanelOrientation->id }}">{{ $pvPanelOrientation->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent

                </div>

                <div class="col-sm-4">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_pv_panels.angle', 'translation' => 'solar-panels.angle', 'required' => false])

                        <?php \App\Helpers\KeyFigures\PvPanels\KeyFigures::getAngles(); ?>
                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => \App\Helpers\KeyFigures\PvPanels\KeyFigures::getAngles(), 'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility, 'userInputColumn' => 'angle'])
                            <span class="input-group-addon">&deg;</span>
                            <select id="building_pv_panels_angle" class="form-control"
                                    name="building_pv_panels[angle]">
                                @foreach(\App\Helpers\KeyFigures\PvPanels\KeyFigures::getAngles() as $angle)
                                    <option @if(old('building_pv_panels.angle', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'angle')) == $angle) selected="selected"
                                            @endif value="{{ $angle }}">{{ $angle }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                    @endcomponent

                </div>

            </div>

            <div class="row total-power">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert alert-info show" role="alert">
                        <p id="solar-panels-total-power"></p>
                    </div>
                </div>
            </div>


            <div id="indication-for-costs">
                <hr>
                @include('cooperation.tool.includes.section-title', [
                    'translation' => 'solar-panels.indication-for-costs.title',
                    'id' => 'indication-for-costs',
                ])

                <div id="costs" class="row">
                    <div class="col-sm-4">
                        @component('cooperation.tool.components.step-question', ['id' => 'yield-electricity', 'translation' => 'solar-panels.indication-for-costs.yield-electricity', 'required' => false])
                            <div class="input-group">
                                <span class="input-group-addon">kWh / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                                <input type="text" id="yield_electricity" class="form-control disabled"
                                       disabled="" value="0">
                            </div>
                        @endcomponent
                    </div>

                    <div class="col-sm-4">
                            @component('cooperation.tool.components.step-question', ['id' => 'raise-own-consumption', 'translation' => 'solar-panels.indication-for-costs.raise-own-consumption', 'required' => false])
                                <div class="input-group">
                                    <span class="input-group-addon">%</span>
                                    <input type="text" id="raise_own_consumption" class="form-control disabled"
                                           disabled="" value="0">
                                </div>
                            @endcomponent
                    </div>
                    <div class="col-sm-4">
                        @include('cooperation.layouts.indication-for-costs.co2', ['translation' => 'solar-panels.index.costs.co2'])
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro',[
                                'translation' => 'solar-panels.index.savings-in-euro'
                            ])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs',[
                                'translation' => 'solar-panels.index.indicative-costs'
                            ])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent',[
                                'translation' => 'solar-panels.index.comparable-rent'
                            ])
                </div>
            </div>
        </div>

        <div class="row system-performance">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <div class="alert show" role="alert">
                    <p id="performance-text"></p>
                </div>
            </div>
        </div>


        @include('cooperation.tool.includes.comment', [
             'translation' => 'solar-panels.index.specific-situation'
         ])


        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">{{\App\Helpers\Translation::translate('general.download.title')}}</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download=""
                                   href="{{asset('storage/hoomdossier-assets/Maatregelblad_Zonnepanelen.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Zonnepanelen.pdf')))))}}</a>
                            </li>
                        </ol>
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
                    url: '{{ route('cooperation.tool.solar-panels.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {
                        if (data.hasOwnProperty('advice')) {
                            $("#solar-panels-advice").html("<strong>" + data.advice + "</strong>");
                            $(".advice").show();
                        } else {
                            $("#solar-panels-advice").html("");
                            $(".advice").hide();
                        }

                        if (data.hasOwnProperty('yield_electricity')) {
                            $("input#yield_electricity").val(hoomdossierRound(data.yield_electricity));
                        }
                        if (data.hasOwnProperty('raise_own_consumption')) {
                            $("input#raise_own_consumption").val(hoomdossierRound(data.raise_own_consumption));
                        }
                        if (data.hasOwnProperty('savings_co2')) {
                            $("input#savings_co2").val(hoomdossierRound(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')) {
                            $("input#savings_money").val(hoomdossierRound(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')) {
                            $("input#cost_indication").val(hoomdossierRound(data.cost_indication));
                        }
                        if (data.hasOwnProperty('interest_comparable')) {
                            $("input#interest_comparable").val(hoomdossierNumberFormat(data.interest_comparable, '{{ app()->getLocale() }}', 1));
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
                        if (data.hasOwnProperty('total_power')) {
                            $("#solar-panels-total-power").html(data.total_power);
                            $(".total-power").show();
                        } else {
                            $("#solar-panels-total-power").html("");
                            $(".total-power").hide();
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