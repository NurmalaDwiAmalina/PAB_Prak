<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Tour;
use App\Models\TourClient;

class TourController extends Controller
{
    public function index()
    {
        $result = [];
        $nextWeek = Carbon::today()->addWeek();
        for ($i = Carbon::today(); $i < $nextWeek; $i = $i->addDay()) {
            $remaining = 10;
            $booked = 0;
            $attended = 0;
            $tour = Tour::where('tour_date', $i)->first();
            $result[] = [
                'tour_date' => $i->copy(),
                'remaining' => $remaining
                ,
                'booked' => $booked,
                'attended' => $attended
            ];
        }
        return response()->json($result, 200);
    }

    public function show(string $id)
    {
        $client_name = '';
        $tour_date = Carbon::today();
        $tour_client = TourClient::where('unique_id', $id)->first();
        if ($tour_client) {
            $client_name = $tour_client->client_name;
            $tour = Tour::where('id', $tour_client->tour_id)->first();
            if ($tour) {
                $tour_date = $tour->tour_date;
            }
        }
        return response()->json([
            'client_name' => $client_name,
            'tour_date' => $tour_date
        ], 200);
    }

    public function attend(Request $request, $id)
    {
        $tour_client = TourClient::where('unique_id', $id)->first();
        $tour = Tour::where('id', $tour_client->tour_id)->first();
        $tour_client->is_attended = 1;
        $tour_client->save();
        $tour->attended++;
        $tour->save();
        return $tour_client;
    }
}
