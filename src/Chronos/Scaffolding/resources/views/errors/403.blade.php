@extends('chronos::guard')

@section('content')
    <div class="notification">
        <div class="title"><span class="icon c4icon-lock-locked-2"></span> 403</div>
        <p class="text-center">{!! trans('chronos.scaffolding::alerts.You don\'t have the necessary privileges to access this page. Go back to the homepage and start again.') !!}</p>
        <a class="btn" href="{{ url('/admin') }}">{!! trans('chronos.scaffolding::interface.Take me home') !!}</a>
    </div>
@endsection