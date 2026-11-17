@include('sections.breadcrumb', ['title' => 'Blog Grids', 'subtitle' => 'Blog Details'])
<!-- START: Blog Page -->
<section class="blogs-section blog-item-wrapper">
    <div class="container">
    <div class="row">
        @php
            $gridBlogs = [
                [
                    'title' => 'Portrait of adult male working from home at night',
                    'author' => 'Tawam',
                    'category' => 'Graphic',
                    'date' => '09 March 2025',
                    'image' => 'images/blogs/blog-1.png',
                    'delay' => '.3s'
                ],
                [

                    'title' => 'Portrait of adult male working from home at night',
                    'author' => 'Tawam',
                    'category' => 'Graphic',
                    'date' => '09 March 2025',
                    'image' => 'images/blogs/blog-2.png',
                    'delay' => '.4s'
                ],
                [

                    'title' => 'Portrait of adult male working from home at night',
                    'author' => 'Tawam',
                    'category' => 'Graphic',
                    'date' => '09 March 2025',
                    'image' => 'images/blogs/blog-1.png',
                    'delay' => '.7s'
                ],
                [

                    'title' => 'Portrait of adult male working from home at night',
                    'author' => 'Tawam',
                    'category' => 'Graphic',
                    'date' => '09 March 2025',
                    'image' => 'images/blogs/blog-1.png',
                    'delay' => '.5s'
                ],
                [
  
                    'title' => 'Portrait of adult male working from home at night',
                    'author' => 'Tawam',
                    'category' => 'Graphic',
                    'date' => '09 March 2025',
                    'image' => 'images/blogs/blog-2.png',
                    'delay' => '.6s'
                ],
                [
                    'title' => 'Portrait of adult male working from home at night',
                    'author' => 'Tawam',
                    'category' => 'Graphic',
                    'date' => '09 March 2025',
                    'image' => 'images/blogs/blog-1.png',
                    'delay' => '.7s'
                ],
            ];
        @endphp

        @foreach ($gridBlogs as $blog)
            <div class="col-lg-4 col-md-6">
                <div class="blog-item wow fadeInRight" data-wow-delay="{{ $blog['delay'] }}">
                    <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
                        <img src="{{ asset($blog['image']) }}" alt="thumbnail">
                    </a>
                    <div class="blog-meta d-inline-flex flex-wrap align-items-center gap-3 gap-xl-4">
                        <span class="d-inline">by <span class="sub-text">{{ $blog['author'] }}</span> | {{ $blog['category'] }}</span>
                        <span class="date d-inline-flex align-items-center">{{ $blog['date'] }}</span>
                    </div>
                    <h5><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">{{ $blog['title'] }}</a></h5>
                    <span class="read-more"><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Read More</a></span>
                </div>
            </div>
        @endforeach
       
    </div>

    <div class="row">
        <div class="col-lg-12">
        <div class="glenic-pagination">
            <ul class="wow fadeInUp" data-wow-delay=".3s">
            <li><a href="#"><i class="bi bi-arrow-left"></i></a></li>
            <li><a href="#">01</a></li>
            <li><span>02</span></li>
            <li><a href="#">03</a></li>
            <li><a href="#"><i class="bi bi-arrow-right"></i></a></li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</section>
<!-- END: Blog Page -->
@include('sections.contact')
