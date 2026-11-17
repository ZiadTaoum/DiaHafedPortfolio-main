@extends('layouts.app')
@section('title', '404 - Pertest')
@section('header')
    @include('layouts.header1')
@endsection
@section('content')

@include('sections.breadcrumb', ['title' => '404', 'subtitle' => '404'])

<!-- Start Page Not Found -->
<section class="page_not_found">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 wow fadeIn text-center mx-auto">
                <div class="pnf_content overflow-hidden  pb-5 mb-5">
                    <h3 class="mb-3">Oops! Page Not Found</h3>
                    <p class="mb-5">We are sorry, but the page you are looking for does not exist. It might have been removed, had its name changed, or is temporarily unavailable.</p>
                    <a href="{{ url('/') }}" class="bttn bttn-primary">
                        Back to Home
                    </a>
                </div>
            </div> <!-- End Col -->

        </div>
    </div>
</section>
<!-- End Page Not Found -->

@endsection  