{{-- Template Name: Page --}}
{!! get_header() !!}


<main class="main">

    <div class="page-title">
        <div class="title-wrapper">
            <h1>{{ $post->title }}</h1>
            @if(!empty($post->excerpt))
                <p class="page-lead">{{ $post->excerpt }}</p>
            @endif
        </div>
    </div>


    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </section>


</main>

{!! get_footer() !!}