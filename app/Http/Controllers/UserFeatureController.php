<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class UserFeatureController extends Controller
{
    public function show(string $feature): View
    {
        $features = [
            'dps' => [
                'title' => 'DPS',
                'description' => 'Manage your deposit pension scheme settings and plans.',
            ],
            'loan' => [
                'title' => 'Loan',
                'description' => 'Review loan products and track your active loan requests.',
            ],
            'mobile-top-up' => [
                'title' => 'Mobile Top Up',
                'description' => 'Recharge mobile lines and manage airtime top-up activity.',
                'comingSoon' => true,
            ],
            'statement' => [
                'title' => 'Statement',
                'description' => 'Export and review your account statements and summaries.',
            ],
            'referral' => [
                'title' => 'Referral',
                'description' => 'Share referral links and monitor referral rewards.',
            ],
        ];

        abort_unless(isset($features[$feature]), 404);

        return view('features.show', [
            'featureKey' => $feature,
            'feature' => $features[$feature],
        ]);
    }
}
