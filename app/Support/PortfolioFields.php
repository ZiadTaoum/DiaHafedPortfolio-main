<?php
namespace App\Support;
class PortfolioFields {
    public static function section(string $type): array {
        return ($type === 'settings' ? array_map(fn ($color) => $color['label'], PortfolioTheme::colors()) : []) + ['nav_label'=>'Navigation label'] + match ($type) {
            'settings' => ['name'=>'Name / site brand', 'role'=>'Professional title', 'description'=>'SEO description', 'footer'=>'Footer text', 'cv_label'=>'CV download label', 'portfolio_label'=>'Portfolio download label', 'cv'=>'CV PDF', 'portfolio'=>'Portfolio PDF'],
            'hero' => ['eyebrow'=>'Small heading', 'image'=>'Banner image', 'image_alt'=>'Image description', 'cta_label'=>'Primary button label', 'cta_url'=>'Primary button link', 'secondary_label'=>'Secondary button label', 'secondary_url'=>'Secondary button link'],
            'about' => ['image'=>'About image', 'image_alt'=>'Image description'],
            'contact' => ['email'=>'Email address', 'phone'=>'Phone', 'location'=>'Location', 'form_heading'=>'Form heading', 'submit_label'=>'Submit button label', 'success_message'=>'Success message'],
            default => [],
        };
    }
    public static function item(string $type): array {
        return match ($type) {
            'projects' => ['category'=>'Category', 'location'=>'Location', 'year'=>'Year / period', 'thumbnail'=>'Homepage thumbnail', 'image'=>'Cover image', 'image_alt'=>'Image description', 'model_url'=>'Interactive 3D maquette (GLB)', 'plan_image'=>'Floor plan image (click-through plan)', 'plan_rooms'=>'Plan rooms (draw them on the plan)', 'cut_before'=>'Section-cut: drawing (left side)', 'cut_after'=>'Section-cut: render (right side)', 'cut_before_label'=>'Section-cut: left label', 'cut_after_label'=>'Section-cut: right label', 'page_width'=>'Magazine page width (mm)', 'page_height'=>'Magazine page height (mm)', 'gallery'=>'Gallery image paths (one per line)'],
            'experience', 'education' => ['period'=>'Period', 'location'=>'Location'],
            'skills' => ['category'=>'Category'],
            'contact' => ['url'=>'Social profile URL'],
            default => ['image'=>'Image', 'image_alt'=>'Image description'],
        };
    }
    public static function media(string $field): bool { return in_array($field, ['thumbnail','image','cv','portfolio','model_url','plan_image','cut_before','cut_after']); }
}
