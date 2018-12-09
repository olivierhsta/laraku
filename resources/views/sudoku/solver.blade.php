@extends('layouts.master')

@section('content')
<div class="container">
    <div class="jumbotron text-center">
        <h1 class="display-3">{{ __('Sudoku solver') }}</h1>
        <p class="lead">{{__('Enter your grid')}}</p>
        <hr class="my-4">
        @include('layouts.error')
        <form action="{{route('solver.solve')}}" method="POST">
            {{ csrf_field() }}
            <div class="row">
                <input name="grid" type="text" class="form-control col-10" placeholder="81-digit grid"/>
                <button type="submit" class="btn btn-primary col-2 form-control">Submit</button>
            </div>
        </form>
    </div>
    @if(@$grid)
        <div class="text-center">
            <table class="table table-bordered" style="width: 500px; height:500px; border:2px solid black;">
                @foreach($grid as $row)
                    <tr scope="row">
                        @foreach($row as $cell)
                                @php
                                    $style = "";
                                    if ($cell->row%3 == 0 && $cell->row != 9)
                                    {
                                        $style .= "border-bottom:2px solid black;";
                                    }
                                    if ($cell->column%3 == 0 && $cell->column != 9)
                                    {
                                        $style .= "border-right:2px solid black;";
                                    }
                                @endphp
                            <td style="{{$style}}">{{ $cell->value}}</td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    @endif


</div>
@endsection
