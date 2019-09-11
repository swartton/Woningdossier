<?php
    // some pages need a different layout / structure then the data gives us.
    // its easier, faster and more readable to do it in this way then do magic on all the array keys.
    // however, we should avoid this as much a possible otherwise the code will be bloated
    // spoiler: it became bloated.

    // calculations
    $calculationsForStep = $dataForStep['calculation'] ?? [];
    unset($dataForStep['calculation']);
?>

@switch($stepSlug)
    @case('insulated-glazing')
        @include('cooperation.pdf.user-report.steps.insulated-glazing')
    @break

    @case('roof-insulation')
        @include('cooperation.pdf.user-report.steps.roof-insulation')
    @break

    @default
        @component('cooperation.pdf.components.new-page')
        <div class="container">

            @include('cooperation.pdf.user-report.parts.step-intro')

            @include('cooperation.pdf.user-report.parts.filled-in-data')

            @include('cooperation.pdf.user-report.parts.insulation-advice')

            @include('cooperation.pdf.user-report.parts.indicative-costs-and-measures')

            @include('cooperation.pdf.user-report.parts.advices')

            @include('cooperation.pdf.user-report.parts.comments')
        </div>
    @endcomponent
    @break

@endswitch
