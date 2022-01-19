<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DataController extends Controller
{
    public function getTodayData(Request $request): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => "getTodayData"]);
    }

    public function getProcessData(Request $request): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => "getProcessData"]);
    }
}
