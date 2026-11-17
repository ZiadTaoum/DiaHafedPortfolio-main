@include('sections.breadcrumb', ['title' => 'Blog Details', 'subtitle' => 'Blog Details'])
<!-- START: Blog Details Section -->
<section class="blog-details-section pt-120 pb-90">
    <div class="container">
        <div class="row">
			<!-- Blog Details -->
			<div class="col-lg-8 mb-30">
				<div class="blog-details-full">
					<!-- Thumbnail -->
					<div class="thumbnail wow fadeInUp" data-wow-delay=".3s">
						<img src="{{asset('images/blog-details/thumbnail.png')}}" alt="Blog Details Image">
					</div>
					<!-- Auth Section -->
					<div class="b-auth-section mt-30 mb-20">
						<div class="row">
							<div class="col-md-6 order-md-first order-last">
								<div class="left-a wow fadeInUp" data-wow-delay=".5s">
									<span class="img">
										<img src="{{asset('images/blog-details/auth-img.png')}}" alt="auth">
									</span>
									<span>by&nbsp;<a href="#" class="sub-text">Creativemela</a></span>
									<span><img src="{{asset('images/blogs/calendar.svg')}}" alt="calender">&nbsp;&nbsp;22 Aug 2025</span>
								</div>
							</div>
							<div class="col-md-6 align-self-center text-md-end">
								<div class="blog-share mb-3 mb-md-0 wow fadeInUp" data-wow-delay=".4s">
									<span><a href="#"><i class="fa-solid fa-link"></i></a></span>
									<span><a href="#"><i class="fa-brands fa-linkedin-in"></i></a></span>
									<span><a href="#"><i class="fa-brands fa-x-twitter"></i></a></span>
									<span><a href="#"><i class="fa-brands fa-facebook-f"></i></a></span>
								</div>
							</div>
						</div>
					</div>
					<!-- Content -->
					<div class="content">
						<h4 class="wow fadeInUp" data-wow-delay=".6s">Migration Pellentesque consequat in augue sedus sodales
							Top Standard size of business.
						</h4>
						<p class="wow fadeInUp" data-wow-delay=".7s">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut
							venenatis sagittis tortor, in
							convallis justo sagittis et. Nunc placerat risus sem, vitae dictum libero laoreet sit
							amet. Suspendisse semper leo nec justo aliquam venenatis. Aliquam vitae lorem sed orci
							vulputate porta. Vestibulum ut elit eget arcu tincidunt sollicitudin quis sed augue.
							Aenean nibh nunc, elementum a dolor vel, vestibulum commodo dui. Donec id felis a ipsum
							molestie tincidunt. Nunc ullamcorper quam nunc, eget laoreet dui pharetra eget. Aenean
							viverra interdum condimentum. Cras mollis, arcu vel fermentum auctor.</p>
						<p class="wow fadeInUp" data-wow-delay=".8s">Phasellus ac consequat turpis, sit amet fermentum nulla.
							Donec dignissim augue nunc.
							Praesent bibendum erat ac lectus molestie lobortis. Curabitur ultrices justo ac leo
							facilisis tincidunt.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut
							venenatis sagittis tortor, in convallis justo sagittis et. Nunc placerat risus sem,
							vitae dictum libero laoreet sit amet. Suspendisse semper.</p>
						<p class="quote wow fadeInUp" data-wow-delay=".9s">
							<img src="{{asset('images/blog-details/quote2.png')}}" alt="quote">
							Phasellus ac consequat turpis, sit amet fermentum nulla. Donec dignissim augue nunc.
							Praesent bibendum erat ac lectus molestie lobortis. Curabitur ultrices justo ac leo
							facilisis tincidunt.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut
							venenatis sagittis tortor, in convallis justo sagittis et. Nunc placerat risus sem,
							vitae dictum libero laoreet sit amet. Suspendisse semper.
						</p>
						<p class="wow fadeInUp" data-wow-delay="1s">Sed nec sapien eu nibh porta fringilla. Aenean in lectus id
							tellus tempus rutrum vitae a
							elit. Nulla sit amet interdum ligula. Duis bibendum porttitor tempus. Morbi nisi nisl,
							sagittis in enim at, tempus convallis magna. Nam malesuada risus non congue viverra.
							Nullam ultrices massa orci, in eleifend diam fringilla.Lorem ipsum dolor sit amet,
							consectetur adipiscing elit. Ut venenatis sagittis tortor, in convallis justo sagittis
							et. Nunc placerat risus sem, vitae dictum libero laoreet sit amet. Suspendisse semper
							leo nec justo aliquam venenatis. </p>
						<div class="blog-tags mt-20 pt-30 mb-40 d-flex flex-wrap gap-3 wow fadeInUp" data-wow-delay="1.1s">
							<span>Tag Here</span>
							<a href="#">Creative</a>
							<a href="#">Agency</a>
							<a href="#">Business</a>
						</div>
					</div>
					<!-- Author Content -->
					<div class="blog-author-section mt-20 mb-60 wow fadeInUp" data-wow-delay=".3s">
						<div class="thumb">
							<img src="{{asset('images/blog-details/b-auth.png')}}" alt="img">
						</div>
						<div class="content-auth">
							<div class="auth-header">
								<h4><a href="#">Towkibur Rahman</a></h4>
								<div class="a-social-icon">
									<span><a href="#"><i class="fa-brands fa-facebook-f"></i></a></span>
									<span><a href="#"><i class="fa-brands fa-instagram"></i></a></span>
									<span><a href="#"><i class="fa-brands fa-dribbble"></i></a></span>
									<span><a href="#"><i class="fa-brands fa-behance"></i></a></span>
								</div>
							</div>
							<p>Fusce condimentum enim vestibulum libero gravida, ut accumsan quam bibendum.
								Curabitur gravida faucibus est sit amet cursus. Duis accumsan vel arcu pretium
								viverra.</p>
						</div>
					</div>
					<!-- Next Prev -->
					<div class="singlepage-navigation border-0">
						<div class="row">
							<div class="col-sm-6">
								<a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}" class="wow fadeInUp" data-wow-delay=".4s">
									<div class="singlepage-navi-icon">
										<i class="fa-solid fa-arrow-left-long"></i>
									</div>
									<div class="singlepage-navi-text">
										<span>Prev Services</span>
										<h5>Child Emergency</h5>
									</div>
								</a>
							</div>
							<div class="col-sm-6">
								<a class="float-right wow fadeInUp" data-wow-delay=".5s" href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
									<div class="singlepage-navi-text">
										<span>Next Portfolio</span>
										<h5>Surgery Center</h5>
									</div>
									<div class="singlepage-navi-icon">
										<i class="fa-solid fa-arrow-right-long"></i>
									</div>
								</a>
							</div>
						</div>
					</div>
					<!-- Related Post Section -->
					<div class="related-blog-section blogs-section mt-120 mb-90">
						<div class="row">
							<div class="col-lg-12 mb-50 text-center">
								<div class="related-blog-title">
									<h5 class="wow fadeInUp" data-wow-delay=".3s">Related Blogs</h5>
									<h3 class="wow fadeInUp" data-wow-delay=".4s">Related Blogs</h3>
								</div>
							</div>
						</div>
						<div class="row">
							<!-- Single -->
							<div class="col-lg-6 col-md-6 mb-30">
								<div class="blog-item wow fadeInRight" data-wow-delay=".5s">
									<a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
										<img src="{{ asset('images/blogs/blog-1.png') }}" alt="thumbnail">
									</a>
									<div class="blog-meta d-inline-flex flex-wrap align-items-center gap-3 gap-xl-4">
										<span class="d-inline">by <span class="sub-text">Tawam</span> | Graphic</span>
										<span class="date d-inline-flex align-items-center">09 March 2025</span>
									</div>
									<h5>
										<a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
											Fusce Tincidunt Commodo Sapien, Quis Porttitor Ipsum Fringilla Et.
										</a>
									</h5>
									<span class="read-more"><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Read More</a></span>
								</div>
							</div>

							<!-- Single -->
							<div class="col-lg-6 col-md-6 mb-30">
								<div class="blog-item wow fadeInRight" data-wow-delay=".6s">
									<a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
										<img src="{{ asset('images/blogs/blog-2.png') }}" alt="thumbnail">
									</a>
									<div class="blog-meta d-inline-flex flex-wrap align-items-center gap-3 gap-xl-4">
										<span class="d-inline">by <span class="sub-text">Tawam</span> | Graphic</span>
										<span class="date d-inline-flex align-items-center">09 March 2025</span>
									</div>
									<h5>
										<a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">
											Fusce Tincidunt Commodo Sapien, Quis Porttitor Ipsum Fringilla Et.
										</a>
									</h5>
									<span class="read-more"><a href="{{ request()->is('*-dark*') ? url('blog-details-dark') : url('blog-details') }}">Read More</a></span>
								</div>
							</div>

							
						</div>
					</div>
					
					<!-- Comment Section Full -->
					<div class="comment-section-full">
						<div class="cooment-s-header mb-30">
							<h5 class="title">02 Comments</h5>
							<p class="p-content">Nunc velit metus, volutpat elementum euismod eget, cursus nec nunc.
							</p>
						</div>
						<!-- Comment List -->
						<ul>
							<!-- Single -->
							<li class="single-comment">
								<div class="thum">
									<img src="{{ asset('images/blog-details/c-user1.png') }}" alt="thumb">
								</div>
								<div class="content">
									<h4>Monsur Rahman Lito</h4>
									<p>Phasellus ac consequat turpis, sit amet fermentum nulla. Donec dignissi augue
										nunc. Praesent bibendum erat ac lectus molestie lobortis. Curabi ultrices
										justo ac leo facilisis tincidunt Maecenas et dui.</p>
								</div>
								<!-- Comment Reply -->
								<ul class="comment-reply">
									<li class="single-comment">
										<div class="thum">
											<img src="{{ asset('images/blog-details/c-user2.png') }}" alt="thumb">
										</div>
										<div class="content">
											<h4>Monsur Rahman Lito</h4>
											<p>Phasellus ac consequat turpis, sit amet fermentum nulla. Donec
												dignissi augue nunc. Praesent bibendum erat ac lectus molestie
												lobortis. Curabi ultrices justo ac leo facilisis tincidunt Maecenas
												et dui.</p>
										</div>
									</li>
								</ul>
							</li>

						</ul>
						
						<div class="cooment-s-header mt-40 mb-30">
							<h5 class="title">Post Comments</h5>
							<p class="p-content">Nunc velit metus, volutpat elementum euismod eget, cursus nec nunc.
							</p>
						</div>
						<!-- Comment Form -->
						<div class="comment-form-full">
							<form id="blog-form">
								<div class="user-details row">
									<div class="input-box col-md-6">
										<label class="details" for="f-name">First Name</label>
										<input type="text" id="f-name" class="text" name="f_name" placeholder="" autocomplete="off"
											required>
									</div>
									<div class="input-box col-md-6">
										<label class="details" for="l-name">Last Name</label>
										<input type="text" id="l-name" class="text" name="l_name" placeholder="" autocomplete="off"
											required>
									</div>
									<div class="input-box col-md-6">
										<label class="details" for="mail-addr">Email</label>
										<input type="email" id="mail-addr" class="text" name="mail-addr" placeholder=""
											autocomplete="off" required>
									</div>
									<div class="input-box col-md-6">
										<label class="details" for="phoneNo">Phone Number</label>
										<input type="tel" id="phoneNo" class="text" name="phoneNo" placeholder="" autocomplete="off"
											required>
									</div>
								</div>
								<div class="user-message">
									<label class="details" for="comment">Comment</label>
									<textarea id="comment" name="comment" cols="1" rows="1" placeholder="Write your comment..."
										autocomplete="off" required></textarea>
								</div>
								<button class="bttn bttn-theme" type="submit">POST COMMENTS</button>
							</form>
						</div>
					</div>
					<!-- End Cooment Section -->
				</div>
			</div><!-- End Col-- >

			 <!-- Sidebar Widgets -->
			 <div class="col-lg-4 mb-30">
				@include('sections.blog-sidebar')
			</div>
		</div>
	</div>
</section>
@include('sections.contact')
