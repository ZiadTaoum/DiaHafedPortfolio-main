@extends('portfolio.layout')
@section('title', $project->title.' — '.($settings['name'] ?? 'Portfolio'))
@section('content')
@php
	$galleryPages = array_values(array_unique(array_filter(array_map('trim', preg_split('/\R/', (string) ($project->data['gallery'] ?? ''))))));
	$cover = trim((string) ($project->data['image'] ?? ''));
	$magazinePages = $cover !== ''
		? array_merge([$cover], array_values(array_filter($galleryPages, fn ($path) => $path !== $cover)))
		: $galleryPages;
	$isMagazine = strtolower($project->data['category'] ?? '') === 'editorial';
	$pageWidth = isset($project->data['page_width']) && is_numeric($project->data['page_width']) ? max(100, min(500, (int) $project->data['page_width'])) : 320;
	$planImage = trim((string) ($project->data['plan_image'] ?? ''));
	$planRooms = $planImage !== '' ? \App\Support\PlanRooms::parse($project->data['plan_rooms'] ?? '') : [];
	$cutBefore = trim((string) ($project->data['cut_before'] ?? ''));
	$cutAfter = trim((string) ($project->data['cut_after'] ?? ''));
	$cutBeforeLabel = trim((string) ($project->data['cut_before_label'] ?? '')) ?: 'Section';
	$cutAfterLabel = trim((string) ($project->data['cut_after_label'] ?? '')) ?: 'Render';
	$pageHeight = isset($project->data['page_height']) && is_numeric($project->data['page_height']) ? max(100, min(500, (int) $project->data['page_height'])) : 300;
@endphp
<section class="section project-detail">
	<a class="text-link" href="{{ route('home') }}#{{ $project->section->key }}">← Back to selected work</a>
	<div class="section-heading">
		<div><span class="eyebrow">{{ $project->data['category'] ?? '' }} / {{ $project->data['year'] ?? '' }}</span><h1>{{ $project->title }}</h1></div>
		<p>{{ $project->subtitle }}<br>{{ $project->data['location'] ?? '' }}</p>
	</div>
	<div class="project-description prose">{{ $project->body }}</div>
	@if(!empty($project->data['model_url']))
		<div class="maquette-detail">
			<div class="maquette-heading"><div><span class="eyebrow">Interactive model</span><h2>Explore the maquette</h2></div><p>Drag to orbit · Scroll to zoom</p></div>
			<model-viewer class="maquette-viewer" data-model-viewer src="{{ $project->data['model_url'] }}" @if($cover !== '')poster="{{ $cover }}"@endif alt="Interactive 3D maquette of {{ $project->title }}" camera-controls auto-rotate shadow-intensity="1" exposure="1" ar ar-modes="webxr scene-viewer quick-look" loading="lazy" reveal="interaction"><span class="model-hint" slot="progress-bar">Preparing the model…</span><button class="ar-button" slot="ar-button" type="button">View in your space ↗</button></model-viewer>
		</div>
	@endif

	@if($cutBefore !== '' && $cutAfter !== '')
		<div class="cut-compare" data-cut>
			<div class="tool-heading"><div><span class="eyebrow">Section cut</span><h2>Slice through the design</h2></div><p>Drag the line · or use ← → on the slider</p></div>
			<div class="cut-stage" data-cut-stage>
				<img class="cut-img cut-under" data-cut-under src="{{ $cutAfter }}" alt="{{ $project->title }} — {{ $cutAfterLabel }}" draggable="false">
				<img class="cut-img cut-over" src="{{ $cutBefore }}" alt="{{ $project->title }} — {{ $cutBeforeLabel }}" draggable="false">
				<span class="cut-label cut-label-left" aria-hidden="true">{{ $cutBeforeLabel }}</span><span class="cut-label cut-label-right" aria-hidden="true">{{ $cutAfterLabel }}</span>
				<span class="cut-line" aria-hidden="true"><span class="cut-handle">◂ ▸</span></span>
				<input class="cut-range" type="range" min="0" max="100" value="50" step="1" aria-label="Reveal {{ $cutAfterLabel }} over {{ $cutBeforeLabel }}">
			</div>
		</div>
	@endif

	@if($planImage !== '' && count($planRooms))
		<div class="plan-explorer" data-plan>
			<div class="tool-heading"><div><span class="eyebrow">Interactive plan</span><h2>Walk through the plan</h2></div><p>Tap or click a room · or press Play tour</p></div>
			<div class="plan-layout">
				<div class="plan-stage" data-plan-stage>
					<img src="{{ $planImage }}" alt="Floor plan of {{ $project->title }}" draggable="false">
					@foreach($planRooms as $i => $room)
						<button class="plan-room" type="button" data-room="{{ $i }}" aria-pressed="false" aria-label="{{ $room['name'] }}{{ $room['size'] !== '' ? ', '.$room['size'] : '' }}" style="--x:{{ $room['x'] }};--y:{{ $room['y'] }};--w:{{ $room['w'] }};--h:{{ $room['h'] }}" data-name="{{ $room['name'] }}" data-size="{{ $room['size'] }}" data-note="{{ $room['note'] }}" data-image="{{ $room['image'] }}"><span class="plan-room-tag">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span></button>
					@endforeach
				</div>
				<aside class="plan-panel" aria-live="polite">
					<div class="plan-empty" data-plan-empty><span class="eyebrow">{{ count($planRooms) }} spaces</span><p>Select a room on the plan to see its size, its story and a closer look.</p></div>
					<div class="plan-detail" data-plan-detail hidden>
						<span class="plan-number" data-plan-number></span>
						<h3 data-plan-name></h3>
						<span class="eyebrow" data-plan-size></span>
						<p data-plan-note></p>
						<img class="plan-photo" data-plan-photo alt="" hidden>
					</div>
					<div class="plan-controls"><button type="button" data-plan-prev aria-label="Previous room">←</button><button type="button" data-plan-play class="plan-play" aria-pressed="false">▶ Play tour</button><button type="button" data-plan-next aria-label="Next room">→</button></div>
					<ol class="plan-index">@foreach($planRooms as $i => $room)<li><button type="button" data-room-item="{{ $i }}"><span>{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>{{ $room['name'] }}</button></li>@endforeach</ol>
				</aside>
			</div>
		</div>
	@endif

	@if($isMagazine && count($magazinePages))
		<div class="offline-flipbook" data-offline-flipbook data-page-width="{{ $pageWidth }}" data-page-height="{{ $pageHeight }}" data-title="{{ $project->title }}" aria-label="{{ $project->title }} image flipbook">
			<div class="offline-flipbook-toolbar">
				<span class="eyebrow">{{ $project->title }}</span>
				<div class="offline-flipbook-controls">
					<span data-pageflip-count aria-live="polite">Page 1 of {{ count($magazinePages) }}</span>
					<button type="button" data-pageflip-prev aria-label="Previous page" disabled>←</button>
					<button type="button" data-pageflip-next aria-label="Next page">→</button>
				</div>
			</div>
			<div class="offline-flipbook-progress"><span data-pageflip-progress></span></div>
			<div class="offline-flipbook-stage">
				<div class="offline-flipbook-book" data-pageflip-book>
					@foreach($magazinePages as $path)
						<div class="offline-flipbook-page" @if($loop->first || $loop->last) data-density="hard" @endif>
							<img src="{{ $path }}" alt="{{ $loop->first && $cover !== '' ? ($project->data['image_alt'] ?? $project->title.' cover') : $project->title.' — page '.$loop->iteration }}" draggable="false">
							<span class="offline-page-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
						</div>
					@endforeach
				</div>
			</div>
			<p class="offline-flipbook-hint">Use the arrows, swipe, or click a page corner to turn.</p>
		</div>
		@vite('resources/js/flipbook.js')
	@else
		@if($cover !== '')<button class="detail-image-trigger detail-cover-trigger" type="button" data-gallery-open data-gallery-src="{{ $cover }}" data-gallery-alt="{{ $project->data['image_alt'] ?? $project->title }}" aria-label="Inspect {{ $project->title }} image"><img class="detail-cover" src="{{ $cover }}" alt="{{ $project->data['image_alt'] ?? $project->title }}"><span class="image-inspect-label">Inspect image ↗</span></button>@endif
		@if(count($galleryPages))<p class="gallery-hint">Click an image to inspect it · use arrows or swipe to browse</p>@endif
		<div class="gallery">@foreach($galleryPages as $path)<button class="detail-image-trigger" type="button" data-gallery-open data-gallery-src="{{ $path }}" data-gallery-alt="{{ $project->title }} — project artwork {{ $loop->iteration }}" aria-label="Inspect project artwork {{ $loop->iteration }}"><img src="{{ $path }}" alt="{{ $project->title }} — project artwork {{ $loop->iteration }}" loading="lazy"><span class="image-inspect-label">Inspect ↗</span></button>@endforeach</div>
	@endif
	<dialog class="project-lightbox" data-project-lightbox aria-label="Project image viewer">
		<button class="lightbox-close" type="button" data-lightbox-close aria-label="Close image viewer">×</button>
		<button class="lightbox-arrow lightbox-previous" type="button" data-lightbox-previous aria-label="Previous image">←</button>
		<figure class="lightbox-figure" data-lightbox-stage><img data-lightbox-image alt=""><figcaption><span data-lightbox-caption></span><span data-lightbox-count></span></figcaption></figure>
		<button class="lightbox-arrow lightbox-next" type="button" data-lightbox-next aria-label="Next image">→</button>
		<button class="lightbox-zoom" type="button" data-lightbox-zoom aria-pressed="false">Zoom in +</button>
	</dialog>
	<script src="{{ asset('js/project-gallery.js') }}" defer></script>
	@if(($cutBefore !== '' && $cutAfter !== '') || count($planRooms))<script src="{{ asset('js/project-tools.js') }}" defer></script>@endif
</section>
@endsection
