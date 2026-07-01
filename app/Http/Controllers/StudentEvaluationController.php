<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentEvaluationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return redirect('/login');
        }

        $profile = DB::table('users')
            ->where('id', $user->id)
            ->first();

        $stalls = DB::table('stalls')
            ->orderBy('name')
            ->get();

        $displayStatements = [
            [
                'id' => 1,
                'statement' => 'How do you rate the overall quality of the meals served/offered, is it healthy and nutritious?',
                'criterion_key' => 'taste'
            ],
            [
                'id' => 2,
                'statement' => 'How do you rate the presentation of the food displayed, is it covered or placed in an enclosed glass display?',
                'criterion_key' => 'taste'
            ],
            [
                'id' => 3,
                'statement' => 'How do you rate the varieties of food/menus provided?',
                'criterion_key' => 'taste'
            ],
            [
                'id' => 4,
                'statement' => 'How do you rate the quantity of food provided?',
                'criterion_key' => 'price'
            ],
            [
                'id' => 5,
                'statement' => 'How do you rate the price of the food?',
                'criterion_key' => 'price'
            ],
            [
                'id' => 6,
                'statement' => 'How do you rate the appearance of the food server/staff, is she wearing hair net, apron, mask and hand gloves?',
                'criterion_key' => 'cleanliness'
            ],
            [
                'id' => 7,
                'statement' => 'How satisfied are you with the utensils you used when eating, is it clean or sanitized?',
                'criterion_key' => 'cleanliness'
            ],
            [
                'id' => 8,
                'statement' => 'How do you rate the food stall, is it hygienic, orderly and observing proper waste management?',
                'criterion_key' => 'cleanliness'
            ],
            [
                'id' => 9,
                'statement' => 'How do you rate the ambience of the dining area/food court, is it a pleasant place to sit and enjoy your meals?',
                'criterion_key' => 'service'
            ],
            [
                'id' => 10,
                'statement' => 'Overall, how do you rate the service of the food stall where you bought your meal?',
                'criterion_key' => 'service'
            ],
        ];

        return view('student.evaluation', compact(
            'profile',
            'stalls',
            'displayStatements'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'stall_id' => 'required|exists:stalls,id',
            'comment' => 'nullable|string',
        ]);

        $responses = $request->responses;

        $criterionTotals = [];
        $criterionCounts = [];

        $displayStatements = [
            1=>'taste',
            2=>'taste',
            3=>'taste',
            4=>'price',
            5=>'price',
            6=>'cleanliness',
            7=>'cleanliness',
            8=>'cleanliness',
            9=>'service',
            10=>'service',
        ];

        foreach($displayStatements as $id=>$criterion){

            $rating = (int)$responses[$id];

            $criterionTotals[$criterion] =
                ($criterionTotals[$criterion] ?? 0) + $rating;

            $criterionCounts[$criterion] =
                ($criterionCounts[$criterion] ?? 0) + 1;
        }

        $cleanliness = round(
            $criterionTotals['cleanliness'] /
            $criterionCounts['cleanliness'],2);

        $service = round(
            $criterionTotals['service'] /
            $criterionCounts['service'],2);

        $taste = round(
            $criterionTotals['taste'] /
            $criterionCounts['taste'],2);

        $price = round(
            $criterionTotals['price'] /
            $criterionCounts['price'],2);

        DB::table('stall_evaluations')->insert([

            'student_id'=>$user->id,

            'stall_id'=>$request->stall_id,

            'cleanliness'=>$cleanliness,

            'service'=>$service,

            'taste'=>$taste,

            'price'=>$price,

            'comment'=>$request->comment,

            'created_at'=>now(),

            'updated_at'=>now(),

        ]);

        return back()->with('success','Evaluation submitted successfully!');
    }
}