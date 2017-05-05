@extends('chronos::guard')

@section('content')
    <div class="notification">
        <strong>{!! trans('chronos.scaffolding::alerts.Password successfully changed') !!}</strong>
        <p>{!! trans('chronos.scaffolding::alerts.You can now <a href=":link">login</a> using your new password. Write it down so you don\'t forget it next time.', ['link' => route('chronos.auth.login')]) !!}</p>
    </div>
@endsection