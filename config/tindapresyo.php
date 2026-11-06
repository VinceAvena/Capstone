<?php

/*
|--------------------------------------------------------------------------
| TindaPresyo settings
|--------------------------------------------------------------------------
| Central place for the numbers and lists the system uses. Change a value
| here (or in .env) and the whole system follows; no code edits needed.
*/

return [

    // Name shown in the header and on printed reports.
    'market_name' => env('TP_MARKET_NAME', 'City Public Market'),

    // The market is in the Philippines, so "today" must follow Manila time.
    'timezone' => env('TP_TIMEZONE', 'Asia/Manila'),

    // A stall is flagged as a repeat violator once this many complaints
    // against it have been confirmed (see Stall::refreshFlag()).
    'violation_threshold' => (int) env('TP_VIOLATION_THRESHOLD', 3),

    // Maximum size of a photo evidence upload, in kilobytes.
    'photo_max_kb' => 4096,

    // Market sections used to group stalls and commodities.
    'sections' => [
        'Meat & Poultry',
        'Fish & Seafood',
        'Vegetables',
        'Fruits',
        'Rice & Grains',
        'Dry Goods',
        'Condiments & Spices',
        'Others',
    ],

    // Units a commodity can be sold in.
    'units' => ['kg', 'piece', 'liter', 'bundle', 'tray', 'can', 'pack'],

    // Five-point Likert scale from Table 4 of the paper.
    'rating_scale' => [
        ['min' => 4.20, 'label' => 'Excellent'],
        ['min' => 3.40, 'label' => 'Very Good'],
        ['min' => 2.60, 'label' => 'Good'],
        ['min' => 1.80, 'label' => 'Fair'],
        ['min' => 1.00, 'label' => 'Poor'],
    ],
];
