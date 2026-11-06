<?php

/*
|--------------------------------------------------------------------------
| ISO/IEC 25010 evaluation questionnaire
|--------------------------------------------------------------------------
| The eight quality characteristics named in the paper, each with a few
| statements respondents rate from 1 (Poor) to 5 (Excellent). The
| Evaluation module reads this file to build the form and the results.
*/

return [
    'functional_suitability' => [
        'label' => 'Functional Suitability',
        'items' => [
            'The system provides all the functions needed for stall price posting.',
            'The system correctly compares a reported price against the posted price.',
            'The system provides the functions needed to verify and resolve complaints.',
        ],
    ],
    'performance_efficiency' => [
        'label' => 'Performance Efficiency',
        'items' => [
            'Pages load quickly.',
            'Photo evidence uploads without noticeable delay.',
            'Reports are generated in a reasonable time.',
        ],
    ],
    'compatibility' => [
        'label' => 'Compatibility',
        'items' => [
            'The system works correctly in different web browsers.',
            'The system works correctly on desktop, tablet, and smartphone screens.',
            'The system works without conflict with other software on my device.',
        ],
    ],
    'usability' => [
        'label' => 'Usability',
        'items' => [
            'The system is easy to learn.',
            'The screens and menus are clear and easy to understand.',
            'Posting a price or filing a complaint takes few steps.',
        ],
    ],
    'reliability' => [
        'label' => 'Reliability',
        'items' => [
            'The system works consistently without unexpected errors.',
            'Saved prices and complaints are not lost.',
            'The system shows clear messages when something goes wrong.',
        ],
    ],
    'security' => [
        'label' => 'Security',
        'items' => [
            'Only authorized users can access restricted pages.',
            'Photo evidence and complaint records are protected from unauthorized access.',
            'User accounts are protected by a password.',
        ],
    ],
    'maintainability' => [
        'label' => 'Maintainability',
        'items' => [
            'The system is organized so that features can be modified or corrected easily.',
            'Settings such as the violation limit can be changed without rewriting the system.',
            'New commodities, stalls, and users can be added easily.',
        ],
    ],
    'portability' => [
        'label' => 'Portability',
        'items' => [
            'The system can be accessed on different operating systems.',
            'The system does not require installing software on the device.',
            'The system can be moved to another web hosting server with little effort.',
        ],
    ],
];
