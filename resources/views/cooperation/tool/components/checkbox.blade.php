@foreach($inputValues as $inputValue)
    @foreach($userInputValues as $userInputValue)
        <?php
            // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
            if (strpos($userInputColumn, ".") !== false) {
                $value = array_get($userInputValue, $userInputColumn);
            } else {
                $value = $userInputValue->$userInputColumn;
            }

            if (array_key_exists('value', $inputValue->attributesToArray())) {
                $inputName = $inputValue->value;
            } else {
                $inputName = $inputValue->name;
            }
        ?>
        @if($inputValue->id == $value)
            <li class="change-input-value" data-input-value="{{$inputValue->id}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$inputName}}</a></li>
        @endif
    @endforeach
@endforeach