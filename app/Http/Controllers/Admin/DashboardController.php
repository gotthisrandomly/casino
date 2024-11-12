<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalDeposits' => Transaction::where('type', 'deposit')->sum('amount'),
            'totalWithdrawals' => Transaction::where('type', 'withdrawal')->sum('amount'),
            'activeGames' => Game::where('active', true)->count(),
        ];

        return response()->json($stats);
    }
}