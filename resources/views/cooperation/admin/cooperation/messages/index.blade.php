@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.messages.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.date')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.unread-messages')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildings as $building)
                            <?php
                                $user = $building->user;
                                $userExists = $user instanceof \App\Models\User;
                            ?>
                            <tr>
                                <td>{{$userExists ? $user->created_at : '-'}}</td>
                                <td>{{$userExists ? $user->getFullName() : '-'}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.buildings.show', ['buildingId' => $building->id])}}">
                                        {{$building->street}} {{$building->number}} {{$building->extension}}
                                    </a>
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td>
                                    {{\App\Models\PrivateMessageView::getTotalUnreadMessagesCountForABuildingByUserId(Auth::id())}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>

        $('#table').DataTable({
            responsive: true,
            columnDefs: [
                {responsivePriority: 4, targets: 4},
                {responsivePriority: 5, targets: 3},
                {responsivePriority: 3, targets: 2},
                {responsivePriority: 2, targets: 1},
                {responsivePriority: 1, targets: 0}
            ],
        });

    </script>
@endpush

