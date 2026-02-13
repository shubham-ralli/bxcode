{!! get_header() !!}

<main class="main">
    <section id="category-section" class="category-section section">

        <div class="container">
            <div class="row gy-4 mb-4">

                @forelse($posts as $post)
                    @php 
                        $GLOBALS['post'] = $post; // Set global post for WP-style helpers
                    @endphp

                    <div class="col-lg-4">
                        <article class="featured-post">

                            @if($post->featured_image)
                                <div class="post-img">
                                    {{-- Using WordPress-style helper: the_post_thumbnail( 'size', attributes ) --}}
                                    @php the_post_thumbnail('full', ['class' => 'img-fluid']) @endphp
                                </div>
                            @endif
                            <div class="post-content">
                                <div class="category-meta">
                                    @foreach($post->tags as $tag)
                                        <a href="{{ url('tag/' . $tag->slug) }}" class="post-category">{{ $tag->name }}</a>
                                    @endforeach

                                    <div class="author-meta">
                                        @if($post->author)
                                            <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->display_name }}"
                                                class="author-img">
                                            <span class="author-name">{{ $post->author->display_name }}</span>
                                        @else
                                            <span class="author-name">Unknown Author</span>
                                        @endif
                                        <span
                                            class="post-date">{{ $post->published_at ? $post->published_at->format('d F Y') : $post->created_at->format('d F Y') }}</span>
                                    </div>
                                </div>
                                <h2 class="title">
                                    <a href="{{ $post->url }}">{{ $post->title }}</a>
                                </h2>
                            </div>
                        </article>
                    </div>

                @empty
                    <div class="no-posts">
                        <p>No posts found.</p>
                    </div>
                @endforelse




            </div>
        </div>


    </section>


</main>

{!! get_footer() !!}