<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
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
        $userId = 1; //intval($request->header('User-ID'));

        $foodItemsGroupedByDate = FoodItem::select('created_at', 'calories', 'carbs', 'proteins', 'fat')
            ->where('user_id', '=', $userId)
            ->orderBy('created_at')->get()
            ->groupBy(function ($item) {

                return $item->created_at->format('Y-m-d');
            });

        $result = [];

        foreach ($foodItemsGroupedByDate as $key => $value1) {
            $result[$key]['day'] = $key;
            $result[$key]['value'] = 0;
            $result[$key]['carbs'] = 0;
            $result[$key]['proteins'] = 0;
            $result[$key]['fat'] = 0;
            foreach ($value1 as $value2) {
                $result[$key]['value'] += $value2->calories;
                $result[$key]['carbs'] += $value2->carbs;
                $result[$key]['proteins'] += $value2->proteins;
                $result[$key]['fat'] += $value2->fat;
            }
        }

        $result = array_values($result);

        return response()->json(['status' => 'success', 'data' => $result]);
    }
}
