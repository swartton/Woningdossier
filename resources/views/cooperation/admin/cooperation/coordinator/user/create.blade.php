@extends('cooperation.admin.cooperation.coordinator.layouts.app')
@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <form action="{{route('cooperation.admin.cooperation.coordinator.user.store')}}" id="register" method="post"  >
                    {{csrf_field()}}
                    <input id="addressid" name="addressid" type="text" value="" style="display:none;">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <label for="first-name">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.first-name')</label>
                                    <input value="{{old('first_name')}}"  type="text" class="form-control" name="first_name" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.first-name')...">
                                    @if ($errors->has('first_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                    <label for="last_name">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.last-name')</label>
                                    <input  value="{{old('last_name')}}"  type="text" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.last-name')..." name="last_name">
                                    @if ($errors->has('last_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--<div class="row">--}}
                            {{--<div class="col-sm-12">--}}

                                {{--<div class="panel panel-default">--}}
                                    {{--<div class="panel-heading">--}}
                                        {{--<h4 class="panel-title">--}}
                                            {{--<a data-toggle="collapse" href="#password">--}}
                                                {{--<span class="glyphicon glyphicon-plus"></span>--}}
                                                {{--@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.password.header')--}}
                                            {{--</a>--}}
                                        {{--</h4>--}}
                                    {{--</div>--}}
                                    {{--<div id="password" class="panel-collapse collapse">--}}
                                        {{--<div class="panel-body">--}}
                                            {{--<div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} ">--}}
                                                {{--<label for="password">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.password.label')</label>--}}
                                                {{--<input id="password" type="password" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.password.placeholder')" name="password">--}}
                                                {{--@if ($errors->has('password'))--}}
                                                    {{--<span class="help-block">--}}
                                                        {{--<strong>{{ $errors->first('password') }}</strong>--}}
                                                    {{--</span>--}}
                                                {{--@else--}}
                                                    {{--<span class="help-block">--}}
                                                        {{--<strong>@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.password.help')</strong>--}}
                                                    {{--</span>--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.email')</label>
                                    <input type="email" value="{{old('email')}}" class="form-control" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.email')..." name="email">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group" {{ $errors->has('roles') ? ' has-error' : '' }}>
                                    <label for="roles">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.roles')</label>
                                    <select name="roles[]" class="roles form-control" id="roles" multiple="multiple">
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->human_readable_name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('roles'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('roles') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="resident-info" class="col-md-6">
                        <div class="row">
                            <div class="form-group{{ $errors->has('postal_code') || $errors->has('number') ? ' has-error' : '' }}">
                                <div class="col-md-4">
                                    <label for="postal_code" class="">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.postal-code')<span class="">*</span></label>

                                    <input id="postal_code" type="text" class="form-control" name="postal_code" value="{{old('postal_code')}}"  >

                                    {{--@if ($errors->has('postal_code'))--}}
                                        {{--<span class="help-block">--}}
                                            {{--<strong>{{ $errors->first('postal_code') }}</strong>--}}
                                        {{--</span>--}}
                                    {{--@endif--}}

                                </div>
                                <div class="col-md-4">
                                    <label for="number" class="">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.number')<span class="">*</span></label>

                                    <input id="number" type="text" class="form-control" name="number" value="{{ old('number') }}"  >

                                    {{--@if ($errors->has('number'))--}}
                                        {{--<span class="help-block">--}}
                                            {{--<strong>{{ $errors->first('number') }}</strong>--}}
                                        {{--</span>--}}
                                    {{--@endif--}}
                                </div>
                                <div class="col-md-4">
                                    <label for="house_number_extension" class="">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.house-number-extension')</label>
                                    <input id="house_number_extension" type="text" class="form-control" name="house_number_extension" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.house-number-extension')" value="{{ old('house_number_extension') }}" >

                                    {{--@if ($errors->has('house_number_extension'))--}}
                                        {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('house_number_extension') }}</strong>--}}
                                    {{--</span>--}}
                                    {{--@endif--}}
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-sm-12">

                                <div class="form-group{{ $errors->has('street') ? ' has-error' : '' }}">
                                    <label for="street" class="">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.street')<span class="">*</span></label>

                                    <input id="street" type="text" class="form-control" name="street" value="{{ old('street') }}"  >

                                    @if ($errors->has('street'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('street') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-sm-12">

                                <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                                    <label for="city" class="">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.city')<span class="">*</span></label>

                                    <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}"  >

                                    @if ($errors->has('city'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('city') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <button class="btn btn-primary btn-block" type="submit">@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.submit') <span class="glyphicon glyphicon-plus"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@push('js')

    <script>

        $('form').disableAutoFill();


        $('.collapse').on('shown.bs.collapse', function(){
            $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hidden.bs.collapse', function(){
            $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });


        $(document).ready(function () {

            var oldSelectedRoles = [];

            @if(!is_null(old('roles')))
                @foreach(old('roles') as $roleId)
                    oldSelectedRoles.push('{{$roleId}}');
                @endforeach
            @endif

            $(".roles").select2({
                placeholder: "@lang('woningdossier.cooperation.admin.cooperation.coordinator.user.create.form.select-role')",
                maximumSelectionLength: Infinity
            }).select2('val', oldSelectedRoles);

        });

        // $('.roles').change(function () {
        //     // show the fill resident form if the value is 5 or contains 5
        //     if ($('.roles').val() == 5 || $('.roles').val().includes('5')) {
        //         $('#resident-info').show();
        //         // $('#resident-info input').attr('required', true);
        //         // $('#resident-info input').attr('autofocus', true);
        //     } else {
        //         $('#resident-info input').attr('required', false);
        //         $('#resident-info input').attr('autofocus', false);
        //         $('#resident-info input').val('');
        //         $('#resident-info').hide()
        //     }
        // })

    </script>
@endpush

