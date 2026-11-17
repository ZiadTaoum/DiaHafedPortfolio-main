<fieldset class="theme-editor" data-theme-editor>
    <legend>Website colors</legend>
    <p>Choose your palette below. The preview updates as you edit; click Save changes to apply it to the website.</p>
    <div class="theme-colors">
        @foreach(\App\Support\PortfolioTheme::colors() as $key => $color)
            @php($value = old('data.'.$key, \App\Support\PortfolioTheme::value($record->data ?? [], $key)))
            <div class="theme-color" data-theme-color data-variable="{{ $color['variable'] }}" data-default="{{ $color['default'] }}">
                <label for="{{ $key }}">{{ $color['label'] }}</label>
                <div class="color-inputs">
                    <input type="color" aria-label="{{ $color['label'] }} picker" value="{{ is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/D', $value) ? $value : $color['default'] }}" data-color-picker>
                    <input id="{{ $key }}" name="data[{{ $key }}]" value="{{ $value }}" pattern="#[0-9a-fA-F]{6}" maxlength="7" required aria-describedby="theme-help" data-color-hex>
                </div>
                @error('data.'.$key)<small class="field-error">{{ $message }}</small>@enderror
            </div>
        @endforeach
    </div>
    <small id="theme-help">Use the color picker or enter a six-digit hex color, such as #ef7043.</small>
    <div class="theme-preview" data-theme-preview aria-label="Website palette preview">
        <div class="theme-preview-banner"><span>YOUR PORTFOLIO</span><h3>Design that tells a story.</h3><span class="theme-preview-button">Explore my work ↗</span></div>
        <div class="theme-preview-body"><h3>Selected work</h3><p>Thoughtful identities and visual stories.</p><div class="theme-preview-about">A little about me.</div></div>
        <div class="theme-preview-dark"><h3>Experience & skills</h3><p>The details behind the work.</p></div>
    </div>
    <p class="theme-contrast" data-theme-contrast role="status"></p>
    <button type="button" class="button" data-theme-reset>Reset to original colors</button>
    <small>Reset updates this form. Save changes to publish the original palette.</small>
</fieldset>
