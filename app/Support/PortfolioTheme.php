<?php

namespace App\Support;

class PortfolioTheme
{
    public static function colors(): array
    {
        return [
            'color_accent' => ['label' => 'Accent / banner & contact', 'variable' => 'orange', 'default' => '#b47a52'],
            'color_background' => ['label' => 'Page background', 'variable' => 'paper', 'default' => '#f2efe8'],
            'color_text' => ['label' => 'Main text & buttons', 'variable' => 'ink', 'default' => '#26332e'],
            'color_muted' => ['label' => 'Secondary text', 'variable' => 'muted', 'default' => '#667067'],
            'color_surface' => ['label' => 'About section background', 'variable' => 'surface', 'default' => '#e7e1d6'],
            'color_border' => ['label' => 'Borders & dividers', 'variable' => 'line', 'default' => '#d8d2c5'],
            'color_dark' => ['label' => 'Dark section background', 'variable' => 'dark', 'default' => '#26332e'],
            'color_light' => ['label' => 'Text on dark sections & buttons', 'variable' => 'light', 'default' => '#f2efe8'],
            'color_dark_muted' => ['label' => 'Secondary text on dark sections', 'variable' => 'dark-muted', 'default' => '#c4c8bf'],
            'color_focus' => ['label' => 'Keyboard focus outline', 'variable' => 'focus', 'default' => '#476b55'],
        ];
    }

    public static function value(array $settings, string $key): string
    {
        $value = $settings[$key] ?? null;
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/D', $value)
            ? $value : self::colors()[$key]['default'];
    }
}
