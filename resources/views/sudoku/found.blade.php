@if (@$found)
    <div class="pl-3" style="overflow-y: auto; height:45em; width:20em;">
        @foreach ($found as $step => $attr)
            @php
            if (is_array($attr['values']))
            {
                $values = '';
                foreach ($attr['values'] as $value)
                {
                    $values .= $value . ', ';
                }
                $values = substr($values, 0, -2);
            }
            else
            {
                $values = $attr['values'];
            }
            @endphp
            <span>
                <u>{{"Step ".$step." :"}}</u>{{$attr["method"] . " " . $attr["action"] . " "}}<b>{{$values}}</b>{{" on cell"}} <b>{{$attr["cell"]}}</b>
                <br /><br />
            </span>
        @endforeach
    </div>
@endif
