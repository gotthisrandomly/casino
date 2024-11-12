<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'minDeposit' => env('CASINO_MIN_DEPOSIT', 10),
            'maxDeposit' => env('CASINO_MAX_DEPOSIT', 10000),
            'minWithdrawal' => env('CASINO_MIN_WITHDRAWAL', 20),
            'maxWithdrawal' => env('CASINO_MAX_WITHDRAWAL', 5000),
            'dailyWithdrawalLimit' => env('CASINO_DAILY_WITHDRAWAL_LIMIT', 20000),
        ];

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $request->validate([
            'minDeposit' => 'required|numeric|min:1',
            'maxDeposit' => 'required|numeric|gt:minDeposit',
            'minWithdrawal' => 'required|numeric|min:1',
            'maxWithdrawal' => 'required|numeric|gt:minWithdrawal',
            'dailyWithdrawalLimit' => 'required|numeric|gt:maxWithdrawal',
        ]);

        $this->updateEnvFile([
            'CASINO_MIN_DEPOSIT' => $request->minDeposit,
            'CASINO_MAX_DEPOSIT' => $request->maxDeposit,
            'CASINO_MIN_WITHDRAWAL' => $request->minWithdrawal,
            'CASINO_MAX_WITHDRAWAL' => $request->maxWithdrawal,
            'CASINO_DAILY_WITHDRAWAL_LIMIT' => $request->dailyWithdrawalLimit,
        ]);

        Cache::tags(['settings'])->flush();

        return response()->json(['message' => 'Settings updated successfully']);
    }

    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        }

        file_put_contents($envFile, $envContent);
    }
}