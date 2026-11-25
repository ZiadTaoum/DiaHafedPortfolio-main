<?php
namespace App\Http\Requests;
use App\Support\PortfolioFields;
use App\Support\ProjectSections;
use Illuminate\Foundation\Http\FormRequest;
class ContentRequest extends FormRequest {
    public function authorize(): bool { return (bool) $this->user()?->is_admin; }
    public function rules(): array {
        $section = $this->route('section') ?? $this->route('item')?->section;
        $isItem = $this->route('item') || $this->routeIs('admin.items.store');
        $fields = $isItem ? PortfolioFields::item($section->type) : PortfolioFields::section($section->type);
        $dataKeys = array_keys($fields);
        if (isset($fields['gallery'])) $dataKeys[] = 'visibility';
        $rules = ['title'=>'required|string|max:255', 'subtitle'=>'nullable|string|max:255', 'body'=>'nullable|string|max:20000', 'position'=>'required|integer|min:0|max:10000', 'is_visible'=>'nullable|boolean', 'data'=>['nullable','array:'.implode(',', $dataKeys)]];
        foreach ($fields as $key=>$label) {
            $rules['data.'.$key] = ['nullable','string','max:10000'];
            if (in_array($key, ['page_width','page_height'])) $rules['data.'.$key] = ['nullable','integer','min:100','max:500'];
            if (array_key_exists($key, \App\Support\PortfolioTheme::colors())) $rules['data.'.$key] = ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/D'];
            if (in_array($key, ['url','cta_url','secondary_url'])) $rules['data.'.$key][] = 'regex:~^(https?://[^\s]+|/[a-zA-Z0-9/_\-.#?=&%]*|\#[a-zA-Z][\w-]*)$~';
            if ($key === 'email') $rules['data.'.$key][] = 'email';
            if (PortfolioFields::media($key)) {
                $rules['data.'.$key][] = 'regex:~^/(?!/)[a-zA-Z0-9/_\-.]+$~';
                $rules['upload_'.$key] = match ($key) {
                    'thumbnail', 'image', 'plan_image', 'cut_before', 'cut_after' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
                    'model_url' => 'nullable|file|extensions:glb|max:51200',
                    default => 'nullable|file|mimes:pdf|max:30720',
                };
            }
            if ($key === 'gallery') {
                $rules += [
                    'gallery_uploads.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
                    'edit_galleries' => 'sometimes|accepted',
                    'galleries' => 'array',
                    'galleries.*' => 'array:title,visible,images',
                    'galleries.*.title' => 'nullable|string|max:255',
                    'galleries.*.visible' => 'required|boolean',
                    'galleries.*.images' => 'array',
                    'galleries.*.images.*' => 'array:path,upload',
                    'galleries.*.images.*.path' => ['nullable', 'string', 'max:2048', 'regex:~^/(?!/)[a-zA-Z0-9/_\-.]+$~'],
                    'galleries.*.images.*.upload' => 'required_without:galleries.*.images.*.path|nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
                    'data.visibility' => 'array:'.implode(',', array_keys(ProjectSections::LABELS)),
                    'data.visibility.*' => 'boolean',
                ];
            }
        }
        return $rules;
    }
    public function withValidator($validator): void {
        $validator->after(function ($validator) {
            $category = strtolower(trim((string) $this->input('data.category')));
            if (in_array($category, ['magazine', 'editorial'], true)
                && filled($this->input('data.page_width')) !== filled($this->input('data.page_height'))) {
                $validator->errors()->add('data.page_width','Enter both page dimensions, or leave both blank to match the cover image.');
                $validator->errors()->add('data.page_height','Enter both page dimensions, or leave both blank to match the cover image.');
            }
            foreach (\App\Support\PlanRooms::errors(is_string($this->input('data.plan_rooms')) ? $this->input('data.plan_rooms') : '') as $message) {
                $validator->errors()->add('data.plan_rooms', $message);
            }
            foreach (preg_split('/\R/', is_string($this->input('data.gallery')) ? $this->input('data.gallery') : '') as $path) {
                if (trim($path) && !preg_match('~^/(?!/)[a-zA-Z0-9/_\-.]+$~', trim($path))) $validator->errors()->add('data.gallery','Use local image paths, one per line.');
            }
        });
    }
}
