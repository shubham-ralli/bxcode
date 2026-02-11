<footer class="site-footer">
    <div class="container">
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ get_setting('site_title', 'BxCode CMS') }}. All rights reserved.
            </p>
            <div class="footer-legal">
                {!! bx_nav_menu([
    'theme_location' => 'footer',
    'container' => false,
    'menu_class' => 'footer-links'
]) !!}
            </div>
        </div>
    </div>
</footer>

{!! bx_footer() !!}
</body>

</html>