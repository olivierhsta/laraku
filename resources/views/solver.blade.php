@extends('layouts.master')

@section('content')
<div class="row row-desktop">
    <sudoku-input class="col-desktop-7"></sudoku-input>
</div>
<div class="row row-desktop m-t-20">
    <sudoku-grid class="col-desktop-6 col-tablet-12 col-mobile-12"></sudoku-grid>
    <control-pad></control-pad>
</div>
@endsection
