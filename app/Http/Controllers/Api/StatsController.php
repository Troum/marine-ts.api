<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\StatsServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatsAggregatesResource;

class StatsController extends Controller
{
    public function __construct(
        private readonly StatsServiceInterface $statsService,
    ) {}

    public function __invoke(): StatsAggregatesResource
    {
        return new StatsAggregatesResource($this->statsService->getAggregates());
    }
}
