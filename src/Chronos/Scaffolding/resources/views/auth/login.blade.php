@extends('chronos::guard')

@section('content')
    {!! Form::open(['route' => 'chronos.auth.login_post', 'method' => 'POST']) !!}
        <div class="form-group @if ($errors->has('email')) has-error @endif" id="email">
            {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => trans('chronos.scaffolding::forms.Email')]) !!}
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{!! trans('chronos.scaffolding::alerts.Incorrect email or password') !!}</strong>
                    {{ $errors->first('email') }}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('password')) has-error @endif" id="password">
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('chronos.scaffolding::forms.Password')]) !!}
            <a id="reset-password" href="{{ route('chronos.auth.password_reset_request') }}" title="{!! trans('chronos.scaffolding::forms.Recover password') !!}">?</a>
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{!! trans('chronos.scaffolding::alerts.Incorrect password') !!}</strong>
                    {{ $errors->first('password') }}
                </span>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">{!! trans('Log in') !!}</button>
    {!! Form::close() !!}
@endsection