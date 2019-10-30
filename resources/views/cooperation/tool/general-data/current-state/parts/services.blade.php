<?php
    $boilerCount = 0;
?>
@foreach($services as $i => $service)

    <?php
        $iconName = array_key_exists($service->short, \App\Helpers\StepHelper::SERVICE_TO_SHORT) ? \App\Helpers\StepHelper::SERVICE_TO_SHORT[$service->short] : $service->short;
    ?>


    @if(in_array($service->short, ['hr-boiler', 'boiler']))
        <?php $boilerCount++; ?>
        @if($boilerCount == 1)
        <div class="row">
        @endif
            <div class="col-md-4 ">
                @if($boilerCount == 1)
                    <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">
                @endif
                @component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id, 'translation' => 'general-data/current-state.service.'.$service->short])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $service->values, 'userInputValues' => $building->buildingservices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])
                        <select id="service_{{ $service->id }}" class="form-control" name="service[{{ $service->id }}]">
                            {{--5 is the "vraaggestuurd" value, we need this for a checkbox--}}
                            @foreach($service->values as $serviceValue)
                                <option @if(old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingservices()->where('service_id', $service->id), 'service_value_id')) == $serviceValue->id) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
        {{-- since the last question wont come out of the services we have to add it manually when its the last boiler service--}}
        @if($boilerCount == 2)
            <div class="col-md-4">
                @component('cooperation.tool.components.step-question', ['id' => 'building_features-building_heating_application_id', 'translation' => 'general-data/current-state.building-heating-applications'])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $buildingHeatingApplications, 'userInputValues' => $myBuildingFeatures, 'userInputColumn' => 'service_value_id'])
                        <select id="building_features-building_heating_application_id" class="form-control" name="building_features[building_heating_application_id]">
                            {{--5 is the "vraaggestuurd" value, we need this for a checkbox--}}
                            @foreach($buildingHeatingApplications as $buildingHeatingApplication)
                                <option @if(old('building_features.building_heating_application_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_heating_application_id')) == $buildingHeatingApplication->id) selected="selected" @endif value="{{ $buildingHeatingApplication->id }}">{{ $buildingHeatingApplication->name }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
        </div>
        @endif
    @elseif($service->short == 'heat-pump')
        <div class="row">
            <div class="col-sm-4 ">
                <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">
                @component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id, 'translation' => 'general-data/current-state.service.'.$service->short])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $service->values, 'userInputValues' => $building->buildingservices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])
                        <select id="service_{{ $service->id }}" class="form-control" name="service[{{ $service->id }}]">
                            {{--5 is the "vraaggestuurd" value, we need this for a checkbox--}}
                            @foreach($service->values as $serviceValue)
                                <option @if(old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingservices()->where('service_id', $service->id), 'service_value_id')) == $serviceValue->id) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
        </div>
    @elseif($service->short == 'house-ventilation')
        <div class="row" id="house-ventilation">
            <div class="col-xs-12 col-sm-12 col-md-4">
                <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">
                @component('cooperation.tool.components.step-question', ['id' => 'house-ventilation-service', 'translation' => 'general-data/current-state.service.'.$service->short])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $service->values, 'userInputValues' => $building->buildingservices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])
                        <select id="house-ventilation-service" class="form-control" name="service[{{ $service->id }}]">
                            {{--5 is the "vraaggestuurd" value, we need this for a checkbox--}}
                            @foreach($service->values->where('calculate_value', '!=', 5) as $serviceValue)
                                <option data-calculate-value="{{$serviceValue->calculate_value}}" @if(old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingservices()->where('service_id', $service->id), 'service_value_id')) == $serviceValue->id) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
            <div class="mt-25">
                <div id="demand-driven" class="col-sm-offset-1 col-md-offset-0 col-xs-12 col-sm-4 col-md-3">
                    <div class="form-group add-space">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="service[{{$service->id}}][demand_driven]">
                            @lang('general-data/current-state.service.'.$service->short.'.demand-driven.title')
                        </label>
                    </div>
                </div>
                <div id="heat-recovery" class="col-xs-12 col-sm-4 col-md-3">
                    <div class="form-group add-space">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="service[{{$service->id}}][heat_recovery]">
                            @lang('general-data/current-state.service.'.$service->short.'.heat-recovery.title')
                        </label>
                    </div>
                </div>
            </div>
        </div>
    @elseif($service->short == 'total-sun-panels')
        <div class="row">
            <div class="col-md-4">
                <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">
                @component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id, 'translation' => 'general-data/current-state.service.'.$service->short])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'input', 'userInputValues' => $building->buildingServices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'extra.value'])
                        <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.pieces')</span>
                        <input type="text" id="{{ $service->short }}" class="form-control" value="{{ old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $service->id), 'extra.value')) }}" name="service[{{ $service->id }}][extra][value]">
                        {{--<input type="text" id="{{ $service->short }}" class="form-control" value="@if(old('service.' . $service->id )){{old('service.' . $service->id)}} @elseif(isset($building->buildingServices()->where('service_id', $service->id)->first()->extra['value'])){{$building->buildingServices()->where('service_id', $service->id)->first()->extra['value']}} @endif" name="service[{{ $service->id }}]">--}}
                    @endcomponent
                @endcomponent
            </div>
            <div id="optional-total-sun-panels-questions">

                <div class="col-md-4">
                    @component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id.'.extra.year', 'translation' => 'general-data/current-state.service.'.$service->short.'.year', 'required' => false])
                        @component('cooperation.tool.components.input-group', ['inputType' => 'input', 'userInputValues' => $building->buildingServices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'extra.year'])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                            <input type="text" class="form-control" name="service[{{ $service->id }}][extra][year]" value="{{ old('service.'.$service->id . '.extra.year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $service->id), 'extra.year')) }}">
                        @endcomponent
                    @endcomponent
                </div>

            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-4">
                <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">
                @component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id, 'translation' => 'general-data/current-state.service.'.$service->short])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $service->values, 'userInputValues' => $building->buildingservices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])
                        <select id="service_{{ $service->id }}" class="form-control" name="service[{{ $service->id }}]">
                            {{--5 is the "vraaggestuurd" value, we need this for a checkbox--}}
                            @foreach($service->values as $serviceValue)
                                <option @if(old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingservices()->where('service_id', $service->id), 'service_value_id')) == $serviceValue->id) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
        </div>
    @endif

@endforeach

@push('js')
    <script>
        $(document).ready(function () {
            var houseVentilationService = $('select#house-ventilation-service');
            houseVentilationService.trigger('change');

            var totalSunPanelInput = $("input#total-sun-panels");
            var optionalTotalSunPanelQuestions = $('#optional-total-sun-panels-questions');

            totalSunPanelInput.keyup(function () {
                handleSunPanelFields(totalSunPanelInput, optionalTotalSunPanelQuestions)
            });

            houseVentilationService .change(function () {
                handleVentilationFields($(this));
           });
        });

        function handleSunPanelFields(totalSunPanelInput, optionalTotalSunPanelQuestions) {
            var totalSunPanels = parseInt(totalSunPanelInput.val());
            if (totalSunPanels > 0) {
                optionalTotalSunPanelQuestions.show();
            } else {
                optionalTotalSunPanelQuestions.hide();
                if (optionalTotalSunPanelQuestions.find('input').val().trim() !== "") {
                    console.log("Adjusting sun panel year");
                    optionalTotalSunPanelQuestions.find('input').val("");
                }
            }
        }

        function handleVentilationFields(ventilationField) {
            var selectedCalculateValue = ventilationField.find('option:selected').data('calculate-value');
            var demandDriven = $('#demand-driven');
            var heatRecovery = $('#heat-recovery');

            demandDriven.hide();
            demandDriven.removeProp('selected');
            heatRecovery.hide();
            heatRecovery.removeProp('selected');

            if (selectedCalculateValue === 2) {
                demandDriven.show();
            } else if (selectedCalculateValue === 3 || selectedCalculateValue === 4) {
                demandDriven.show();
                heatRecovery.show();
            }
        }
    </script>
@endpush