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


    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="{{ url('/') }}" style="text-decoration: none; color: inherit;">
                        @php 
                                                        $siteLogo = \App\Models\Setting::get('site_logo');
                            $displayHeaderText = \App\Models\Setting::get('display_header_text', true);
                        @endphp

                                                   
@if($siteLogo)
    <img src="{{ $siteLogo }}" alt="{{ get_setting('site_title') }}" class="custom-logo" style="max-height: 80px; width: auto; display: block; margin-bottom: 10px;">
@endif
                    
                        @if($displayHeaderText)
                            <span class="logo-text" style="display:block;">{{ get_setting('site_title', 'BxCode') }}</span>
                            <span class="page-subtitle" style="display:block;">{{ get_setting('tagline', '') }}</span>
                        @else
                            <span class="logo-text" style="display:none;">{{ get_setting('site_title', 'BxCode') }}</span>
                            <span class="page-subtitle" style="display:none;">{{ get_setting('tagline', '') }}</span>
                        @endif
                    </a>
                </div>

                <nav class="main-nav">
                    {!! bx_nav_menu([
    'theme_location' => 'primary',
    'container' => false,
    'menu_class' => 'nav-list',
    'item_class' => 'menu-link'
]) !!}
                </nav>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle mobile menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <div class="mobile-menu-content">
            <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Close mobile menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <nav class="mobile-nav">
                {!! bx_nav_menu([
    'theme_location' => 'primary',
    'container' => false,
    'menu_class' => 'mobile-nav-list',
    'item_class' => 'mobile-menu-link'
]) !!}
            </nav>
        </div>
    </div>