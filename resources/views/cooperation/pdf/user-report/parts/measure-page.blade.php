<?php
// some pages need a different layout / structure then the data gives us.
// its easier, faster and more readable to do it in this way then do magic on all the array keys.
// however, we should avoid this as much a possible otherwise the code will be bloated
// spoiler: it became bloated.

// calculations
$calculationsForStep = $calculations[$stepShort][$subStepShort] ?? [];
?>

@switch($stepShort)
    @case('insulated-glazing')
        @include('cooperation.pdf.user-report.steps.insulated-glazing', [
            'dataForSubStep' => \App\Helpers\Arr::arrayUndot($dataForSubStep)
        ])
    @break

    @case('roof-insulation')
        @include('cooperation.pdf.user-report.steps.roof-insulation', [
            'dataForSubStep' => \App\Helpers\Arr::arrayUndot($dataForSubStep)
        ])
    @break

    @case('ventilation')
        @include('cooperation.pdf.user-report.steps.ventilation', [
            'dataForSubStep' =>$dataForSubStep
        ])
    @break

    @case('floor-insulation')
        @include('cooperation.pdf.user-report.steps.floor-insulation', [
            'dataForSubStep' =>$dataForSubStep
        ])
    @break

    @default
    @component('cooperation.pdf.components.new-page')
        <div class="container">

            @include('cooperation.pdf.user-report.parts.measure-page.step-intro')

            @include('cooperation.pdf.user-report.parts.measure-page.filled-in-data')

            {{-- since no calculations are done here --}}
            @if($stepShort != 'general-data')
                @include('cooperation.pdf.user-report.parts.measure-page.insulation-advice')

                @include('cooperation.pdf.user-report.parts.measure-page.indicative-costs-and-measures')
            @endif

            @include('cooperation.pdf.user-report.parts.measure-page.advices')

            @if(isset($commentsByStep[$stepShort][$subStepShort]) && !\App\Helpers\Arr::isWholeArrayEmpty($commentsByStep[$stepShort][$subStepShort]))
                @include('cooperation.pdf.user-report.parts.measure-page.comments', [
                    'comments' => $commentsByStep[$stepShort][$subStepShort],
                ])
            @endif
        </div>
    @endcomponent
    @break

@endswitch
