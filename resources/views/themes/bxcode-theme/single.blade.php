{!! get_header() !!}

<article class="article">
    <div class="article-header">
        <div class="container-narrow">
            <div class="article-meta">
                @if($post->categories && $post->categories->count() > 0)
                    <a href="{{ url('/category/' . $post->categories->first()->slug) }}" class="article-category">
                        {{ $post->categories->first()->name }}
                    </a>
                @endif
                <span class="article-date">
                    {{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}
                </span>
                @php
                    $wordCount = str_word_count(strip_tags($content));
                    $readTime = ceil($wordCount / 200); // Average reading speed: 200 words/minute
                @endphp
                <span class="article-read-time">{{ $readTime }} min read</span>
            </div>
            <h1 class="article-title">{{ $post->title }}</h1>
            @if(!empty($post->excerpt))
                <p class="article-subtitle">
                    {{ $post->excerpt }}
                </p>
            @endif

            <div class="article-author">
                <img src="{{ $post->author->avatar_url }}"
                    alt="{{ $post->author->display_name ?? $post->author->name ?? 'Author' }}" class="author-avatar">
                <div class="author-info">
                    <div class="author-name">{{ $post->author->display_name ?? $post->author->name ?? 'Author' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Article Image -->
    @if($post->featured_image)
        <div class="article-featured-image">
            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}">
        </div>
    @endif

    <!-- Article Content -->
    <div class="article-content">
        <div class="container-narrow">
            <div class="article-body">
                {!! $content !!}
            </div>

            <!-- Author Card -->
            <div class="author-card">
                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->display_name ?? 'Author' }}"
                    class="author-card-avatar">
                <div class="author-card-content">
                    <h3 class="author-card-name">{{ $post->author->display_name ?? $post->author->name ?? 'Author' }}
                    </h3>
                    <p class="author-card-bio">{{ strip_tags($post->author->bio) }}</p>
                </div>
            </div>

            <!-- Related Posts -->
            @php
                // Get related posts - recent published posts excluding current
                $relatedPosts = \App\Models\Post::where('status', 'publish')
                    ->where('id', '!=', $post->id)
                    ->latest('published_at')
                    ->limit(3)
                    ->get();
            @endphp

            @if($relatedPosts->count() > 0)
                <section class="related-posts">
                    <h2 class="section-title">Related Articles</h2>
                    <div class="related-grid">
                        @foreach($relatedPosts as $relatedPost)
                            <article class="related-card">
                                @if($relatedPost->featured_image_url)
                                    <img src="{{ $relatedPost->featured_image_url }}" alt="{{ $relatedPost->title }}">
                                @else
                                    <img src="https://placehold.co/400x250/e8e6e1/666666?text={{ urlencode($relatedPost->title) }}"
                                        alt="{{ $relatedPost->title }}">
                                @endif
                                <div class="related-content">
                                    @if($relatedPost->categories && $relatedPost->categories->count() > 0)
                                        <span class="related-category">{{ $relatedPost->categories->first()->name }}</span>
                                    @endif
                                    <h3><a href="{{ $relatedPost->url }}">{{ $relatedPost->title }}</a></h3>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</article>

{!! get_footer() !!}