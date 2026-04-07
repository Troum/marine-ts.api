<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'slug',
    'label',
    'seo_title',
    'seo_description',
    'seo_keywords',
])]
class SiteSeoPage extends Model
{
}
