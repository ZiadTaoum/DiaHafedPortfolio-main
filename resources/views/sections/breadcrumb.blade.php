<!-- START: Breadcrumb Area -->
<section class="breadcumb-area">
    <div class="container">
        <h2>{{ $title ?? 'Page Title' }}</h2>
        <ul>
            <li class="wow fadeInUp" data-wow-delay=".3s">
                <a href="{{ url('/') }}">Home</a>
            </li>
            <li class="wow fadeInUp" data-wow-delay=".4s">
                {{ $subtitle ?? $title ?? 'Page' }}
            </li>
        </ul>
    </div>
</section>
<!-- END: Breadcrumb Area -->
