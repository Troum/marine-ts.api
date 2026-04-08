<?php

namespace App\Support;

final class NormalizeTranslationInput
{
    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function newsLocaleRow(array $row): array
    {
        return [
            'title' => $row['title'],
            'excerpt' => $row['excerpt'],
            'content' => $row['content'] ?? null,
            'category' => $row['category'],
            'seo_title' => $row['seoTitle'] ?? $row['seo_title'] ?? null,
            'seo_description' => $row['seoDescription'] ?? $row['seo_description'] ?? null,
            'seo_keywords' => $row['seoKeywords'] ?? $row['seo_keywords'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function vacancyLocaleRow(array $row): array
    {
        $req = $row['requirements'] ?? null;

        return [
            'title' => $row['title'],
            'excerpt' => $row['excerpt'],
            'content' => $row['content'] ?? null,
            'requirements' => is_array($req) ? array_values($req) : null,
            'location' => $row['location'] ?? null,
            'employment_type' => $row['employmentType'] ?? $row['employment_type'] ?? null,
            'seo_title' => $row['seoTitle'] ?? $row['seo_title'] ?? null,
            'seo_description' => $row['seoDescription'] ?? $row['seo_description'] ?? null,
            'seo_keywords' => $row['seoKeywords'] ?? $row['seo_keywords'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function projectLocaleRow(array $row): array
    {
        return [
            'title' => $row['title'],
            'type_label' => $row['typeLabel'] ?? $row['type_label'],
            'location' => $row['location'],
            'description' => $row['description'],
            'stats' => $row['stats'],
            'seo_title' => $row['seoTitle'] ?? $row['seo_title'] ?? null,
            'seo_description' => $row['seoDescription'] ?? $row['seo_description'] ?? null,
            'seo_keywords' => $row['seoKeywords'] ?? $row['seo_keywords'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function serviceLocaleRow(array $row): array
    {
        $features = $row['features'] ?? [];

        return [
            'title' => $row['title'],
            'description' => $row['description'],
            'features' => is_array($features) ? array_values($features) : [],
            'seo_title' => $row['seoTitle'] ?? $row['seo_title'] ?? null,
            'seo_description' => $row['seoDescription'] ?? $row['seo_description'] ?? null,
            'seo_keywords' => $row['seoKeywords'] ?? $row['seo_keywords'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function contentPageLocaleRow(array $row): array
    {
        return [
            'title' => $row['title'],
            'excerpt' => $row['excerpt'] ?? null,
            'body' => $row['body'],
            'seo_title' => $row['seoTitle'] ?? $row['seo_title'] ?? null,
            'seo_description' => $row['seoDescription'] ?? $row['seo_description'] ?? null,
            'seo_keywords' => $row['seoKeywords'] ?? $row['seo_keywords'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function siteSeoLocaleRow(array $row): array
    {
        return [
            'label' => $row['label'] ?? null,
            'seo_title' => $row['seoTitle'] ?? $row['seo_title'] ?? null,
            'seo_description' => $row['seoDescription'] ?? $row['seo_description'] ?? null,
            'seo_keywords' => $row['seoKeywords'] ?? $row['seo_keywords'] ?? null,
        ];
    }
}
