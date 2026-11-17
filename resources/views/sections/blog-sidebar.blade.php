<!-- Sidebar Widgets -->
<div class="sidebar-widgets">
        <!-- Sidebar Single Widget -->
        <div class="sidebar-single-widget mb-30">
            <h5 class="pb-20 wow fadeInUp" data-wow-delay=".3s">Search Here</h5>
            <div class="widget-search wow fadeInUp" data-wow-delay=".4s">
                <input type="text" name="search" placeholder="Enter Your Keyword">
                <button><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>
        <!-- Sidebar Single Widget -->

        <div class="sidebar-single-widget category-widget mb-30">
            <h5 class="pb-10 wow fadeInUp" data-wow-delay=".3s">Service List</h5>
            <ul class="wow fadeInUp" data-wow-delay=".4s">
                <li><a href="#"><i class="fa-solid fa-caret-right"></i> UX strategy</a></li>
                <li><a href="#"><i class="fa-solid fa-caret-right"></i> Information architecture</a></li>
                <li><a href="#"><i class="fa-solid fa-caret-right"></i> Prototyping</a></li>
                <li><a href="#"><i class="fa-solid fa-caret-right"></i> Wireframing</a></li>
                <li><a href="#"><i class="fa-solid fa-caret-right"></i> UI Design</a></li>
                <li><a href="#"><i class="fa-solid fa-caret-right"></i> Figma Design</a></li>
            </ul>
        </div>

        <!-- Sidebar Single Widget -->
        <div class="sidebar-single-widget socialicon-widget mb-30">
            <h5 class="pb-20 wow fadeInUp" data-wow-delay=".3s">Social Icons</h5>
            <ul class="d-lg-flex d-xxl-block flex-lg-wrap gap-lg-3 wow fadeInUp" data-wow-delay=".4s">
                <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-dribbble"></i></a></li>
            </ul>
        </div>

        <!-- Sidebar Single Widget -->
        <div class="sidebar-single-widget socialicon-widget">
            <h5 class="pb-20 wow fadeInUp" data-wow-delay=".3s">Newest Posts</h5>
            <div class="sidebar-blog-widgets">

                <!-- Single -->
                <div class="single-item wow fadeInUp" data-wow-delay=".4s">
                    <div class="thumb">
                        <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
                            <img src="{{ asset('images/service/service-1.png') }}" alt="blog">
                        </a>
                    </div>
                    <div class="content">
                        <span>by <a href="#">Creativemela</a></span>
                        <h4><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Fusce tincidunt commodo sapien, quis portitor.</a></h4>
                    </div>
                </div>
                <!-- Single -->

                <div class="single-item wow fadeInUp" data-wow-delay=".5s">
                    <div class="thumb">
                        <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
                            <img src="{{ asset('images/service/service-2.png') }}" alt="blog">
                        </a>
                    </div>
                    <div class="content">
                        <span>by <a href="#">Creativemela</a></span>
                        <h4><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Duis eu orci porta, lacinia tortor auctor vulputate.</a></h4>
                    </div>
                </div>
                <!-- Single -->

                <div class="single-item wow fadeInUp" data-wow-delay=".6s">
                    <div class="thumb">
                        <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
                            <img src="{{ asset('images/service/service-3.png') }}" alt="blog">
                        </a>
                    </div>
                    <div class="content">
                        <span>by <a href="#">Creativemela</a></span>
                        <h4><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Suspendisse justo justo, elementum id egestas.</a></h4>
                    </div>
                </div>
                <!-- Single -->

                <div class="single-item wow fadeInUp" data-wow-delay=".7s">
                    <div class="thumb">
                        <a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
                            <img src="{{ asset('images/service/service-1.png') }}" alt="blog">
                        </a>
                    </div>
                    <div class="content">
                        <span>by <a href="#">Creativemela</a></span>
                        <h4><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Mauris luctus, tellus sit amet sagittis viverra.</a></h4>
                    </div>
                </div>

            </div>
        </div>
</div>