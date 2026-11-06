<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CONTROLLER: the ISO/IEC 25010 questionnaire that IT experts and end users
 * answer after trying the system. Each user has one response, which they
 * can edit. The statements live in config/iso25010.php.
 */
class EvaluationController extends Controller
{
    public function form(Request $request)
    {
        $user = $request->user();

        return view('evaluation.form', [
            'characteristics' => config('iso25010'),
            'existing' => $user->evaluation,
            'types' => Evaluation::RESPONDENT_TYPES,
            'defaultType' => match ($user->role) {
                User::ROLE_ADMIN => 'admin_personnel',
                User::ROLE_INSPECTOR => 'inspector',
                User::ROLE_VENDOR => 'vendor',
                default => 'shopper',
            },
        ]);
    }

    public function store(Request $request)
    {
        $characteristics = config('iso25010');

        $rules = [
            'respondent_type' => ['required', Rule::in(array_keys(Evaluation::RESPONDENT_TYPES))],
            'comments' => ['nullable', 'string', 'max:2000'],
        ];
        foreach ($characteristics as $key => $group) {
            foreach (array_keys($group['items']) as $i) {
                $rules["answers.$key.$i"] = ['required', 'integer', 'between:1,5'];
            }
        }

        $data = $request->validate($rules, [
            'answers.*.required' => 'Please rate every statement.',
            'answers.*.*.required' => 'Please rate every statement.',
        ]);

        // Flatten to {"usability.0": 5, ...} for compact storage.
        $flat = [];
        foreach ($data['answers'] as $key => $items) {
            foreach ($items as $i => $score) {
                $flat["$key.$i"] = (int) $score;
            }
        }

        Evaluation::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'respondent_type' => $data['respondent_type'],
                'respondent_group' => Evaluation::groupFor($data['respondent_type']),
                'answers' => $flat,
                'comments' => $data['comments'] ?? null,
            ]
        );

        return redirect()->route('dashboard')->with('success', 'Thank you! Your evaluation was saved.');
    }
}
