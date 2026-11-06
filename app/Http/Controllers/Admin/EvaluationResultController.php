<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Support\Stats;

/**
 * CONTROLLER: results of the ISO/IEC 25010 evaluation (Statement of the
 * Problem no. 4 and the "significant difference" test in the Abstract).
 *
 * For each quality characteristic it shows the weighted mean and Likert
 * rating of IT experts, of end users, and of everyone, plus Welch's t-test
 * comparing the two groups.
 */
class EvaluationResultController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::with('user')->get();
        $characteristics = config('iso25010');

        $rows = [];
        foreach ($characteristics as $key => $group) {
            $scores = ['it_expert' => [], 'end_user' => []];

            foreach ($evaluations as $e) {
                $answers = $e->answers ?? [];
                $values = [];
                foreach (array_keys($group['items']) as $i) {
                    if (isset($answers["$key.$i"])) {
                        $values[] = $answers["$key.$i"];
                    }
                }
                if ($values) {
                    $scores[$e->respondent_group][] = Stats::mean($values); // this respondent's mean for the characteristic
                }
            }

            $rows[] = $this->row($group['label'], $scores);
        }

        // Overall: every respondent's mean across all statements.
        $overall = ['it_expert' => [], 'end_user' => []];
        foreach ($evaluations as $e) {
            $overall[$e->respondent_group][] = Stats::mean(array_values($e->answers));
        }
        $total = $this->row('Overall quality', $overall);

        return view('admin.evaluation.index', [
            'rows' => $rows,
            'total' => $total,
            'byType' => $evaluations->groupBy('respondent_type')->map->count(),
            'respondents' => $evaluations->count(),
            'comments' => $evaluations->filter(fn ($e) => filled($e->comments))->values(),
            'types' => Evaluation::RESPONDENT_TYPES,
        ]);
    }

    private function row(string $label, array $scores): array
    {
        $all = array_merge($scores['it_expert'], $scores['end_user']);
        $test = Stats::welchT($scores['it_expert'], $scores['end_user']);

        $expert = Stats::mean($scores['it_expert']);
        $user = Stats::mean($scores['end_user']);
        $both = Stats::mean($all);

        return [
            'label' => $label,
            'n_expert' => count($scores['it_expert']),
            'n_user' => count($scores['end_user']),
            'expert' => $expert,
            'expert_rating' => Stats::rating($expert),
            'user' => $user,
            'user_rating' => Stats::rating($user),
            'all' => $both,
            'all_rating' => Stats::rating($both),
            'test' => $test,
            'significant' => $test !== null && $test['p'] < 0.05,
        ];
    }
}
