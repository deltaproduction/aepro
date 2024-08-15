<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>
    <link rel="stylesheet" href="{{ asset('css/standart/main.css') }}">
    @yield('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="layout-header_wrapper">
        <div class="layout-header">
            <div class="layout-header__logo">
                <a href="/">
                    <img src="{{ asset('images/logo.svg') }}" width="170" height="36" alt="Academy">
                </a>
            </div>
            <div class="layout-header__info">
                <div>{{auth()->user()->last_name}} {{auth()->user()->first_name}} {{auth()->user()->middle_name}}</div>
                <div class="layout-header__info_down-icon"></div>
            </div>
            <div class="layout-header__info_menu_wrapper">
                <div class="layout-header__info_menu">
                    @yield('dropdown-items')
                </div>
            </div>
        </div>
    </div>

    <div class="modal-window_back">
        <div class="modal-window_wrapper">
            <div class="modal-window">
                <div class="modal-window__header">
                    <div class="modal-window__header_title">@yield('mw-title')</div>
                    <div class="modal-window__header_close">x</div>
                </div>
                <div class="modal-window__content">
                    @yield('mw-content')
                </div>
            </div>
        </div>
    </div>

    <div class="layout-container_wrapper">
        <div class="layout-container">
            @yield('container')
        </div>
    </div>
</body>
<script type="text/javascript" src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/toggle_dropdown.js') }}"></script>
<script type="text/javascript" type="module"  src="{{ asset('js/modal_window.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/logout.js') }}"></script>
@yield('scripts')
</html>
