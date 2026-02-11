<!DOCTYPE html>
<html lang="{{ get_setting('site_language', 'en') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {!! bx_head() !!}
</head>

<body id="{{ body_id($bodyId ?? '') }}"
    class="{{ body_class($bodyClass ?? '') }} bg-gray-50 flex flex-col min-h-screen">
    @include('partials.admin-bar')


    <header id="header" class="header position-relative">
        <div class="container-fluid container-xl position-relative">

            <div class="top-row d-flex align-items-center justify-content-between">
                <a href="{{ url('/') }}" style="text-decoration: none; color: inherit;">


                    <span class="logo-text h4" style="display:block;">{{ get_setting('site_title', 'BxCode') }}</span>
                    <span class="page-subtitle" style="display:block;">{{ get_setting('tagline', '') }}</span>

                </a>


                <div class="d-flex align-items-center">
                    <div class="social-links">
                        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                    </div>

                    <form class="search-form ms-4">
                        <input type="text" placeholder="Search..." class="form-control">
                        <button type="submit" class="btn"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>

        </div>

        <div class="nav-wrap">
            <div class="container d-flex justify-content-center position-relative">

                <nav id="navmenu" class="navmenu">
                    {!! bx_nav_menu([
    'theme_location' => 'primary',
    'container' => false,
    'menu_class' => 'nav-list',
    'item_class' => 'menu-link'
]) !!}

                </nav>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>
            </div>
        </div>

    </header>