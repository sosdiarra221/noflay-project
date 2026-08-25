<!DOCTYPE html>
<html lang="fr">

<meta charset="utf-8" />
<title>@yield('title', ' | RH')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="Module RH" name="description" />

<!-- layout setup -->
<script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/k_favicon_32x.png') }}">

@yield('css')
@include('partials.rh.head-css')

<body>

    @include('partials.rh.header')
    @include('partials.rh.sidebar')


    <main class="app-wrapper">
        <div class="container-fluid">
            @include('partials.page-title')

            @yield('content')

            @include('partials.switcher')
            @include('partials.scroll-to-top')
            @include('partials.footer')

            @include('partials.rh.vendor-scripts')

            @yield('js')

</body>

</html>
