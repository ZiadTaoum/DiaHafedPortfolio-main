<?php

namespace App\Support;

class ProjectSections
{
    public const LABELS = [
        'details' => 'Project details',
        'description' => 'Description',
        'cover' => 'Cover image',
        'model' => 'Interactive model',
        'cut' => 'Section-cut comparison',
        'plan' => 'Interactive floor plan',
        'flipbook' => 'Magazine flipbook',
    ];

    public static function galleries(array $data): array
    {
        if (isset($data['galleries'])) {
            return $data['galleries'];
        }

        $paths = array_values(array_unique(array_filter(array_map('trim', preg_split('/\R/', $data['gallery'] ?? '')))));

        return $paths ? [[
            'title' => '',
            'visible' => true,
            'images' => array_map(fn ($path) => ['path' => $path], $paths),
        ]] : [];
    }
}
