<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ApplicationFormCollection extends ResourceCollection
{
    public $collects = ApplicationFormResource::class;
}
