<div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100 my-4 max-w-sm">
    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $title ?? 'Default Title' }}</h3>
    <p class="text-gray-600">{{ $content ?? 'This is a sample card created via a shortcode using get_template_part.' }}
    </p>
    @if(isset($link))
        <a href="{{ $link }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 font-medium">Read More
            &rarr;</a>
    @endif
</div>