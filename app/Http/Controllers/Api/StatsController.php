<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\StatsServiceInterface;
use App\Http\Controllers\Controller;

class StatsController extends Controller
{
    public function __construct(
        private readonly StatsServiceInterface $statsService,
    ) {}

    public function __invoke()
    {
        return response()->json($this->statsService->getAggregates());
    }
}
