@extends('layouts.master')

@section('content')
<div class="row no-mobile no-tablet">
    <sudoku-grid class="col-desktop-5 col-tablet-12 col-mobile-12"></sudoku-grid>
    <control-pad></control-pad>
</div>
@endsection
