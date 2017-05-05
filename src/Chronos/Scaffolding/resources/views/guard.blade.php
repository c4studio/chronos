<!DOCTYPE html>
<html lang="en">
<head>
    <title>{!! trans('chronos.scaffolding::interface.Administration') !!} | {{ env('APP_NAME', 'Your Chronos Site') }}</title>

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="shortcut icon" href="{{ asset('chronos/favicon.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('chronos/favicon.png') }}" type="image/png">

    <link href="{{ asset('chronos/css/guard.css') }}" rel="stylesheet" />
</head>
<body>

<div class="auth-box">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">
                <div class="marginB60 text-center"><img class="logo" src="{{ asset('chronos/img/logo-knockout.svg') }}" alt="{{ env('APP_NAME', 'Chronos') }}" width="200" /></div>
                    @yield('content')
            </div>
        </div>
    </div>
</div>
<a class="credits" href="http://c4studio.ro" target="_blank">by <img src="{{ asset('chronos/img/logo-c4studio.svg') }}" alt="C4studio" height="30" /></a>

</body>
</html>