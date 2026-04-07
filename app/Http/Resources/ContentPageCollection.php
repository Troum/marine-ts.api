<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContentPageCollection extends ResourceCollection
{
    public $collects = ContentPageResource::class;
}
