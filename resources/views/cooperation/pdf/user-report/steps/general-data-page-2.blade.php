<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.title')}}</p>
    <table class="full-width">
        <thead>
        <tr>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.planned-year')}}</th>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.interested')}}</th>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.measure')}}</th>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.costs')}}</th>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.savings')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($userActionPlanAdvices as $userActionPlanAdvice)
            <tr>
                <td>{{$userActionPlanAdvice->getAdviceYear()}}</td>
                <td>{{$userActionPlanAdvice->planned ? 'Ja' : 'Nee'}}</td>
                <td>{{$userActionPlanAdvice->measureApplication->measure_name}}</td>
                <td>{{$userActionPlanAdvice->costs}}</td>
                <td>{{$userActionPlanAdvice->savings_money}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.text')}}</p>
</div>


<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.comment-action-plan')}}</p>
    <p>data</p>
</div>

<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.calculations-are-indicative.title')}}</p>
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.calculations-are-indicative.text')}}</p>
</div>

<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.title')}}</p>
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.text', ['cooperation_name' => strtolower($cooperation->name)])}}</p>
</div>

<div class="question-answer-section">
    <h2>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.title')}}</h2>
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.lead')}}</p>
    <p>{!!\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.text')!!}</p>
</div>