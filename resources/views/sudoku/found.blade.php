@if (@$found)
    <div class="pl-3" style="overflow-y: auto; height:45em; width:20em;">
        @php ksort($found); @endphp
        @foreach ($found as $method => $cells)
            @php $id = strtolower(str_replace(' ', '_', $method)); @endphp
            <button class="btn btn-link" data-toggle="collapse" data-target="#{{$id}}">{{$method}}</button>
            <br />
            <div id="{{$id}}" class="collapse pl-3">
                @php ksort($cells); @endphp
                @foreach ($cells as $cell => $value)
                    {{ "Cell " }} <b>{{$cell}}</b> {{" contains "}} <b>{{ $value }}</b>
                    <br />
                @endforeach
            </div>
        @endforeach
    </div>
@endif
