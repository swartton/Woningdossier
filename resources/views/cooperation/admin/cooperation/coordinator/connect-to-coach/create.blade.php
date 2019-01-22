
@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.header', ['name' => $privateMessage->getSender()])

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.messages.participants.add-with-building-access')}}" method="post"  >
                        {{csrf_field()}}
                        <input type="hidden" name="building_id" value="{{$buildingId}}">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('coach') ? ' has-error' : '' }}>
                                    <label for="coach">@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.select-coach')</label>
                                    <select name="user_id" class="coach form-control" id="coach">
                                        @foreach($coaches as $coach)
                                            <option @if(old('coach') == $coach->id) selected @endif value="{{$coach->id}}">{{$coach->getFullName()}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('coach'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('coach') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-primary btn-block" type="submit">@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.submit') <span class="glyphicon glyphicon-plus"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
    @push('js')
        <script src="{{asset('js/select2.js')}}"></script>

        <script>

            var form = $('form');
            form.disableAutoFill();

            form.on('submit', function () {
                if (confirm('@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.submit-warning', ['name' => $privateMessage->getSender()])')) {

                } else {
                    event.preventDefault();

                }
            });


            $('.collapse').on('shown.bs.collapse', function(){
                $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
            }).on('hidden.bs.collapse', function(){
                $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
            });


            $(document).ready(function () {

                $(".coach").select2({
                    placeholder: "@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.select-coach')",
                    maximumSelectionLength: Infinity
                });
            });
        </script>
    @endpush

