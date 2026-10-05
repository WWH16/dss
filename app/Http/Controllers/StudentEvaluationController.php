<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Rules\Recaptcha;

// CHANGED: role checks moved out of each method into the role middleware on this controller's routes (routes/web.php).
class StudentEvaluationController extends Controller
{
    // ADDED: one statement list for the form (index) and the scoring (store); it was written out in both.
    public const STATEMENTS = [
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

    public function index(Request $request)
    {
        $user = Auth::user();

        $profile = $user;

        $stalls = DB::table('stalls')
            ->where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // If a specific stall was requested via query param, verify it is in the active list
        if ($request->filled('stall')) {
            $stallId = (int) $request->stall;
            if (!$stalls->contains('id', $stallId)) {
                $requestedStall = DB::table('stalls')->where('id', $stallId)->first();
                if ($requestedStall) {
                    return redirect()->route('student.evaluation')->with('error', "{$requestedStall->name} is currently closed for student evaluations.");
                }
            }
        }

        // CHANGED: statements moved to self::STATEMENTS; store() reads the same list.
        $displayStatements = self::STATEMENTS;

        return view('student.evaluation', compact(
            'profile',
            'stalls',
            'displayStatements'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // CHANGED: moved above validate() so the rating rules below are built from the same statement list the scores are computed from.
        // CHANGED: built from self::STATEMENTS instead of a second hand-written id => criterion list.
        $displayStatements = array_column(self::STATEMENTS, 'criterion_key', 'id');

        // CHANGED: every statement's rating is now required and must be a whole number from 1 to 5. Before, a missing answer crashed with a 500 error and any number (e.g. 100) was saved and fed into the SAW and AHP rankings.
        $ratingRules = [];
        foreach (array_keys($displayStatements) as $id) {
            $ratingRules["responses.$id"] = 'required|integer|between:1,5';
        }

        $request->validate([
            'stall_id' => [
                'required',
                Rule::exists('stalls', 'id')->where(function ($query) {
                    $query->where('is_active', true);
                }),
            ],
            'comment' => 'nullable|string',
            'g_recaptcha_response' => [new Recaptcha('evaluation')],
            // CHANGED: rating rules added (see above).
            'responses' => 'required|array',
        ] + $ratingRules, [
            'stall_id.exists' => 'The selected food stall is currently closed for student evaluations.',
            // CHANGED: plain-language messages for the new rating rules.
            'responses.required' => 'Please answer all survey statements before submitting.',
            'responses.*.required' => 'Please answer all survey statements before submitting.',
            'responses.*.integer' => 'Each rating must be a whole number from 1 to 5.',
            'responses.*.between' => 'Each rating must be a whole number from 1 to 5.',
        ]);

        $responses = $request->responses;

        // CHANGED: one loop averages each criterion; replaces running totals and counts plus four copied round() blocks.
        $ratings = [];
        foreach ($displayStatements as $id => $criterion) {
            $ratings[$criterion][] = (int) $responses[$id];
        }
        $averages = array_map(fn ($r) => round(array_sum($r) / count($r), 2), $ratings);

        DB::table('stall_evaluations')->insert($averages + [
            'student_id' => $user->id,
            'stall_id' => $request->stall_id,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success','Evaluation submitted successfully!');
    }
}