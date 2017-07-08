@extends('chronos::admin')

@section('content')
    <header class="subheader">
        <h1>{!! trans('chronos.scaffolding::interface.Dashboard') !!}</h1>
    </header><!--/.subheader -->
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <h2 class="panel-title">{!! trans('chronos.scaffolding::interface.Dashboard') !!}</h2>
                    <p class="no-results">{!! trans('chronos.scaffolding::interface.Please use the menu on the left to navigate.') !!}</p>
                </div>
            </div>
        </div>
    </div>
@endsection