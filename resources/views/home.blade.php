@extends('layouts.master')

@section('content')
<div class="container">
    <div class="jumbotron text-center">
        <h1 class="display-3">{{ config('app.name', 'Sudoku') }}</h1>
        <p class="lead">{{__('A human-like Sudoky-solver')}}</p>
        <hr class="my-4">
        <a href="{{ route('solver') }}" class="btn btn-primary">{{__('Get started')}}</a>
    </div>
</div>
@endsection
