<!-- System Footer Injection -->
@stack('scripts')
@stack('footer')
{!! get_setting('footer_scripts') !!}

@php do_action('bx_footer'); @endphp
<!-- End System Footer Injection -->