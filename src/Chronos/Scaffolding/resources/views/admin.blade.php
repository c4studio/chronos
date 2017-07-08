<!DOCTYPE html>
<html lang="en">
<head>
    <title>{!! trans('chronos.scaffolding::interface.Administration') !!} | {{ env('APP_NAME', 'Your Chronos Site') }}</title>

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="shortcut icon" href="{{ asset('chronos/favicon.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('chronos/favicon.png') }}" type="image/png">

    <link href="{{ asset('chronos/css/admin.css?v=' . Config::get('chronos.version')) }}" rel="stylesheet" />
    @stack('styles')
</head>
<body>

<div id="chronos">

<div class="header">
    <div class="header-left">
        <button class="btn offcanvas-toggle" v-bind:class="{ active: offcanvas }" type="button" accesskey="m" v-on:click.prevent="toggleOffcanvas"><span class="icon c4icon-bars"></span></button
        ><div class="dropdown user-account">
            <a class="btn" id="user-account-toggle" data-toggle="dropdown" accesskey="u" aria-expanded="true">
                @if (Auth::user()->picture != '')
                <img class="profile-picture" src="{{ asset('uploads/user-pictures/' . Auth::user()->picture) }}" alt="{{ Auth::user()->name }}" /><span class="user-name">{{ Auth::user()->name }}</span>
                @else
                <img class="profile-picture" src="{{ asset('chronos/img/no-profile-picture.png') }}" alt="{{ Auth::user()->name }}" /><span class="user-name">{{ Auth::user()->name }}</span>
                @endif
            </a>
            <ul class="dropdown-menu user-account-dropdown" role="menu" aria-labelledby="user-account-toggle">
                <li><a href="{{ route('chronos.auth.profile') }}"><span class="icon c4icon-user-profile"></span>{!! trans('chronos.scaffolding::interface.My profile') !!}</a></li>
                <li><a href="{{ route('chronos.auth.logout') }}"><span class="icon c4icon-power"></span>{!! trans('chronos.scaffolding::interface.Log out') !!}</a></li>
            </ul>
        </div>
    </div>
    <div class="header-right">
        <div class="brand">
            <span class="version">v{{ Config::get('chronos.version') }}</span><a href="/" class="logo"><img src="{{ asset('chronos/img/logo-negative.svg') }}" height="30" alt="" /></a>
        </div>
    </div>
</div>

@include('chronos::components.offcanvas')

<div class="content-wrapper">
    <div class="content" v-bind:class="{ offset: offcanvas }" v-on:click="closeOffcanvas">
        @yield('content')
    </div><!--/.content -->
</div><!--/.content-wrapper -->

@include('chronos::components.alerts')
@include('chronos::components.loader')

@stack('content-modals')

</div>


<!-- Load vue.js, components and app specific scripts -->
<script src="https://unpkg.com/flatpickr/dist/flatpickr.js"></script>
<script src="https://unpkg.com/vue/dist/vue.min.js"></script>
<script src="https://unpkg.com/vue-resource/dist/vue-resource.min.js"></script>
<script>
    Vue.http.interceptors.push(function(request, next) {
        request.headers.set('X-CSRF-TOKEN', '{{ csrf_token() }}');
        next();
    });
</script>
@stack('scripts-pre')
<script src="{{ asset('chronos/js/utils.min.js?v=' . Config::get('chronos.version')) }}"></script>
@include('chronos::components.ajax_upload')
@include('chronos::components.alert')
@include('chronos::components.autocomplete')
@stack('scripts-components')
<script src="{{ asset('chronos/js/admin.min.js?v=' . Config::get('chronos.version')) }}"></script>
<script src="{{ asset('chronos/js/bootstrap-native.min.js') }}"></script>
@stack('scripts-post')

</body>
</html>