{!! get_header() !!}

<main class="main">

    <div class="container">
        <div class="row">

            <div class="col-lg-8">

                <!-- Blog Details Section -->
                <section id="blog-details" class="blog-details section">
                    <div class="container">

                        <article class="article">

                            <div class="hero-img">
                                <img src="{{ $post->featured_image_url }}" alt="Featured blog image" class="img-fluid"
                                    loading="lazy">
                                <div class="meta-overlay">
                                    <div class="meta-categories">
                                        <a href="#" class="category">Web Development</a>
                                        <span class="divider">•</span>
                                        <span class="reading-time"><i class="bi bi-clock"></i> 6 min read</span>
                                    </div>
                                </div>
                            </div>

                            <div class="article-content">
                                <div class="content-header">
                                    <h1 class="title">{{ $post->title }}</h1>

                                    @if(!empty($post->excerpt))
                                        <p class="article-subtitle">
                                            {{ $post->excerpt }}
                                        </p>
                                    @endif

                                    <div class="author-info">
                                        <div class="author-details">
                                            <img src="{{ $post->author->avatar_url }}" alt="Author" class="author-img">
                                            <div class="info">
                                                <h4>{{ $post->author->display_name ?? $post->author->name ?? 'Author' }}
                                                </h4>
                                                <span class="role">{{ $post->author->role ?? 'Author' }}</span>
                                            </div>
                                        </div>
                                        <div class="post-meta">
                                            <span class="date"><i class="bi bi-calendar3"></i>
                                                {{ $post->published_at->format('F d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="content">
                                    {!! $content !!}
                                </div>
                            </div>

                        </article>

                    </div>
                </section><!-- /Blog Details Section -->

                <!-- Blog Author Section -->
                <section id="blog-author" class="blog-author section">

                    <div class="container">
                        <div class="author-box">
                            <div class="row align-items-center">
                                <div class="col-lg-3 col-md-4 text-center">
                                    <img src="assets/img/person/person-f-12.webp" class="author-img rounded-circle"
                                        alt="" loading="lazy">

                                    <div class="author-social-links mt-3">
                                        <a href="https://twitter.com/#" class="twitter"><i
                                                class="bi bi-twitter-x"></i></a>
                                        <a href="https://linkedin.com/#" class="linkedin"><i
                                                class="bi bi-linkedin"></i></a>
                                        <a href="https://github.com/#" class="github"><i class="bi bi-github"></i></a>
                                        <a href="https://facebook.com/#" class="facebook"><i
                                                class="bi bi-facebook"></i></a>
                                        <a href="https://instagram.com/#" class="instagram"><i
                                                class="bi bi-instagram"></i></a>
                                    </div>
                                </div>

                                <div class="col-lg-9 col-md-8">
                                    <div class="author-content">
                                        <h3 class="author-name">
                                            {{ $post->author->display_name ?? $post->author->name ?? 'Author' }}
                                        </h3>
                                        <span class="author-title">{{ $post->author->role ?? 'Author' }}</span>

                                        <div class="author-bio mt-3">
                                            {{ strip_tags($post->author->bio) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </section><!-- /Blog Author Section -->



            </div>

            <div class="col-lg-4 sidebar">

                <div class="search-widget widget-item">

                    <h3 class="widget-title">Search</h3>
                    <form action="">
                        <input type="text">
                        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                    </form>

                </div>



            </div>

        </div>
    </div>

</main>

{!! get_footer() !!}