@include('sections.breadcrumb', ['title' => 'Standard Blog', 'subtitle' => 'Standard Blog'])
<!-- START: Blog Page -->
<section class="blogs-section blog-list-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-30">

            @php
                $blogs = [
                    [
                        'title' => 'Fusce Tincidunt Commodo Quis Portitor.',
                        'author' => 'Tawam',
                        'category' => 'Graphic',
                        'date' => '09 March 2025',
                        'excerpt' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Perspiciatis omnis explicabo blanditiis cum. Quasi adipisci reprehenderit tempora ab sed fugit...',
                        'image' => 'images/blogs/blog-ds-1.png'
                    ],
                    [
                        'title' => 'Mauris Luctus Tellus Sagittis Viverra.',
                        'author' => 'Tawam',
                        'category' => 'Graphic',
                        'date' => '09 March 2025',
                        'excerpt' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Perspiciatis omnis explicabo blanditiis cum. Quasi adipisci reprehenderit tempora ab sed fugit...',
                        'image' => 'images/blogs/blog-ds-2.png'
                    ],
        
                ];
            @endphp

            @foreach ($blogs as $blog)
                <!-- Single -->
                <div class="blog-list-item mb-30">
                    <div class="wow fadeInUp mb-2" data-wow-delay=".3s">
                        <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
                            <img src="{{ asset($blog['image']) }}" alt="thumbnail">
                        </a>
                    </div>
                    <div class="mt-3 mt-md-0 wow fadeInRight" data-wow-delay=".4s">
                        <h5><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">{{ $blog['title'] }}</a></h5>
                        <div class="blog-meta d-inline-flex flex-wrap align-items-center gap-3 gap-xl-4">
                            <span class="d-inline">by <span class="sub-text">{{ $blog['author'] }}</span> | {{ $blog['category'] }}</span>
                            <span class="date d-inline-flex align-items-center">{{ $blog['date'] }}</span>
                        </div>
                        <p class="my-2 overflow-hidden">{{ $blog['excerpt'] }}</p>
                        <div><span class="read-more"><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Read More</a></span></div>
                    </div>
                </div>
            @endforeach


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
            </div><!--End Col-->
        </div>
    </div>
</section>
<!-- END: Blog Page -->

@include('sections.contact')