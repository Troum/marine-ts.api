<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AdminUserCollection extends ResourceCollection
{
    public $collects = AdminUserResource::class;
}
