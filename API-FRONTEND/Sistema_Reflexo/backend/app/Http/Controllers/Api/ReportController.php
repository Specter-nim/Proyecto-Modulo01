<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $ReportService)
    {
        $this->ReportService = $ReportService;
        $this->middleware('can:reports.getNumberAppointmentsPerTherapist')->only('getNumberAppointmentsPerTherapist');
        $this->middleware('can:reports.getPatientsByTherapist')->only('getPatientsByTherapist');
        $this->middleware('can:reports.getDailyCash')->only('getDailyCash');
        $this->middleware('can:reports.getAppointmentsBetweenDates')->only('getAppointmentsBetweenDates');
    }

    public function getNumberAppointmentsPerTherapist(Request $request): JsonResponse
    {
        return $this->ReportService->getAppointmentsCountByTherapist($request);
    }

    public function getPatientsByTherapist(Request $request): JsonResponse
    {
        return $this->ReportService->getPatientsByTherapist($request);
    }

    public function getDailyCash(Request $request): JsonResponse
    {
        return $this->ReportService->getDailyCash($request);
    }

    public function getAppointmentsBetweenDates(Request $request): JsonResponse
    {
        return $this->ReportService->getAppointmentsBetweenDates($request);
    }
}
