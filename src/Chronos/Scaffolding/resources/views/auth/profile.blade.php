@extends('chronos::admin')

@section('content')
    <header class="subheader">
        <h1>{!! trans('chronos.scaffolding::interface.My profile') !!}</h1>
        <ul class="breadcrumbs">
            <li><span class="icon c4icon-user-profile"></span></li>
            <li class="active">{!! trans('chronos.scaffolding::interface.My profile') !!}</li>
        </ul>
    </header><!--/.subheader -->

    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-8">
                <div class="panel">
                    <h2 class="panel-title">{!! trans('chronos.scaffolding::interface.Edit profile') !!}</h2>

                    {!! Form::model(Auth::user(), ['route' => 'chronos.auth.profile_post', 'method' => 'POST']) !!}

                    <div class="form-group @if ($errors->has('firstname')) has-error @endif">
                        {!! Form::label('firstname', trans('chronos.scaffolding::forms.Firstname'), ['class' => 'control-label']) !!}
                        {!! Form::text('firstname', null, ['class' => 'form-control']) !!}
                        @if ($errors->has('firstname'))
                            <span class="help-block">{{ $errors->first('firstname') }}</span>
                        @endif
                    </div>

                    <div class="form-group @if ($errors->has('lastname')) has-error @endif">
                        {!! Form::label('lastname', trans('chronos.scaffolding::forms.Lastname'), ['class' => 'control-label']) !!}
                        {!! Form::text('lastname', null, ['class' => 'form-control']) !!}
                        @if ($errors->has('lastname'))
                            <span class="help-block">{{ $errors->first('lastname') }}</span>
                        @endif
                    </div>

                    <div class="form-group @if ($errors->has('email')) has-error @endif">
                        {!! Form::label('email', trans('chronos.scaffolding::forms.Email address'), ['class' => 'control-label']) !!}
                        {!! Form::text('email', null, ['class' => 'form-control']) !!}
                        @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="panel-footer">
                        <button type="submit" class="btn btn-primary">{!! trans('chronos.scaffolding::forms.Save') !!}</button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="panel">
                    <h2 class="panel-title">{!! trans('chronos.scaffolding::interface.Change picture') !!}</h2>

                    <ajax-upload action="{{ route('chronos.auth.update_picture') }}" crop="1:1">
                        <div class="profile-picture edit">
                            @if (Auth::user()->picture != '')
                                <img src="{{ asset('uploads/user-pictures/' . Auth::user()->picture) }}" alt="{{ Auth::user()->name }}" />
                            @else
                                <img src="{{ asset('chronos/img/no-profile-picture.png') }}" alt="{{ Auth::user()->name }}" />
                            @endif
                        </div>
                    </ajax-upload>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-8">
                <div class="panel">
                    <h2 class="panel-title">{!! trans('chronos.scaffolding::interface.Change password') !!}</h2>
                    {!! Form::open(['route' => 'chronos.auth.update_password', 'method' => 'POST']) !!}

                    <div class="form-group @if ($errors->has('current_password')) has-error @endif">
                        {!! Form::label('current_password', trans('chronos.scaffolding::forms.Current password'), ['class' => 'control-label']) !!}
                        {!! Form::password('current_password', ['class' => 'form-control']) !!}
                        @if ($errors->has('current_password'))
                            <span class="help-block">{{ $errors->first('current_password') }}</span>
                        @endif
                    </div>

                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                        {!! Form::label('password', trans('chronos.scaffolding::forms.New password'), ['class' => 'control-label']) !!}
                        {!! Form::password('password', ['class' => 'form-control']) !!}
                        @if ($errors->has('password'))
                            <span class="help-block">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif">
                        {!! Form::label('password_confirmation', trans('chronos.scaffolding::forms.Confirm new password'), ['class' => 'control-label']) !!}
                        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                        @endif
                    </div>

                    <div class="panel-footer">
                        <button type="submit" class="btn btn-primary">{!! trans('chronos.scaffolding::forms.Update') !!}</button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection



@push('content-modals')
    <crop-dialog></crop-dialog>
@endpush



@push('scripts-components')
    @include('chronos::components.crop_dialog')
@endpush



@push('styles')
<link href="{{ asset('chronos/css/cropper.css') }}" rel="stylesheet" />
@endpush

@push('scripts-post')
<script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>
@endpush