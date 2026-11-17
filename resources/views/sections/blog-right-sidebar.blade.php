@include('sections.breadcrumb', ['title' => 'Blog Right Sidebar', 'subtitle' => 'Blog Right Sidebar'])
<!-- START: Blog Page -->
<section class="blogs-section blog-page-sidebar">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-30">
                <div class="row">
                   <div class="row">
                        @php
                            $blogs = [
                                ['img' => 'images/blogs/blog-1.png', 'title' => 'Portrait of adult male working from home at night'],
                                ['img' => 'images/blogs/blog-2.png', 'title' => 'Portrait of adult male working from home at night'],
                                ['img' => 'images/blogs/blog-1.png', 'title' => 'Portrait of adult male working from home at night'],
                                ['img' => 'images/blogs/blog-2.png', 'title' => 'Portrait of adult male working from home at night'],
                                ['img' => 'images/blogs/blog-1.png', 'title' => 'Portrait of adult male working from home at night'],
                                ['img' => 'images/blogs/blog-2.png', 'title' => 'Portrait of adult male working from home at night'],
                            ];
                        @endphp

                        @foreach ($blogs as $key => $blog)
                            <div class="col-lg-6 col-md-6 mb-30">
                                <div class="blog-item wow fadeInRight" data-wow-delay=".{{ $key + 3 }}s">
                                    <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
                                        <img src="{{ asset($blog['img']) }}" alt="thumbnail">
                                    </a>
                                    <div class="blog-meta d-inline-flex flex-wrap align-items-center gap-3 gap-xl-4">
                                        <span class="d-inline">by <span class="sub-text">Tawam</span> | Graphic</span>
                                        <span class="date d-inline-flex align-items-center">09 March 2024</span>
                                    </div>
                                    <h5>
                                        <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">{{ $blog['title'] }}</a>
                                    </h5>
                                    <span class="read-more">
                                        <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Read More</a>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>


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
            
            <div class="col-lg-4">
                 @include('sections.blog-sidebar')
            </div>
        </div>
    </div>
</section>
<!-- END: Blog Page -->

@include('sections.contact')