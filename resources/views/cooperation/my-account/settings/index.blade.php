@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @lang('woningdossier.cooperation.my-account.settings.index.header')
                    </div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.my-account.settings.update') }}" autocomplete="off">
                            {{ method_field('PUT')  }}
                            {{ csrf_field() }}


                            <div class="form-group{{ $errors->has('building.street') ? ' has-error' : '' }}">
                                <label for="street" class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.building.street')</label>

                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="building[street]" value="{{ old('building.street', $building->street) }}" required autofocus>

                                    @if ($errors->has('building.street'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('building.street') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('building.number') ? ' has-error' : '' }}">
                                <label for="building.number" class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.building.number')</label>

                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="building[number]" value="{{ old('building.number', $building->number) }}" required autofocus>

                                    @if ($errors->has('building.number'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('building.number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('building.extension') ? ' has-error' : '' }}">
                                <label for="building.extension" class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.building.extension')</label>

                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="building[extension]" value="{{ old('building.extension', $building->extension) }}" autofocus>

                                    @if ($errors->has('building.extension'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('building.extension') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('building.postal_code') ? ' has-error' : '' }}">
                                <label for="building.postal_code" class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.building.postal-code')</label>

                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="building[postal_code]" value="{{ old('building.postal_code', $building->postal_code) }}" required autofocus>

                                    @if ($errors->has('building.postal_code'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('building.postal_code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('building.city') ? ' has-error' : '' }}">
                                <label for="building.city" class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.building.city')</label>

                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="building[city]" value="{{ old('building.city', $building->city) }}" required autofocus>

                                    @if ($errors->has('building.city'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('building.city') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('user.first_name') ? ' has-error' : '' }}">
                                <label for="first_name" class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.first-name')</label>

                                <div class="col-md-8">
                                    <input id="first_name" type="text" class="form-control" name="user[first_name]"
                                           value="{{ old('first_name', $user->first_name) }}" required autofocus>

                                    @if ($errors->has('user.first_name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user.first_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('user.last_name') ? ' has-error' : '' }}">
                                <label for="last_name"
                                       class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.last-name')</label>

                                <div class="col-md-8">
                                    <input id="last_name" type="text" class="form-control" name="user[last_name]"
                                           value="{{ old('last_name', $user->last_name) }}" required autofocus>

                                    @if ($errors->has('user.last_name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user.last_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('user.email') ? ' has-error' : '' }}">
                                <label for="email"
                                       class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.e-mail')</label>

                                <div class="col-md-8">
                                    <input id="email" type="email" class="form-control" name="user[email]" value="{{ old('email', $user->email) }}" required autofocus>

                                    @if ($errors->has('user.email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user.email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('user.phone_number') ? ' has-error' : '' }}">
                                <label for="phone_number"
                                       class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.phone_number')</label>

                                <div class="col-md-8">
                                    <input id="phone_number" type="text" class="form-control" name="user[phone_number]"
                                           value="{{ old('phone_number', $user->phone_number) }}" autofocus>

                                    @if ($errors->has('user.phone_number'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user.phone_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('user.current_password') ? ' has-error' : '' }}">
                                <label for="current_password"
                                       class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.current-password')</label>

                                <div class="col-md-8">
                                    <input id="current_password" type="password" class="form-control"
                                           name="user[current_password]">

                                    @if ($errors->has('user.current_password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user.current_password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('user.password') ? ' has-error' : '' }}">
                                <label for="password"
                                       class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.new-password')</label>

                                <div class="col-md-8">
                                    <input id="password" type="password" class="form-control" name="user[password]">

                                    @if ($errors->has('user.password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user.password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password-confirm"
                                       class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.new-password-confirmation')</label>

                                <div class="col-md-8">
                                    <input id="password-confirm" type="password" class="form-control"
                                           name="user[password_confirmation]">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('woningdossier.cooperation.my-account.settings.index.form.submit')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.settings.reset-file.header')</div>

                    <div class="panel-body">
                        @lang('woningdossier.cooperation.my-account.settings.reset-file.description')
                        <form class="form-horizontal" method="POST"
                              action="{{ route('cooperation.my-account.settings.reset-file', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="reset-file"
                                       class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.reset-file.label')</label>
                                <div class="col-md-8">
                                    <a id="reset-account" class="btn btn-danger">
                                        @lang('woningdossier.cooperation.my-account.settings.reset-file.submit')
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @can('delete-own-account')
            <div class="row">
                <div class="col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-heading">@lang('woningdossier.cooperation.my-account.settings.destroy.header')</div>

                        <div class="panel-body">
                            <form class="form-horizontal" method="POST"
                                  action="{{ route('cooperation.my-account.settings.destroy', ['cooperation' => $cooperation]) }}">
                                {{ method_field('DELETE') }}
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label for="delete-account"
                                           class="col-md-4 control-label">@lang('woningdossier.cooperation.my-account.settings.destroy.label')</label>
                                    <div class="col-md-8">
                                        <button type="submit" id="delete-account" class="btn btn-danger">
                                            @lang('woningdossier.cooperation.my-account.settings.destroy.submit')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan


    </div>
@endsection


@push('js')
    <script>
        var areYouSure = '@lang('woningdossier.cooperation.my-account.settings.reset-file.are-you-sure')';
        $('#reset-account').click(function () {
            if (confirm(areYouSure)) {
                $(this).closest('form').submit();
            } else {
                return false;
                event.preventDefault();
            }
        });
        var areYouSureToDestroy = '@lang('woningdossier.cooperation.my-account.settings.destroy.are-you-sure')';
        $('#delete-account').click(function () {
            if (confirm(areYouSureToDestroy)) {
                $(this).closest('form').submit();
            } else {
                return false;
                event.preventDefault();
            }
        })
    </script>
@endpush