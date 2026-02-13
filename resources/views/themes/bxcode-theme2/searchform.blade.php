<form role="search" method="get" class="search-form ms-4" action="{{ route('frontend.search') }}">
    <input type="text" placeholder="Search..." class="search-field form-control" value="{{ request()->get('s') }}"
        name="s">
    <button type="submit" class="btn search-submit"><i class="bi bi-search"></i></button>
</form>