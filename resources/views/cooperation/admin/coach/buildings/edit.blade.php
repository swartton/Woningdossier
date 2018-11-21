@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.edit.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.coach.buildings.update')}}" method="post">
                        <input type="hidden" name="private_message_id" value="{{\App\Models\BuildingCoachStatus::where('building_id', $building->id)->where('coach_id', Auth::id())->get()->last()->private_message_id}}">
                        <input type="hidden" name="building_id" value="{{$building->id}}">
                        <input type="hidden" name="building_coach_status" value="{{\App\Models\BuildingCoachStatus::getCurrentStatusKey($building->id)}}">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label>@lang('woningdossier.cooperation.admin.coach.buildings.edit.form.status')</label>
                            <div class="input-group" id="current-building-status">
                                <input disabled placeholder="@lang('woningdossier.cooperation.admin.coach.buildings.index.table.status')" type="text" class="form-control disabled" aria-label="...">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        @lang('woningdossier.cooperation.admin.coach.buildings.index.table.status')
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @foreach(__('woningdossier.cooperation.admin.coach.buildings.edit.form.options') as $buildingCoachStatusKey => $buildingCoachStatusName)
                                            @if($buildingCoachStatusKey == \App\Models\BuildingCoachStatus::STATUS_APPOINTMENT && $buildingCoachStatuses->contains('status',  \App\Models\BuildingCoachStatus::STATUS_APPOINTMENT))

                                            @elseif($buildingCoachStatusKey == \App\Models\BuildingCoachStatus::STATUS_NEW_APPOINTMENT && !$buildingCoachStatuses->contains('status', \App\Models\BuildingCoachStatus::STATUS_APPOINTMENT))

                                            @elseif($buildingCoachStatusKey == \App\Models\BuildingCoachStatus::STATUS_REMOVED)
                                                {{-- Coach is not allowed to remove it from here he can do this from the chat--}}
                                            @else
                                                <input type="hidden" value="{{$buildingCoachStatusKey}}" data-coach-status="{{$buildingCoachStatusName}}">
                                                <li><a href="javascript:;" @if(\App\Models\BuildingCoachStatus::getCurrentStatusName($building->id) == $buildingCoachStatusName) id="current" @endif >{{$buildingCoachStatusName}}</a></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div><!-- /btn-group -->
                            </div><!-- /input-group -->
                        </div>
                        <div class="form-group">
                            <label>@lang('woningdossier.cooperation.admin.coach.buildings.edit.form.appointment-date')</label>
                            <div class='input-group date' id="appointmentdate">
                                <input name="appointment_date" type='text' class="form-control" value="{{isset($buildingCoachStatus) ? $buildingCoachStatus->appointment_date : ""}}" />
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">@lang('woningdossier.cooperation.admin.coach.buildings.edit.form.submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        // not needed with the format option
        // just to be sure.
        $('#appointmentdate').datetimepicker({
            format: "YYYY-MM-DD HH:mm:ss",
            locale: '{{app()->getLocale()}}',
        });

        $(document).ready(function () {
            // put the label text from the selected option inside the input for ux
            var buildingCoachStatus = $('#current-building-status');
            var input = $(buildingCoachStatus).find('input.form-control');
            var currentStatus = $(buildingCoachStatus).find('li a[id=current]');
            var status = $(buildingCoachStatus).find('li a');
            var dropdown = $(buildingCoachStatus).find('ul');

            var inputValPrefix = '{{__('woningdossier.cooperation.admin.coach.buildings.index.table.current-status')}} ';
            $(input).val(inputValPrefix + $(currentStatus).text().trim());

            $(status).on('click', function () {
                var buildingCoachStatus = $(dropdown).find('[data-coach-status="'+$(this).text().trim()+'"]').val();

                $('input[name=building_coach_status]').val(buildingCoachStatus);
                $(input).val(inputValPrefix + $(this).text().trim());
            });
        });
    </script>
@endpush