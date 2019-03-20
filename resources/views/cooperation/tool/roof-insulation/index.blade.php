@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('roof-insulation.title.title'))

@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.roof-insulation.store', ['cooperation' => $cooperation]) }}">

        {{csrf_field()}}
        @include('cooperation.tool.includes.interested', ['type' => 'element'])
        <div class="row">
            <div id="current-situation" class="col-md-12">
                @include('cooperation.tool.includes.section-title', ['translation' => 'roof-insulation.title', 'id' => 'roof-insulation',])

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space {{ $errors->has('building_roof_types') ? ' has-error' : '' }}">
                            <label for="building_roof_types" class="control-label">
                                <i data-toggle="modal" data-target="#roof-type-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                                   aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('roof-insulation.current-situation.roof-types.title')}}
                            </label>
                            <br>
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'checkbox', 'inputValues' => $roofTypes, 'userInputValues' => $currentRoofTypesForMe, 'userInputColumn' => 'roof_type_id'])
                                @foreach($roofTypes as $roofType)
                                    <label class="checkbox-inline">
                                        <input data-calculate-value="{{$roofType->calculate_value}}"
                                               type="checkbox" name="building_roof_types[]"
                                               value="{{ $roofType->id }}"
                                               @if(in_array($roofType->id, old('building_roof_types',[ \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'roof_type_id') ])))
                                               checked="checked"
                                                @endif
                                                {{--@if((is_array(old('building_roof_types')) && in_array($roofType->id, old('building_roof_types'))) ||
                                                ($currentRoofTypes->contains('roof_type_id', $roofType->id)) ||
                                                ($features->roofType->id == $roofType->id)) checked @endif--}}
                                        >
                                        {{ $roofType->name }}
                                    </label>
                                @endforeach
                            @endcomponent

                            @if ($errors->has('building_roof_types'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('building_roof_types') }}</strong>
                                </span>
                            @endif

                            @component('cooperation.tool.components.help-modal')
                                {{\App\Helpers\Translation::translate('roof-insulation.current-situation.roof-types.help')}}
                            @endcomponent
                        </div>
                    </div>
                </div>

                <div class="if-roof">
                    <div class="row">
                        <div class="col-md-12">

                                @component('cooperation.tool.components.step-question', ['id' => 'building_features.roof_type_id', 'translation' => 'roof-insulation.current-situation.main-roof', 'required' => false])

                                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $roofTypes, 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputModel' => 'roofType', 'userInputColumn' => 'id'])
                                        <select id="main_roof" class="form-control"
                                                name="building_features[roof_type_id]">
                                            @foreach($roofTypes as $roofType)
                                                @if($roofType->calculate_value < 5)
                                                    <option @if(old('building_features.roof_type_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'roof_type_id')) == $roofType->id) selected="selected"
                                                            @endif value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @endcomponent

                                @endcomponent
                        </div>
                    </div>

                    {{--@foreach(['flat', 'pitched'] as $roofCat)--}}
                    @foreach($roofTypes->where('calculate_value', '<', 5) as $roofType)

                        <?php $roofCat = $roofType->short; ?>

                        <div class="{{ $roofCat }}-roof">

                            @include('cooperation.tool.includes.section-title', [
              'translation' => 'roof-insulation.'.$roofCat.'-roof.situation-title',
              'id' => 'roof-situation-title'
          ])
                            <div class="row">
                            <!-- is the {{ $roofCat }} roof insulated? -->
                                <div class="col-sm-12 col-md-12">

                                    @component('cooperation.tool.components.step-question', ['id' => $roofCat .'_roof_insulation', 'name' => 'building_roof_types.' . $roofCat . '.element_value_id', 'translation' => 'roof-insulation.current-situation.is-'.$roofCat.'-roof-insulated'])

                                        @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $roofInsulation->values, 'userInputValues' => $building->roofTypes()->where('roof_type_id', $roofType->id)->forMe()->get(), 'userInputColumn' => 'element_value_id'])
                                            <select id="{{ $roofCat }}_roof_insulation" class="form-control"
                                                    name="building_roof_types[{{ $roofCat }}][element_value_id]">
                                                @foreach($roofInsulation->values as $insulation)
                                                    @if($insulation->calculate_value < 6)
                                                        <option data-calculate-value="{{$insulation->calculate_value}}"
                                                                @if($insulation->id == old('building_roof_types.' . $roofCat . '.element_value_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'element_value_id'))) selected="selected"
                                                                @endif value="{{ $insulation->id }}">{{ $insulation->value }}</option>
                                                        {{--<option data-calculate-value="{{$insulation->calculate_value}}" @if($insulation->id == old('building_roof_types.' . $roofCat . '.element_value_id') || (isset($currentCategorizedRoofTypes[$roofCat]['element_value_id']) && $currentCategorizedRoofTypes[$roofCat]['element_value_id'] == $insulation->id)) selected @endif value="{{ $insulation->id }}">{{ $insulation->value }}</option>--}}
                                                    @endif
                                                @endforeach
                                            </select>
                                        @endcomponent

                                    @endcomponent
                                </div>
                            </div>
                            @include('cooperation.tool.includes.savings-alert', ['buildingElement' => $roofCat])
                            {{--<div class="{{$roofCat}}-hideable">--}}
                            <div class="row">
                                <div class="col-sm-12 col-md-6 roof-surface-inputs">
                                    @component('cooperation.tool.components.step-question', ['id' => 'building_roof_types.' . $roofCat . '.roof_surface', 'translation' => 'roof-insulation.current-situation.'.$roofCat.'-roof-surface', 'required' => true])

                                        @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'input', 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 'userInputColumn' => 'roof_surface'])
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                                            <input type="text" class="form-control"
                                                   name="building_roof_types[{{ $roofCat }}][roof_surface]"
                                                   value="{{ old('building_roof_types.' . $roofCat . '.roof_surface', \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'roof_surface')) }}">
                                            {{--<input type="text" class="form-control" name="building_roof_types[{{ $roofCat }}][roof_surface]" value="{{isset($currentCategorizedRoofTypes[$roofCat]['roof_surface']) ? $currentCategorizedRoofTypes[$roofCat]['roof_surface'] : old('building_roof_types.' . $roofCat . '.roof_surface')}}">--}}
                                        @endcomponent

                                    @endcomponent

                                </div>
                                <div class="col-sm-12 col-md-6">

                                    @component('cooperation.tool.components.step-question', ['id' => 'building_roof_types.' . $roofCat . '.insulation_roof_surface', 'translation' => 'roof-insulation.current-situation.insulation-'.$roofCat.'-roof-surface', 'required' => true])

                                        @component('cooperation.tool.components.input-group',
                                    ['inputType' => 'input', 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 'userInputColumn' => 'insulation_roof_surface'])
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                                            <input type="text" class="form-control"
                                                   name="building_roof_types[{{ $roofCat }}][insulation_roof_surface]"
                                                   value="{{ old('building_roof_types.' . $roofCat . '.insulation_roof_surface', \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'insulation_roof_surface')) }}">
                                            {{--<input type="text"  class="form-control" name="building_roof_types[{{ $roofCat }}][insulation_roof_surface]" value="{{isset($currentCategorizedRoofTypes[$roofCat]['insulation_roof_surface']) ? $currentCategorizedRoofTypes[$roofCat]['insulation_roof_surface'] : old('building_roof_types.' . $roofCat . '.insulation_roof_surface')}}">--}}
                                        @endcomponent
                                    @endcomponent
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    @component('cooperation.tool.components.step-question', ['id' => 'building_roof_types.' . $roofCat . '.extra.zinc_replaced_date', 'translation' => 'roof-insulation.current-situation.zinc-replaced', 'required' => false])

                                        @component('cooperation.tool.components.input-group',
                                    ['inputType' => 'input', 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 'userInputColumn' => 'extra.zinc_replaced_date'])
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.year')</span>
                                            <input type="text" class="form-control"
                                                   name="building_roof_types[{{ $roofCat }}][extra][zinc_replaced_date]"
                                                   value="{{ old('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date', \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'extra.zinc_replaced_date')) }}">
                                        @endcomponent
                                    @endcomponent

                                </div>
                            </div>
                            <div class="row cover-bitumen">
                                <div class="col-md-12">
                                    @component('cooperation.tool.components.step-question', ['id' => 'building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date', 'translation' => 'roof-insulation.current-situation.bitumen-insulated', 'required' => false])

                                        @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'input', 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 'userInputColumn' => 'extra.bitumen_replaced_date'])
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.year')</span>
                                            <input type="text" class="form-control"
                                                   name="building_roof_types[{{ $roofCat }}][extra][bitumen_replaced_date]"
                                                   value="{{ old('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date', \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'extra.bitumen_replaced_date')) }}">
                                            {{--<input type="text" class="form-control" name="building_roof_types[{{ $roofCat }}][extra][bitumen_replaced_date]" value="{{ old('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date', $default) }}">--}}
                                        @endcomponent

                                    @endcomponent

                                </div>
                            </div>

                            @if($roofCat == 'pitched')

                                <div class="row cover-tiles">
                                    <div class="col-md-12">
                                        @component('cooperation.tool.components.step-question', ['id' => 'building_roof_types.' . $roofCat . '.extra.tiles_condition', 'translation' => 'roof-insulation.current-situation.in-which-condition-tiles', 'required' => false])

                                            @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'select', 'inputValues' => $roofTileStatuses, 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat] ,'userInputColumn' => 'extra.tiles_condition'])
                                                <select id="tiles_condition" class="form-control"
                                                        name="building_roof_types[{{ $roofCat }}][extra][tiles_condition]">
                                                    @foreach($roofTileStatuses as $roofTileStatus)
                                                        <option @if($roofTileStatus->id == old('building_roof_types.' . $roofCat . '.extra.tiles_condition', \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'extra.tiles_condition'))) selected="selected"
                                                                @endif value="{{ $roofTileStatus->id }}">{{ $roofTileStatus->name }}</option>
                                                        {{--<option @if($roofTileStatus->id == old('building_roof_types.' . $roofCat . '.extra.tiles_condition', $default)) selected  @endif value="{{ $roofTileStatus->id }}">{{ $roofTileStatus->name }}</option>--}}
                                                    @endforeach
                                                </select>
                                            @endcomponent

                                        @endcomponent

                                    </div>
                                </div>

                            @endif


                            <div class="{{$roofCat}}-hideable">
                                <div class="row">
                                    <div class="col-md-6">

                                        @component('cooperation.tool.components.step-question', ['id' => 'building_roof_types.' . $roofCat . '.extra.measure_application_id', 'translation' => 'roof-insulation.'.$roofCat.'-roof.insulate-roof', 'required' => false])

                                            <?php
                                            $default = isset($currentCategorizedRoofTypes[$roofCat]['extra']['measure_application_id']) ? $currentCategorizedRoofTypes[$roofCat]['extra']['measure_application_id'] : 0;
                                            ?>

                                            @component('cooperation.tool.components.input-group',
                                            ['inputType' => 'select', 'inputValues' => $measureApplications[$roofCat], 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat] ,'userInputColumn' => 'extra.measure_application_id', 'customInputValueColumn' => 'measure_name'])
                                                <select id="flat_roof_insulation" class="form-control"
                                                        name="building_roof_types[{{ $roofCat }}][measure_application_id]">
                                                    <option value="0" @if($default == 0) selected @endif>
                                                        {{\App\Helpers\Translation::translate('roof-insulation.measure-application.no.title')}}
                                                    </option>
                                                    @foreach($measureApplications[$roofCat] as $measureApplication)
                                                        <option @if($measureApplication->id == old('building_roof_types.' . $roofCat . '.extra.measure_application_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'extra.measure_application_id'))) selected="selected"
                                                                @endif value="{{ $measureApplication->id }}">{{ $measureApplication->measure_name }}</option>
                                                        {{--<option @if($measureApplication->id == old('building_roof_types.' . $roofCat . '.extra.measure_application_id', $default)) selected @endif value="{{ $measureApplication->id }}">{{ $measureApplication->measure_name }}</option>--}}
                                                    @endforeach
                                                </select>
                                            @endcomponent

                                        @endcomponent
                                    </div>
                                    <div class="col-md-6">
                                        @component('cooperation.tool.components.step-question', ['id' => 'building_roof_types.' . $roofCat . '.building_heating_id', 'translation' => 'roof-insulation.'.$roofCat.'-roof.situation', 'required' => false])


                                            @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'select', 'inputValues' => $heatings, 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat] ,'userInputColumn' => 'building_heating_id'])
                                                <select id="flat_roof_situation" class="form-control"
                                                        name="building_roof_types[{{ $roofCat }}][building_heating_id]">
                                                    @foreach($heatings as $heating)
                                                        @if($heating->calculate_value < 5)
                                                            <option @if($heating->id == old('building_roof_types.' . $roofCat . '.building_heating_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->roofTypes()->where('roof_type_id', $roofType->id), 'building_heating_id'))) selected="selected"
                                                                    @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                                            {{--<option @if($heating->id == old('building_roof_types.' . $roofCat . '.building_heating_id', $default)) selected @endif value="{{ $heating->id }}">{{ $heating->name }}</option>--}}
                                                        @endif
                                                    @endforeach
                                                </select>@endcomponent

                                        @endcomponent
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    @component('cooperation.tool.components.step-question', ['id' => 'building_roof_types.'.$roofCat.'.extra.comment', 'translation' => 'general.specific-situation', 'required' => false])

                                        <?php
                                        $default = isset($currentCategorizedRoofTypes[$roofCat]['extra']['comment']) ? $currentCategorizedRoofTypes[$roofCat]['extra']['comment'] : old('building_roof_types.' . $roofCat . '.extra.comment');
                                        ?>


                                        <textarea name="building_roof_types[{{ $roofCat }}][extra][comment]" id=""
                                                  class="form-control">{{ $default }}</textarea>
                                    @endcomponent

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @foreach(['flat', 'pitched'] as $roofCat)
            <div class="row">

                <div class="costs {{ $roofCat }}-roof col-md-12">

                    <div class="row">

                        <div class="col-md-12">
                            @include('cooperation.tool.includes.section-title', [
                                'translation' => 'roof-insulation.'.$roofCat.'.costs.title',
                                'id' => $roofCat.'costs'
                            ])
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.gas', ['id' => $roofCat, 'step' => $currentStep->slug.'.'.$roofCat])
                        </div>

                        <div class="col-md-4 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.co2', ['id' => $roofCat, 'step' => $currentStep->slug.'.'.$roofCat])
                        </div>
                        <div class="col-md-4 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.savings-in-euro', ['id' => $roofCat])
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.indicative-costs', ['id' => $roofCat])
                        </div>
                        <div class="col-md-4">
                            @component('cooperation.tool.components.step-question', ['id' => 'indicative-costs-id', 'translation' => 'roof-insulation.'.$roofCat.'.indicative-costs-replacement', 'required' => false])
                                <div class="input-group">
                                            <span class="input-group-addon"><i
                                                        class="glyphicon glyphicon-euro"></i></span>
                                    <input type="text" id="{{ $roofCat }}_replace_cost"
                                           class="form-control disabled" disabled="" value="0">
                                </div>
                            @endcomponent
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 @if($roofCat == 'pitched') cover-tiles @endif">
                            @component('cooperation.tool.components.step-question', ['id' => 'indicative-replacement-year-info', 'translation' => 'roof-insulation.'.$roofCat.'.indicative-replacement.year', 'required' => false])
                                <div class="input-group">
                                        <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-calendar"></i></span>
                                    <input type="text" id="{{ $roofCat }}_replace_year"
                                           class="form-control disabled" disabled="" value="">
                                </div>
                            @endcomponent
                        </div>
                        <div class="col-md-4 {{$roofCat}}-hideable">
                            @include('cooperation.layouts.indication-for-costs.comparable-rent', ['id' => $roofCat])
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


        @foreach(['flat', 'pitched'] as $roofCat)
            @include('cooperation.tool.includes.comment', [
              'collection' => collect($currentCategorizedRoofTypesForMe[$roofCat]),
              'commentColumn' => 'extra.comment',
              'translation' => [
                  'title' => 'general.specific-situation.title',
                  'help' => 'general.specific-situation.help'
              ]
            ])
        @endforeach
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download=""
                                   href="{{asset('storage/hoomdossier-assets/Maatregelblad_Dakisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Dakisolatie.pdf')))))}}</a>
                            </li>
                        </ol>
                    </div>
                </div>
                <hr>
                @if(!\App\helpers\HoomdossierSession::isUserObserving())
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left"
                           href="{{route('cooperation.tool.floor-insulation.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" class=" btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
                    @endif
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {


            $('select[name*=element_value_id]').trigger('change');

            $("select, input[type=radio], input[type=text], input[type=number], input[type=checkbox]").change(formChange);

            function formChange() {

                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.roof-insulation.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {
                        if (!data.hasOwnProperty('flat') && !data.hasOwnProperty('pitched')) {
                            $(".if-roof").hide();
                        } else {

                            $(".if-roof").show();
                        }

                        // default
                        //$(".cover-zinc").hide();
                        $(".flat-roof .cover-bitumen").hide();
                        $(".pitched-roof .cover-bitumen").hide();

                        if (data.hasOwnProperty('flat')) {
                            $(".flat-roof").show();
                            $(".flat-roof .cover-bitumen").show();


                            //if (data.flat.hasOwnProperty('type') && data.flat.type === 'zinc'){
                            //    $(".cover-zinc").show();
                            //}
                            if (data.flat.hasOwnProperty('savings_gas')) {
                                $("input#flat_savings_gas").val(Math.round(data.flat.savings_gas).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.flat.hasOwnProperty('savings_co2')) {
                                $("input#flat_savings_co2").val(Math.round(data.flat.savings_co2).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.flat.hasOwnProperty('savings_money')) {
                                $("input#flat_savings_money").val(Math.round(data.flat.savings_money).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.flat.hasOwnProperty('cost_indication')) {
                                $("input#flat_cost_indication").val(Math.round(data.flat.cost_indication).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.flat.hasOwnProperty('interest_comparable')) {
                                $("input#flat_interest_comparable").val(data.flat.interest_comparable);
                            }
                            if (data.flat.hasOwnProperty('replace')) {
                                if (data.flat.replace.hasOwnProperty('year')) {
                                    $("input#flat_replace_year").val(data.flat.replace.year);
                                }
                                if (data.flat.replace.hasOwnProperty('costs')) {
                                    $("input#flat_replace_cost").val(Math.round(data.flat.replace.costs).toLocaleString('{{ app()->getLocale() }}'));
                                }
                            }
                        } else {

                            $(".flat-roof").hide();
                        }

                        $(".cover-tiles").hide();
                        if (data.hasOwnProperty('pitched')) {


                            $(".pitched-roof").show();
                            if (data.pitched.hasOwnProperty('type')) {

                                if (data.pitched.type === 'tiles') {
                                    $(".cover-tiles").show();
                                    $(".pitched-roof .cover-bitumen").hide();
                                }
                                if (data.pitched.type === 'bitumen') {
                                    $(".pitched-roof .cover-bitumen").show();
                                }
                            }
                            if (data.pitched.hasOwnProperty('savings_gas')) {
                                $("input#pitched_savings_gas").val(Math.round(data.pitched.savings_gas).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.pitched.hasOwnProperty('savings_co2')) {
                                $("input#pitched_savings_co2").val(Math.round(data.pitched.savings_co2).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.pitched.hasOwnProperty('savings_money')) {
                                $("input#pitched_savings_money").val(Math.round(data.pitched.savings_money).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.pitched.hasOwnProperty('cost_indication')) {
                                $("input#pitched_cost_indication").val(Math.round(data.pitched.cost_indication).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.pitched.hasOwnProperty('interest_comparable')) {
                                $("input#pitched_interest_comparable").val(data.pitched.interest_comparable);
                            }
                            if (data.pitched.hasOwnProperty('replace')) {
                                if (data.pitched.replace.hasOwnProperty('year')) {
                                    $("input#pitched_replace_year").val(data.pitched.replace.year);
                                }
                                if (data.pitched.replace.hasOwnProperty('costs')) {
                                    $("input#pitched_replace_cost").val(Math.round(data.pitched.replace.costs).toLocaleString('{{ app()->getLocale() }}'));
                                }
                            }
                        } else {
                            $(".pitched-roof").hide();
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            }

            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');

        });


        $('input[name*=roof_surface]').on('change', function () {
            var insulationRoofSurface = $(this).parent().parent().parent().next().find('input');
            if (insulationRoofSurface.length > 0) {
                if ($(insulationRoofSurface).val().length == 0 || $(insulationRoofSurface).val() == "0,0" || $(insulationRoofSurface).val() == "0.00") {
                    $(insulationRoofSurface).val($(this).val())
                }
            }
        });


        $('select[name^=interest]').on('change', function () {
            $('select[name*=element_value_id]').trigger('change');
        });
        $('select[name*=element_value_id]').on('change', function () {
            var interestedCalculateValue = $('#interest_element_{{$roofInsulation->id}} option:selected').data('calculate-value');
            var elementCalculateValue = $(this).find(':selected').data('calculate-value');

            if (elementCalculateValue >= 3 /* && interestedCalculateValue <= 2 */) {
                if ($(this).attr('name').includes('flat')) {
                    $('.flat-hideable').hide();
                    $('#flat-info-alert').find('.alert').removeClass('hide');
                } else if ($(this).attr('name').includes('pitched')) {
                    $('.pitched-hideable').hide();
                    $('#pitched-info-alert').find('.alert').removeClass('hide');
                }
            } else {
                if ($(this).attr('name').includes('flat')) {
                    $('.flat-hideable').show();
                    $('#flat-info-alert').find('.alert').addClass('hide');
                } else if ($(this).attr('name').includes('pitched')) {
                    $('.pitched-hideable').show();
                    $('#pitched-info-alert').find('.alert').addClass('hide');
                }
            }

        });

    </script>
@endpush