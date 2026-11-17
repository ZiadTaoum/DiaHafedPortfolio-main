<header class="header-section">
    <div class="container">
        <div class="header-wrapper">
            <!-- Logo -->
            <div class="main-logo">
                <a href="{{ url('/home-dark') }}" class="logo">
                    <h4>T<span class="txt-orange">e</span>xtio</h4>
                </a>
            </div>

            <!-- Navigation Menu -->
            <ul class="main-menu">
                <li>
                    <a class="{{ request()->is('/') ? 'current-menu' : '' }}" href="{{ url('/') }}">
                        Home
                    </a>
                    <ul class="sub-menu">
                        <li><a href="{{ url('/') }}">Home One</a></li>
                        <li><a href="{{ url('/home-dark') }}">Home Two</a></li>
                    </ul>
                </li>
                <li><a href="#about">About Me</a></li>
                <li><a href="#portfolio">Portfolio</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#testimonial">Testimonial</a></li>
                <li>
                    <a href="#blogs">Blog</a>
                    <ul class="sub-menu">
                        <li><a href="{{ url('/blog-dark') }}">Standard Blog</a></li>
                        <li><a href="{{ url('/blog-grid-dark') }}">Grid Blog</a></li>
                        <li><a href="{{ url('/blog-list-dark') }}">Blog List</a></li>
                        <li><a href="{{ url('/blog-right-sidebar-dark') }}">Blog Right Sidebar</a></li>
                        <li><a href="{{ url('/blog-details-dark') }}">Blog Details</a></li>
                    </ul>
                </li>
                <li><a href="#contact">Contact</a></li>
            </ul>

            <!-- Right Menu Components -->
            <div class="menu-components d-flex align-items-center">
                <a href="#contact" class="d-none d-xl-flex lets-talk align-items-center">
                    <span class="get-text">Let's Talk&nbsp;</span>
                    <span><i class="bi bi-arrow-up-right"></i></span>
                </a>
                <div class="header-bar d-lg-none">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="remove-click">
                    <div class="hamburger-menu"></div>
                </div>
            </div>
        </div>
    </div>
</header>
