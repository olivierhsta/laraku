@extends('layouts.master')

@section('content')
<div class="container">
    <div class="jumbotron text-center">
        <h1 class="display-3">{{ __('Sudoku solver') }}</h1>
        <p class="lead">{{__('Enter your grid')}}</p>
        <hr class="my-4">
        <form action="{{route('solver')}}" metdod="POST">
            <div class="row">
                <input type="text" class="form-control col-10" placeholder="81-digit grid"/>
                <button class="btn btn-primary col-2 form-control">Submit</button>
            </div>

        </form>
    </div>
</div>
@endsection
