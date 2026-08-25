<!DOCTYPE html>
<html lang="fr">

<meta charset="utf-8" />
<title>@yield('title', ' | Facturation')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="Module Facturation" name="description" />

<!-- layout setup -->
<script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/k_favicon_32x.png') }}">

@yield('css')
@include('partials.facturation.head-css')

<body>

    @include('partials.facturation.header')
    @include('partials.facturation.sidebar')


    <main class="app-wrapper">
        <div class="container-fluid">
            @include('partials.page-title')

            @yield('content')

            @include('partials.switcher')
            @include('partials.scroll-to-top')
            @include('partials.footer')

            @include('partials.facturation.vendor-scripts')

            @yield('js')

</body>

</html>
