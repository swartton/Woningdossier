<?php
    if (isset($id)) {
        $infoAlertId = $id.'_co2-info';
    } else {
        $infoAlertId = 'co2-info';
    }

    // if the step is not given, fallback to the default translation
    if (! isset($step)) {
        $step = 'general';
    }
?>
    @component('cooperation.tool.components.step-question', ['id' => $infoAlertId, 'translation' => $step.'.costs.co2'])
    <div class="input-group">
        <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.kg.title') }} / {{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
        <input type="text" id="{{isset($id) ? $id.'_' : ''}}savings_co2" class="form-control disabled" disabled="" value="0">
    </div>
    @endcomponent
