@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.my-account.notification-settings.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.my-account.notification-settings.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.my-account.notification-settings.index.table.columns.interval')</th>
                            <th>@lang('woningdossier.cooperation.my-account.notification-settings.index.table.columns.last-notified-at')</th>
                            <th>@lang('woningdossier.cooperation.my-account.notification-settings.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($notificationSettings as $i => $notificationSetting)
                            <tr>
                                <td>{{ $notificationSetting->type->name }}</td>
                                <td>{{ $notificationSetting->interval->name }}</td>
                                <td>{{ is_null($notificationSetting->last_notified_at) ? __('woningdossier.cooperation.my-account.notification-settings.index.table.never-sent') : $notificationSetting->last_notified_at->format('Y-m-d') }}</td>
                                <td><a href="{{route('cooperation.my-account.notification-settings.show', ['id' => $notificationSetting->id])}}" class="btn btn-default">
                                        <i class="glyphicon glyphicon-th"></i>
                                    </a>
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