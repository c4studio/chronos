@extends('chronos::guard')

@section('content')
    <div class="notification">
        <div class="title"><span class="icon c4icon-puzzle"></span> Token Mismatch</div>
        <p class="text-center">{!! trans('chronos.scaffolding::alerts.You\'ve been pausing for too long on the previous page and the CSRF token has expired. This is for your protection. Please go back and start again.') !!}</p>
        <a class="btn" href="{{ URL::previous() }}">{!! trans('chronos.scaffolding::interface.Take me back') !!}</a>
    </div>
@endsection