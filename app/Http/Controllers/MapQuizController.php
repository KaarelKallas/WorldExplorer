<?php

namespace App\Http\Controllers;

use App\Models\MapQuestion;
use App\Models\MapQuiz;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MapQuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('MapQuiz/ChooseQuiz', [
            'quizzes' => MapQuiz::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Request::validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
        ]);
       $mapquizzes = MapQuiz::create($validated);
        if ($images = Request::file('images')) {
            foreach ($images as $image) {
                $mapquizzes->addMedia($image)->toMediaCollection('images');
            }
        }
    }

    /**
     * Display the specified resource.
     */


    public $score = 0;

    public function show(MapQuiz $mapQuiz, $id)
    {

        function haversineGreatCircleDistance(

            $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
          {
            // convert from degrees to radians
            $latFrom = deg2rad($latitudeFrom);
            $lonFrom = deg2rad($longitudeFrom);
            $latTo = deg2rad($latitudeTo);
            $lonTo = deg2rad($longitudeTo);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
              cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            return $angle * $earthRadius;
          }
          $currentq = null;
        if (Request::input('lat') == 0) {
            $distance = 0;
            $currentq = 0;
        }
        else {
            $mq = MapQuestion::where('id', Request::input('id'))->first();
            $distance = haversineGreatCircleDistance(Request::input('lat'), Request::input('lng'), $mq->lat, $mq->lng);

        }


        $questions = [];
        foreach (MapQuestion::select('id', 'question')->where('map_quiz_id', $id)->get() as $value) {
            array_push($questions, $value);
            shuffle($questions);
        }
          return Inertia::render('MapQuiz/PlayQuiz', [


            'quiz' => MapQuiz::where('id', $id)->get(),
            'questions' => $questions,
            'score' => $this->score,
            'distance' => $distance,
            'currentQuestion' => $currentq,

        ]);





    }
    public function submitanswer(Request $request)
    {









    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MapQuiz $mapQuiz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MapQuiz $mapQuiz)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MapQuiz $mapQuiz)
    {
        //
    }
}
