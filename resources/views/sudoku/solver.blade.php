@extends('layouts.master')

@section('content')
<div class="container">
    <div class="jumbotron text-center">
        <h1 class="display-3">{{ __('Sudoku solver') }}</h1>
        <p class="lead">{{__('Enter your grid')}}</p>
        <hr class="my-4">
        @include('layouts.error')
        {{-- <form action="{{route('solver.solve')}}" method="POST" @submit.prevent="onSubmit">
            {{ csrf_field() }}
            <div class="row">
                <input name="grid" type="text" class="form-control col-10" placeholder="81-digit grid"/>
                <button type="submit" class="btn btn-primary col-2 form-control">Solve</button>
            </div>
        </form> --}}
    </div>

    <div class="d-flex">
        @include('sudoku.grid')
        @include('sudoku.found')
    </div>

</div>
@endsection
