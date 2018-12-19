@if (@$found)
    <div class="pl-3" style="overflow-y: auto; height:45em; width:20em;">
        @foreach ($found as $method => $cells)
            @php $id = strtolower(str_replace(' ', '_', $method)); @endphp
            <button class="btn btn-link" data-toggle="collapse" data-target="#{{$id}}">{{$method}}</button>
            <br />
            <div id="{{$id}}" class="collapse pl-3">
                @foreach ($cells as $cell => $attr)
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
                    {{"Cell "}} <b>{{$cell}}</b> {{" " . $attr['action'] . " "}} <b>{{$values}}</b>
                    <br />
                @endforeach
            </div>
        @endforeach
    </div>
@endif
