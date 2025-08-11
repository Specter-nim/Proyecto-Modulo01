<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Statistics\StatisticsService;
use App\Http\Resources\StatisticsResource;

class StatisticsController extends Controller
{
    public function __construct(private StatisticsService $statisticsService)
        {
            // Aplica middleware solo si el método getStatistics necesita permiso específico
            $this->middleware('can:statistics.view')->only('getStatistics');
        }
    public function getStatistics(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $statistics = app(StatisticsService::class)->getStatistics($start, $end);

        return new StatisticsResource($statistics);
    }
}
