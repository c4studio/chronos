@extends('chronos::guard')

@section('content')
    <div class="notification">
        <div class="title"><span class="icon c4icon-unlink"></span> 404</div>
        <p class="text-center">{!! trans('chronos.scaffolding::alerts.The page you have been looking for can not be found. Go back to the homepage and start again.') !!}</p>
        <a class="btn" href="{{ url('/admin') }}">{!! trans('chronos.scaffolding::interface.Take me home') !!}</a>
    </div>
@endsection