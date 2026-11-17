<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="HTML, CSS, JavaScript, jQuery, bootstrap5">
    <meta name="author" content="creativemela">
    <meta name="description"
        content="Discover Textio, a versatile personal portfolio HTML template by Creativemela. Ideal for showcasing your work, Tawam offers sleek design, multiple layouts, and comprehensive UI/UX services. Perfect for designers, developers, and creatives.">

    <!-- Title -->
    <title>@yield('title', 'Textio - Personal Portfolio HTML Template')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backToTop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    <style>
        .slider .slides {
            position: absolute;
            top: 50%;
            transform: translateY(-25%);
            width: 196px;
            height: 274px;
            background: var(--img) no-repeat;
            background-size: contain;
            transition: 0.5s;
            display: flex;
            justify-content: flex-start;
            align-items: flex-end;
        }
    </style>
</head>

<body class="{{ request()->is('*-dark*') ? 'dark' : '' }}">
    <!-- start: Preloader -->
    <div id="preloader">
        <div id="container" class="container-preloader">
            <div class="animation-preloader">
                <div class="main-logo">
                    <div class="logo">
                        <h2>T<span class="txt-orange">e</span>xtio</h2>
                    </div>
                </div>
            </div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
    </div>
    <!-- end: Preloader -->

    <!-- start: Back To Top -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
        </svg>
    </div>
    <!-- end: Back To Top -->
     
    {{-- Header --}}
    @if(request()->is('*-dark*'))
        @include('layouts.header2')  {{-- loads header-2.blade.php --}}
    @else
        @include('layouts.header1')   {{-- loads header.blade.php --}}
    @endif

    <!-- Main Area Start -->
    <main id="home">
        
    @yield('content')

    </main>
    <!-- Main Area End -->

       <!-- START: FOOTER SECTION -->
    <footer>
        <div class="container">
            <div class="footer-content d-flex justify-content-between align-items-center">
                <h4 class="d-none d-md-block">T<span class="txt-orange">e</span>xtio</h4>
                <p class="copy-right mx-auto mx-md-0">
                    &copy; 2026
                    <a href="#" target="_blank">Textio</a>
                    - All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    <!-- END: FOOTER SECTION -->


    <!-- START: Sub Side Bar -->
    <div class="subside-barmenu sub-contact">
        <div class="remove-click d-flex justify-content-center align-items-center">
            <i class="bi bi-x-lg"></i>
        </div>
        <div class="sub-contact-wrapper d-grid">
            <!-- <a href="index-2.html" class="side-logo">
                    <img src="assets/img/logo/logo.png" alt="img">
                </a> -->
            <a href="index.html" class="logo">
                <h4>T<span class="txt-orange">e</span>xtio</h4>
            </a>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Nihil, aliquid dolorum optio repellat rerum
                commodi accusantium rem praesentium possimus dignissimos.
            </p>
            <div class="sub-contact-left d-grid">
                <div class="sub-contac-item">
                    <div class="address-widget">
                        <span class="address d-block">
                            address
                        </span>
                        <span class="textp">
                            Victoria Street, London
                        </span>
                    </div>
                </div>
                <div class="sub-contac-item">
                    <div class="address-widget">
                        <span class="address d-block">
                            email
                        </span>
                        <a href="javascript:void(0)" class="textp">
                            Textio@email.com
                        </a>
                    </div>
                </div>
                <div class="sub-contac-item">
                    <div class="address-widget">
                        <span class="address d-block">
                            call now
                        </span>
                        <a href="jasacript:void(0)" class="textp">
                            +89 4758 2154 021
                        </a>
                    </div>
                </div>
            </div>
            <div class="sub-contac-right mb-60 position-relative">
                <ul class="social d-flex gap-3">
                    <li>
                        <a href="javascript:void(0)">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <a href="#contact" class="d-flex justify-content-center lets-talk-btn align-items-center gap-2">
                <span class="get-text">
                    Let's Talk
                </span>
                <span>
                    <i class="bi bi-arrow-up-right"></i>
                </span>
            </a>
        </div>
    </div>
    <!-- END: Sub Side Bar -->


    <!-- start: Popup -->
    <div id="popup" class="popup_content_area zoom-anim-dialog mfp-hide project-popup">
        <div class="popup_modal_content">
            <div class="project-info-wrapper">
                <div class="thumbnail-wrapper">
                    <img src="{{asset('images/popup/popup-project-thumb.png')}}" alt="thumbnail">
                </div>
                <div class="project-client-meta d-flex flex-wrap justify-content-between">
                    <div class="client-meta">
                        <p>Clients:</p>
                        <h6>MD Towkib Tanvir</h6>
                    </div>
                    <div class="client-meta">
                        <p>Category:</p>
                        <h6>Business Consulting</h6>
                    </div>
                    <div class="client-meta">
                        <p>Timeframe:</p>
                        <h6>August 05, 2025</h6>
                    </div>
                </div>
                <hr class="hr w-100">
                <div class="project-details">
                    <h3>Dashboard Design</h3>
                    <p>
                        Maecenas sed tortor molestie, sagittis nibh
                        sit amet, dapibus felis. Vivamus sed neque
                        iaculis, ultrices nulla eu, venenatis dui.
                        Praesent luctus urna eu dapibus pulvinar.
                        Curabitur sed magna accumsan neque fermentum
                        laoreet. Ut nunc.
                    </p>
                    <ul>
                        <li>Will you provide website layout about design</li>
                        <li>Branding With Business Agency</li>
                        <li>Why should I work with an agency over a freelancer</li>
                        <li>How much do you charge for branding</li>
                    </ul>
                    <div class="other-project-img row">
                        <div class="col-md-6">
                            <div>
                                <img src="{{asset('images/popup/popup-img-1.png')}}" alt="thumbnail">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <img src="{{asset('images/popup/popup-img-2.png')}}" alt="thumbnail">
                            </div>
                        </div>
                    </div>
                    <hr class="hr w-100">
                    <div class="footer-modal d-flex justify-content-between align-items-center">
                        <button type="button">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <span><i class="bi bi-microsoft"></i></span>
                        <button type="button">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end: Popup -->

    <!-- **** All JS Files ***** -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/backToTop.js') }}"></script>
    <script src="{{ asset('js/smooth-scroll.js') }}"></script>
    <script src="{{ asset('js/wow.min.js') }}"></script>
    <script src="{{ asset('js/one-page-nav.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('js/magnific-popup.js') }}"></script>
    <script src="{{ asset('js/validate.min.js') }}"></script>
    <script src="{{ asset('js/imagesloaded.pkgd.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/active.js') }}"></script>
    <script src="{{ asset('js/contact-form.js') }}"></script>
</body>

</html>