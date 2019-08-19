<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @if(isset($cooperationStyle->css_url))
        <link href="{{ asset($cooperationStyle->css_url) }}" rel="stylesheet">
    @endif
    <style>
        .add-space {
            padding: 0 10px 0 10px;
        }
    </style>
    @stack('css')
</head>
<body class="@yield('page_class')">
<div id="app">

    @include('cooperation.layouts.navbar')
    @include('cooperation.layouts.messages')
    @yield('content')

</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

<script>
    class Hoomdossier {

        static round(value, bucket) {
            if (typeof bucket === "undefined") {
                bucket = 5;
            }

            return Math.round(value / bucket) * bucket;
        };

        static number_format(value, locale, decimals){
            if (typeof value === "string"){
                value = parseFloat(value);
            }
            return value.toLocaleString(locale, { minimumFractionDigits: decimals });
        };

    }

</script>

@stack('js')
</body>
</html>
