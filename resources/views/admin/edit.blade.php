@extends('admin.layout')
@section('title', $isItem ? ($record->exists ? 'Edit item' : 'Add item') : 'Edit '.ucfirst($section->type))
@section('content')
<a class="back" href="{{ $isItem ? route('admin.sections.edit',$section) : route('admin.index') }}">← {{ $isItem ? 'Back to '.ucfirst($section->type) : 'All sections' }}</a><div class="page-title"><div><p class="eyebrow">{{ $isItem ? ucfirst($section->type).' / ITEM' : 'SECTION EDITOR' }}</p><h1>{{ $isItem ? ($record->exists ? 'Edit item' : 'Add item') : ($section->type==='settings' ? 'Site settings' : ucfirst($section->type)) }}</h1><p>{{ $isItem ? 'Manage the details shown on your portfolio.' : 'Update this section’s content, order, and visibility.' }}</p></div></div>
<form class="panel edit-form" method="post" enctype="multipart/form-data" action="{{ $isItem ? ($record->exists ? route('admin.items.update',$record) : route('admin.items.store',$section)) : route('admin.sections.update',$section) }}">@csrf @if(!$isItem || $record->exists)@method('PUT')@endif
<div class="panel-heading"><h2>Content</h2><button class="button primary" type="submit">Save changes ↗</button></div><div class="form-body">
<label for="title">{{ $section->type==='settings' ? 'Settings name (internal)' : 'Title' }}</label><input id="title" name="title" value="{{ old('title',$record->title) }}" required maxlength="255">@error('title')<small class="field-error">{{ $message }}</small>@enderror
@if($section->type!=='settings')<label for="subtitle">Subtitle / short label</label><input id="subtitle" name="subtitle" value="{{ old('subtitle',$record->subtitle) }}" maxlength="255"><label for="body">Description</label><textarea id="body" name="body" rows="6" maxlength="20000">{{ old('body',$record->body) }}</textarea>@endif
@if(!$isItem && $section->type==='settings')@include('admin.theme')@endif
@if($isItem && $section->type === 'projects')
<fieldset class="project-visibility">
    <legend>Project section visibility</legend>
    @foreach(\App\Support\ProjectSections::LABELS as $part => $label)
        <label class="checkbox">
            <input type="hidden" name="data[visibility][{{ $part }}]" value="0">
            <input type="checkbox" name="data[visibility][{{ $part }}]" value="1" @checked(old('data.visibility.'.$part, $record->data['visibility'][$part] ?? true))>
            Show {{ strtolower($label) }}
        </label>
    @endforeach
</fieldset>
@endif
@php
    $projectCategory = strtolower(trim((string) old('data.category', $record->data['category'] ?? '')));
@endphp
@foreach($fields as $key=>$label)
@continue(array_key_exists($key, \App\Support\PortfolioTheme::colors()))
@if($key==='page_width')<div class="form-columns magazine-size-fields" data-magazine-fields @unless(in_array($projectCategory,['magazine','editorial'],true)) hidden @endunless>@endif
@if(in_array($key,['thumbnail','image','gallery','plan_image','cut_before','cut_after']))<section class="editor-media-card" aria-labelledby="media-heading-{{ $key }}"><div class="editor-media-heading"><span class="eyebrow">{{ $key==='gallery' ? 'PROJECT MEDIA' : 'IMAGE' }}</span><h3 id="media-heading-{{ $key }}">{{ $key==='gallery' ? 'Project gallery' : $label }}</h3><p>{{ $key==='gallery' ? 'Add, preview, reorder, and remove images displayed in the project gallery.' : ($key==='thumbnail' ? 'Optional image shown on the homepage project card. The cover image is used when this is empty.' : 'Upload an image, preview the current file, or remove it from this section.') }}</p></div><div class="editor-media-content">@endif
@unless(in_array($key,['page_width','page_height','gallery']))<label for="data_{{ $key }}">{{ $label }}</label>@endunless
@if($key==='gallery')@include('admin.galleries')
@elseif($key==='plan_rooms')<textarea id="data_{{ $key }}" name="data[{{ $key }}]" rows="7" spellcheck="false" data-plan-text>{{ old('data.'.$key,$record->data[$key] ?? '') }}</textarea><small>One room per line: <code>x, y, width, height | Name | Size | Note | optional image path</code>. The numbers are percentages of the plan image. Easiest way: draw the rooms on the plan below and the lines are written for you.</small>
<div class="plan-editor" data-plan-editor data-path-input="data_plan_image" data-upload-input="upload_plan_image"><p class="pe-empty" data-pe-empty>Add a floor plan image above (type its path or upload one), then draw the rooms here.</p><div class="pe-stage" data-pe-stage hidden><img data-pe-img alt="Floor plan being edited" draggable="false"></div><div class="pe-form" data-pe-form hidden><strong data-pe-title>New room</strong><label for="pe_name">Room name</label><input id="pe_name" data-pe-name maxlength="80" autocomplete="off"><label for="pe_size">Size (optional)</label><input id="pe_size" data-pe-size maxlength="80" placeholder="e.g. 6.4 × 3.0 m" autocomplete="off"><label for="pe_note">Note (optional)</label><textarea id="pe_note" data-pe-note rows="3" maxlength="600"></textarea><label for="pe_image">Room image path (optional)</label><input id="pe_image" data-pe-image placeholder="/media/uploads/room.jpg" autocomplete="off"><div class="pe-actions"><button type="button" class="button primary" data-pe-save>Save room</button><button type="button" class="button" data-pe-cancel>Cancel</button><button type="button" class="link danger" data-pe-delete>Delete room</button></div></div></div>
@elseif(\App\Support\PortfolioFields::media($key))
@php
    $mediaPath = old('data.'.$key, $record->data[$key] ?? '');
    $hasMediaPreview = is_string($mediaPath) && preg_match('~^/(?!/)[a-zA-Z0-9/_\-.]+$~', $mediaPath);
@endphp
@if(in_array($key,['thumbnail','image','plan_image','cut_before','cut_after']))
<img id="preview_{{ $key }}" class="media-preview" @if($hasMediaPreview) src="{{ $mediaPath }}" @else hidden @endif alt="{{ $label }} preview">
@elseif($hasMediaPreview)
<a id="preview_{{ $key }}" class="back" href="{{ $mediaPath }}" target="_blank" rel="noopener">Open current {{ $key==='model_url' ? '3D model' : 'PDF' }}</a>
@endif
<input id="data_{{ $key }}" name="data[{{ $key }}]" data-media-path data-preview-id="preview_{{ $key }}" value="{{ old('data.'.$key,$record->data[$key] ?? '') }}" placeholder="{{ $key==='model_url' ? '/media/uploads/model.glb' : '/images/portfolio/example.jpg' }}"><button type="button" class="clear-media-button" data-clear-media="{{ $key }}">Remove current {{ in_array($key,['thumbnail','image','plan_image','cut_before','cut_after']) ? 'image' : 'file' }}</button><small>Removing it clears this reference; uploaded files are not permanently deleted.</small><label for="upload_{{ $key }}">Upload replacement {{ in_array($key,['thumbnail','image','plan_image','cut_before','cut_after']) ? 'image' : ($key==='model_url' ? '3D model' : 'PDF') }}</label><input type="file" id="upload_{{ $key }}" name="upload_{{ $key }}" accept="{{ in_array($key,['thumbnail','image','plan_image','cut_before','cut_after']) ? 'image/jpeg,image/png,image/webp' : ($key==='model_url' ? '.glb,model/gltf-binary' : 'application/pdf') }}"><small>{{ in_array($key,['thumbnail','image','plan_image','cut_before','cut_after']) ? 'JPG, PNG, WebP. Maximum 10 MB.' : ($key==='model_url' ? 'Binary glTF (.glb), maximum 50 MB. The cover image is used as the model preview.' : 'PDF only. Maximum 30 MB.') }}</small>
@elseif(in_array($key,['page_width','page_height']))<label for="data_{{ $key }}">{{ $label }}</label><input id="data_{{ $key }}" name="data[{{ $key }}]" type="number" min="100" max="500" step="1" value="{{ old('data.'.$key,$record->data[$key] ?? '') }}"><small>Optional. Set both dimensions in millimetres to choose this magazine’s page size. Leave both blank to match its cover image.</small>
@else<input id="data_{{ $key }}" name="data[{{ $key }}]" value="{{ old('data.'.$key,$record->data[$key] ?? '') }}" @if($key==='category')data-project-category @endif @if($key==='email')type="email"@endif>@if(in_array($key,['url','cta_url','secondary_url']))<small>Use https://, a local path, or a section anchor such as #projects.</small>@endif
@endif
@error('data.'.$key)<small class="field-error">{{ $message }}</small>@enderror
@if($key==='page_height')</div>@endif
@if(in_array($key,['thumbnail','image','gallery','plan_image','cut_before','cut_after']))</div></section>@endif
@endforeach
<div class="form-columns"><div><label for="position">Display order</label><input id="position" type="number" name="position" min="0" max="10000" required value="{{ old('position',$record->position) }}"><small>Lower numbers appear first.</small></div><div><label class="checkbox"><input type="hidden" name="is_visible" value="0"><input type="checkbox" name="is_visible" value="1" @checked(old('is_visible',$record->is_visible))> Visible on website</label>@if($section->type==='settings')<small>Global settings are always used by the site.</small>@endif</div></div><div class="form-actions"><button class="button primary" type="submit">Save changes</button><a class="button" href="{{ $isItem ? route('admin.sections.edit',$section) : route('admin.index') }}">Cancel</a></div></div></form>
@if(!$isItem && in_array($section->type,['projects','services','experience','education','skills','contact']))<div class="panel"><div class="panel-heading"><h2>{{ $section->type==='contact' ? 'Social links' : 'Section items' }}</h2><a class="button primary" href="{{ route('admin.items.create',$section) }}">+ Add item</a></div>@forelse($section->items as $item)<div class="content-row"><span class="row-number">{{ $item->position }}</span><div><h3><a href="{{ route('admin.items.edit',$item) }}">{{ $item->title }}</a></h3><p>{{ $item->subtitle }}</p></div><span class="badge">{{ $item->is_visible ? 'Visible' : 'Hidden' }}</span><a class="button" href="{{ route('admin.items.edit',$item) }}">Edit</a><form method="post" action="{{ route('admin.items.destroy',$item) }}" data-confirm="Delete this item permanently?">@csrf @method('DELETE')<button class="link danger">Delete</button></form></div>@empty<div class="empty">No items yet. Add the first one above.</div>@endforelse</div>@endif
@if(array_key_exists('plan_rooms',$fields))<script src="{{ asset('js/plan-editor.js') }}" defer></script>@endif
@endsection
