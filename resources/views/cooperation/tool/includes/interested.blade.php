@foreach ($typeIds as $elementId)

    <?php $typeName =  \App\Models\Element::find($elementId)->name ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group add-space">
                <label for="house_has_insulation" class="control-label">
                    @lang('woningdossier.cooperation.tool.change-interest', ['item' => $typeName])
                </label>

                <select class="form-control" name="interest[{{$type}}][{{$elementId}}]">
                    @foreach($interests as $interest)
                        <option @if($interest->id == old('user_interest.'.$type.'.'. $elementId . '')) selected @elseif(Auth::user()->getInterestedType($type, $elementId) != null && Auth::user()->getInterestedType($type, $elementId)->interest_id == $interest->id) selected  @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('interest.'.$elementId))
                    <span class="help-block">
                        <strong>{{ $errors->first('interest.'.$elementId) }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
@endforeach