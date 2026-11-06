<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\Complaint;
use App\Models\Price;
use App\Models\Stall;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * SEEDER: fills an empty database with DEMO DATA so the system can be tried
 * and defended right away. Every demo account uses the password "password".
 *
 *   admin@tindapresyo.test       Market Administrator
 *   inspector@tindapresyo.test   Market Inspector
 *   vendor1@tindapresyo.test     Vendor (stall M-01)   ... vendor5 (V-05, flagged), vendor6 (pending stall)
 *   shopper1@tindapresyo.test    Shopper               ... shopper2
 *
 * Run it with:   php artisan migrate:fresh --seed
 * (Before real use, delete the demo accounts or change their passwords.)
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026); // same "random" prices every time

        // ---- Accounts ---------------------------------------------------
        $admin = User::create(['name' => 'Maria Reyes (Market Administrator)', 'email' => 'admin@tindapresyo.test', 'password' => 'password', 'role' => 'admin', 'phone' => '0917 000 0001']);
        $inspector = User::create(['name' => 'Roberto Aquino (Market Inspector)', 'email' => 'inspector@tindapresyo.test', 'password' => 'password', 'role' => 'inspector', 'phone' => '0917 000 0002']);

        $shoppers = [
            User::create(['name' => 'Ana Villanueva', 'email' => 'shopper1@tindapresyo.test', 'password' => 'password', 'role' => 'shopper']),
            User::create(['name' => 'Carlo Mendoza', 'email' => 'shopper2@tindapresyo.test', 'password' => 'password', 'role' => 'shopper']),
        ];

        // ---- Commodities: name => [category, unit, usual price in pesos] -----
        $catalog = [
            'Rice, regular milled' => ['Rice & Grains', 'kg', 52],
            'Rice, well milled' => ['Rice & Grains', 'kg', 58],
            'Sugar, white' => ['Dry Goods', 'kg', 85],
            'Cooking oil' => ['Dry Goods', 'liter', 95],
            'Pork liempo' => ['Meat & Poultry', 'kg', 380],
            'Pork kasim' => ['Meat & Poultry', 'kg', 330],
            'Chicken, whole' => ['Meat & Poultry', 'kg', 205],
            'Eggs, medium' => ['Meat & Poultry', 'piece', 9],
            'Bangus (milkfish)' => ['Fish & Seafood', 'kg', 190],
            'Tilapia' => ['Fish & Seafood', 'kg', 140],
            'Galunggong' => ['Fish & Seafood', 'kg', 220],
            'Tomato' => ['Vegetables', 'kg', 80],
            'Red onion' => ['Vegetables', 'kg', 160],
            'Garlic' => ['Vegetables', 'kg', 190],
            'Cabbage' => ['Vegetables', 'kg', 70],
            'Potato' => ['Vegetables', 'kg', 110],
            'Banana (lakatan)' => ['Fruits', 'kg', 85],
        ];
        $commodities = [];
        foreach ($catalog as $name => [$category, $unit]) {
            $commodities[$name] = Commodity::create(['name' => $name, 'category' => $category, 'unit' => $unit]);
        }

        // ---- Vendors and stalls: [name, email no., stall no., stall name, section, status, sells] ----
        $plan = [
            ['Nena Dela Cruz', 1, 'M-01', "Aling Nena's Meat Shop", 'Meat & Poultry', 'active', ['Pork liempo', 'Pork kasim', 'Chicken, whole', 'Eggs, medium']],
            ['Ben Santos', 2, 'F-02', "Mang Ben's Fresh Fish", 'Fish & Seafood', 'active', ['Bangus (milkfish)', 'Tilapia', 'Galunggong']],
            ['Rosa Mercado', 3, 'V-03', "Ate Rosa's Veggies", 'Vegetables', 'active', ['Tomato', 'Red onion', 'Garlic', 'Cabbage', 'Potato']],
            ['Jun Ramos', 4, 'R-04', 'Kuya Jun Rice & Grains', 'Rice & Grains', 'active', ['Rice, regular milled', 'Rice, well milled', 'Sugar, white', 'Cooking oil']],
            ['Lita Bautista', 5, 'V-05', "Lita's Gulay at Prutas", 'Vegetables', 'active', ['Tomato', 'Red onion', 'Banana (lakatan)', 'Potato', 'Cabbage']],
            ['Pedro Cruz', 6, 'D-06', "Pedro's Dry Goods", 'Dry Goods', 'pending', []],
        ];

        $stalls = [];
        foreach ($plan as [$vendorName, $n, $number, $stallName, $section, $status, $sells]) {
            $vendor = User::create(['name' => $vendorName, 'email' => "vendor{$n}@tindapresyo.test", 'password' => 'password', 'role' => 'vendor']);
            $stalls[$number] = [
                'model' => Stall::create(['user_id' => $vendor->id, 'stall_number' => $number, 'name' => $stallName, 'section' => $section, 'status' => $status]),
                'vendor' => $vendor,
                'sells' => $sells,
            ];
        }

        // ---- Seven days of posted prices ------------------------------------------
        // F-02 did not post today, so it shows up as "not posted today".
        foreach ($stalls as $number => $info) {
            foreach ($info['sells'] as $name) {
                $base = $catalog[$name][2];
                for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
                    if ($number === 'F-02' && $daysAgo === 0) {
                        continue;
                    }
                    $price = round($base * (1 + mt_rand(-30, 30) / 1000), 2);
                    $verified = $daysAgo > 0 || in_array($number, ['M-01', 'V-03', 'R-04'], true);

                    Price::create([
                        'stall_id' => $info['model']->id,
                        'commodity_id' => $commodities[$name]->id,
                        'price' => $price,
                        'price_date' => now()->subDays($daysAgo)->toDateString(),
                        'posted_by' => $info['vendor']->id,
                        'verified_by' => $verified ? $inspector->id : null,
                        'verified_at' => $verified ? now()->subDays($daysAgo) : null,
                    ]);
                }
            }
        }

        // ---- Demo complaints ------------------------------------------------------------
        // A tiny placeholder picture stands in for the shopper's photo evidence.
        Storage::disk('local')->put('complaint-photos/sample-evidence.svg', $this->sampleEvidence());

        $file = function (User $shopper, string $stallNo, string $commodity, float $extra, string $text, int $daysAgo) use ($stalls, $commodities) {
            $stall = $stalls[$stallNo]['model'];
            $posted = $stall->latestPriceFor($commodities[$commodity]->id);

            $complaint = Complaint::create([
                'shopper_id' => $shopper->id,
                'stall_id' => $stall->id,
                'commodity_id' => $commodities[$commodity]->id,
                'price_id' => $posted->id,
                'posted_price' => $posted->price,
                'posted_price_date' => $posted->price_date,
                'reported_price' => round((float) $posted->price + $extra, 2),
                'description' => $text,
                'photo_path' => 'complaint-photos/sample-evidence.svg',
                'status' => 'pending',
            ]);
            $complaint->logs()->create(['user_id' => $shopper->id, 'type' => 'status', 'audience' => 'all', 'to_status' => 'pending', 'message' => 'Complaint submitted.']);
            $complaint->forceFill(['created_at' => now()->subDays($daysAgo)])->save();

            return $complaint;
        };

        [$ana, $carlo] = $shoppers;

        // Stall V-05: three confirmed complaints -> becomes a flagged repeat violator.
        $c1 = $file($ana, 'V-05', 'Tomato', 25, 'Charged 25 pesos more per kilo than the price on the board. Kept the receipt.', 6);
        $c1->transitionTo('under_review', $admin);
        $c1->transitionTo('confirmed', $admin, 'Receipt matches the stall and date. Posted price was lower.');
        $c1->transitionTo('resolved', $admin, 'Vendor was warned and corrected the price tag.');

        $c2 = $file($carlo, 'V-05', 'Red onion', 40, 'Onions were sold higher than the posted price.', 3);
        $c2->transitionTo('under_review', $admin);
        $c2->transitionTo('confirmed', $admin, 'Photo of price tag shows the higher price.');
        $c2->logs()->create(['user_id' => $admin->id, 'type' => 'message', 'audience' => 'vendor', 'message' => 'Please explain the difference and update your posted price today.']);
        $c2->logs()->create(['user_id' => $stalls['V-05']['vendor']->id, 'type' => 'message', 'audience' => 'vendor', 'message' => 'Sorry, the new stock was more expensive. I will update the board.']);

        $c3 = $file($ana, 'V-05', 'Banana (lakatan)', 20, 'Bananas were 20 pesos higher per kilo.', 1);
        $c3->transitionTo('under_review', $admin);
        $c3->transitionTo('confirmed', $admin, 'Confirmed against the posted price.');

        // Stall M-01: one confirmed (on the watch list) and one dismissed.
        $c4 = $file($carlo, 'M-01', 'Pork liempo', 30, 'Liempo cost 30 pesos more than the posted price.', 4);
        $c4->transitionTo('under_review', $admin);
        $c4->transitionTo('confirmed', $admin, 'Receipt shows the higher price.');

        $c5 = $file($ana, 'M-01', 'Eggs, medium', 1.5, 'Eggs were more expensive.', 2);
        $c5->transitionTo('under_review', $admin);
        $c5->transitionTo('dismissed', $admin, 'The photo shows a different egg size (large), which has its own price.');

        // Open complaints waiting for the administrator.
        $file($carlo, 'R-04', 'Sugar, white', 8, 'Sugar was sold above the posted price.', 0);
        $c7 = $file($ana, 'R-04', 'Cooking oil', 12, 'Cooking oil price at the counter was higher.', 0);
        $c7->transitionTo('under_review', $admin);
        $file($carlo, 'F-02', 'Tilapia', 15, 'Tilapia cost more than the price shown yesterday.', 0);
    }

    /** A simple receipt-style drawing used as demo "photo evidence". */
    private function sampleEvidence(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="480" height="360" viewBox="0 0 480 360">
  <rect width="480" height="360" fill="#e9e4d6"/>
  <rect x="150" y="24" width="180" height="312" rx="6" fill="#ffffff" stroke="#c9c2ad"/>
  <text x="240" y="58" text-anchor="middle" font-family="monospace" font-size="14" font-weight="bold">PUBLIC MARKET</text>
  <text x="240" y="78" text-anchor="middle" font-family="monospace" font-size="11" fill="#555">SAMPLE EVIDENCE</text>
  <line x1="168" y1="92" x2="312" y2="92" stroke="#bbb" stroke-dasharray="3 3"/>
  <text x="168" y="120" font-family="monospace" font-size="12">1.0 kg  ........</text>
  <text x="168" y="144" font-family="monospace" font-size="12">TOTAL  P ___.__</text>
  <line x1="168" y1="160" x2="312" y2="160" stroke="#bbb" stroke-dasharray="3 3"/>
  <text x="240" y="300" text-anchor="middle" font-family="monospace" font-size="11" fill="#777">demo photo (seed data)</text>
</svg>
SVG;
    }
}
