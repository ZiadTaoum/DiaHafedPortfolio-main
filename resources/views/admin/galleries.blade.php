@php
    $galleries = old('edit_galleries')
        ? old('galleries', [])
        : \App\Support\ProjectSections::galleries($record->data ?? []);
@endphp
<div data-galleries="{{ json_encode($galleries) }}">
    <div data-gallery-list></div>
    <button type="button" class="button" data-add-gallery>+ Add gallery</button>
    <noscript><p>Enable JavaScript to edit project galleries.</p></noscript>
    <template data-gallery-template>
        <fieldset class="gallery-manager">
            <legend>Gallery</legend>
            <label>Gallery title <input type="text" maxlength="255" data-gallery-title></label>
            <label class="checkbox"><input type="checkbox" data-gallery-visible> Show this gallery</label>
            <div class="gallery-image-list" data-gallery-image-list></div>
            <label>Add gallery images <input type="file" data-gallery-uploads multiple accept="image/jpeg,image/png,image/webp"></label>
            <small>JPG, PNG, or WebP, up to 10 MB each. Use the arrows to change image order.</small>
            <details class="gallery-advanced">
                <summary>Advanced: edit image paths</summary>
                <label>One local image path per line <textarea rows="4" data-gallery-paths></textarea></label>
                <button type="button" class="button" data-apply-paths>Apply image paths</button>
            </details>
            <button type="button" class="clear-media-button" data-remove-gallery>Remove gallery</button>
        </fieldset>
    </template>
</div>
<script src="{{ asset('js/admin-galleries.js') }}" defer></script>
