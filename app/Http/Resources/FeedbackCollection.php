<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FeedbackCollection extends ResourceCollection
{
    public $collects = FeedbackResource::class;
}
