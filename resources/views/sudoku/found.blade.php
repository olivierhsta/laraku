@if (@$found)
    <div class="pl-3" style="overflow-y: auto; height:45em; width:20em;">
        @foreach ($found as $step => $cell)
            @foreach ($cell as $cell_nb => $attr)
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
                    <u>{{"Step ".$step." :"}}</u>{{$attr["method"] . " " . $attr["action"] . " "}}<b>{{$values}}</b>{{" on cell"}} <b>{{$cell_nb}}</b> <br /><br />
                </span>
            @endforeach
        @endforeach
    </div>
@endif
