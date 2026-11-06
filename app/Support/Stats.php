<?php

namespace App\Support;

/**
 * SUPPORT CLASS: the small statistics toolbox used by the ISO/IEC 25010
 * evaluation module. It has no Laravel dependencies, so it can be tested
 * on its own.
 *
 *  - mean()      the average (the "weighted mean" of the paper's Table 4)
 *  - welchT()    Welch's t-test, used to decide whether IT experts and end
 *                users rated the system differently
 *  - rating()    turns a mean into Excellent / Very Good / Good / Fair / Poor
 */
class Stats
{
    public static function mean(array $values): ?float
    {
        $values = array_values($values);

        return count($values) ? array_sum($values) / count($values) : null;
    }

    /** Sample variance (divides by n - 1). */
    public static function variance(array $values): ?float
    {
        $n = count($values);
        if ($n < 2) {
            return null;
        }

        $mean = array_sum($values) / $n;
        $sum = 0.0;
        foreach ($values as $v) {
            $sum += ($v - $mean) ** 2;
        }

        return $sum / ($n - 1);
    }

    /**
     * Welch's two-sample t-test (does not assume equal variances).
     * Returns ['t' => ..., 'df' => ..., 'p' => ...] or null when there are
     * not enough ratings (each group needs at least two respondents).
     */
    public static function welchT(array $a, array $b): ?array
    {
        $na = count($a);
        $nb = count($b);
        if ($na < 2 || $nb < 2) {
            return null;
        }

        $va = self::variance($a);
        $vb = self::variance($b);
        $se2 = $va / $na + $vb / $nb;

        // Both groups gave identical ratings: there is nothing to test.
        if ($se2 <= 0.0) {
            $same = abs(self::mean($a) - self::mean($b)) < 1e-12;

            return ['t' => 0.0, 'df' => (float) ($na + $nb - 2), 'p' => $same ? 1.0 : 0.0];
        }

        $t = (self::mean($a) - self::mean($b)) / sqrt($se2);
        $df = ($se2 ** 2) / ((($va / $na) ** 2) / ($na - 1) + (($vb / $nb) ** 2) / ($nb - 1));

        // Two-sided p-value from the t distribution.
        $x = $df / ($df + $t * $t);
        $p = self::incompleteBeta($x, $df / 2, 0.5);

        return ['t' => $t, 'df' => $df, 'p' => min(1.0, max(0.0, $p))];
    }

    /** Descriptive rating from the five-point scale in config/tindapresyo.php. */
    public static function rating(?float $mean, ?array $scale = null): string
    {
        if ($mean === null) {
            return '-';
        }

        $scale = $scale ?? config('tindapresyo.rating_scale');
        foreach ($scale as $row) {
            if ($mean + 1e-9 >= $row['min']) {
                return $row['label'];
            }
        }

        return 'Poor';
    }

    // ---- Maths helpers for the p-value ---------------------------------

    /** Regularized incomplete beta function I_x(a, b). */
    public static function incompleteBeta(float $x, float $a, float $b): float
    {
        if ($x <= 0.0) {
            return 0.0;
        }
        if ($x >= 1.0) {
            return 1.0;
        }

        $front = exp(self::logGamma($a + $b) - self::logGamma($a) - self::logGamma($b)
            + $a * log($x) + $b * log(1.0 - $x));

        if ($x < ($a + 1.0) / ($a + $b + 2.0)) {
            return $front * self::betaContinuedFraction($x, $a, $b) / $a;
        }

        return 1.0 - $front * self::betaContinuedFraction(1.0 - $x, $b, $a) / $b;
    }

    /** Continued fraction for the incomplete beta function (Lentz's method). */
    private static function betaContinuedFraction(float $x, float $a, float $b): float
    {
        $tiny = 1e-30;
        $qab = $a + $b;
        $qap = $a + 1.0;
        $qam = $a - 1.0;
        $c = 1.0;
        $d = 1.0 - $qab * $x / $qap;
        if (abs($d) < $tiny) {
            $d = $tiny;
        }
        $d = 1.0 / $d;
        $h = $d;

        for ($m = 1; $m <= 300; $m++) {
            $m2 = 2 * $m;

            $aa = $m * ($b - $m) * $x / (($qam + $m2) * ($a + $m2));
            $d = 1.0 + $aa * $d;
            if (abs($d) < $tiny) {
                $d = $tiny;
            }
            $c = 1.0 + $aa / $c;
            if (abs($c) < $tiny) {
                $c = $tiny;
            }
            $d = 1.0 / $d;
            $h *= $d * $c;

            $aa = -($a + $m) * ($qab + $m) * $x / (($a + $m2) * ($qap + $m2));
            $d = 1.0 + $aa * $d;
            if (abs($d) < $tiny) {
                $d = $tiny;
            }
            $c = 1.0 + $aa / $c;
            if (abs($c) < $tiny) {
                $c = $tiny;
            }
            $d = 1.0 / $d;
            $delta = $d * $c;
            $h *= $delta;

            if (abs($delta - 1.0) < 1e-12) {
                break;
            }
        }

        return $h;
    }

    /** Natural log of the gamma function (Lanczos approximation). */
    public static function logGamma(float $x): float
    {
        $coef = [
            76.18009172947146, -86.50532032941677, 24.01409824083091,
            -1.231739572450155, 0.1208650973866179e-2, -0.5395239384953e-5,
        ];
        $y = $x;
        $tmp = $x + 5.5;
        $tmp -= ($x + 0.5) * log($tmp);
        $ser = 1.000000000190015;
        foreach ($coef as $c) {
            $ser += $c / ++$y;
        }

        return -$tmp + log(2.5066282746310005 * $ser / $x);
    }
}
