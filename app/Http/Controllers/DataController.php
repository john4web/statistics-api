<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use stdClass;

class DataController extends Controller
{
    public function getTodayData(Request $request): JsonResponse
    {

        $userId = intval($request->header('User-ID'));
        $goal = $request->json()->all();

        $foodItems = FoodItem::select('created_at', 'calories', 'carbs', 'proteins', 'fat')
            ->where('user_id', '=', $userId)
            ->whereDate('created_at', Carbon::today())
            ->get();

        $calories_sum = 0;
        $carbs_sum = 0;
        $proteins_sum = 0;
        $fat_sum = 0;

        foreach ($foodItems as $item) {
            $calories_sum += $item->calories;
            $carbs_sum += $item->carbs;
            $proteins_sum += $item->proteins;
            $fat_sum += $item->fat;
        }

        $progressCircle = new stdClass();
        $progressCircle->goalCalories = $goal['calories'];
        $progressCircle->consumedCalories = $calories_sum;
        $progressCircle->percentValue = round(($calories_sum / $goal['calories']) * 100, 0, PHP_ROUND_HALF_UP);
        $progressCircle->leftCalories = $goal['calories'] - $calories_sum;

        $carbs = new stdClass();
        $carbs->name = 'Carbs';
        $carbs->goalValue = $goal['carbs'];
        $carbs->consumedValue = $carbs_sum;
        $carbs->percentValue = round(($carbs_sum / $goal['carbs']) * 100, 0, PHP_ROUND_HALF_UP);

        $proteins = new stdClass();
        $proteins->name = 'Proteins';
        $proteins->goalValue = $goal['proteins'];
        $proteins->consumedValue = $proteins_sum;
        $proteins->percentValue = round(($proteins_sum / $goal['proteins']) * 100, 0, PHP_ROUND_HALF_UP);

        $fat = new stdClass();
        $fat->name = 'Fat';
        $fat->goalValue = $goal['fat'];
        $fat->consumedValue = $fat_sum;
        $fat->percentValue = round(($fat_sum / $goal['fat']) * 100, 0, PHP_ROUND_HALF_UP);

        $progressBars = array($carbs, $proteins, $fat);

        return response()->json(['status' => 'success', 'data' => ['progress_circle' => $progressCircle, 'progress_bars' => $progressBars]]);
    }

    public function getProcessData(Request $request): JsonResponse
    {
        $userId = intval($request->header('User-ID'));

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

        $data = array_values($result);

        $now = date("Y-m-d");
        $firstUsageDate = $data[0]['day'];
        $dayArray = $this->getDatesFromRange($firstUsageDate, $now);

        $emptyDayArray = [];
        foreach ($dayArray as $day) {
            array_push(
                $emptyDayArray,
                [
                    'day' => $day,
                    'value' => 0,
                    'carbs' => 0,
                    'proteins' => 0,
                    'fat' => 0
                ]
            );
        }

        foreach ($emptyDayArray as $key => $emptyDay) {

            foreach ($data as $fullDay) {
                if ($emptyDay['day'] === $fullDay['day']) {
                    $emptyDayArray[$key] = $fullDay;
                }
            }
        }

        $result = $emptyDayArray;

        return response()->json(['status' => 'success', 'data' => $result]);
    }


    // Function to get all the dates in given range
    private function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {

        // Declare an empty array
        $array = array();

        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        // Use loop to store date into array
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        // Return the array elements
        return $array;
    }
}
