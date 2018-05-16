@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3>Edit: {{ $exampleBuilding->name }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <form action="{{ route('admin.example-buildings.update', ['id' => $exampleBuilding->id]) }}" method="post">
                    {{ csrf_field() }}
                    {{ method_field("PUT") }}

                    @include('admin.example-buildings.components.names')
                    @include('admin.example-buildings.components.building-type')
                    @include('admin.example-buildings.components.cooperation')
                    @include('admin.example-buildings.components.order')
                    @include('admin.example-buildings.components.is_default')
                    @include('admin.example-buildings.components.contents')


                    <div class="form-group" style="margin-top: 5em;">
                        <input type="hidden" name="new" value="0">
                        <input type="submit" name="update" value="Update" class="btn btn-success btn-block">
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $("form").on('submit', function(e){
        var openTabId = $(".tab-content .tab-pane.active").attr('id');
        if (openTabId === 'new') {
            $("input[name='new']").val(1);
        }
        else {
            $("input[name='new']").val(0);
        }
    });
</script>
@endpush
