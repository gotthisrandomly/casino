<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        // Apply sorting
        $sortBy = $request->sortBy ?? 'created_at';
        $sortOrder = $request->sortOrder ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results
        $perPage = $request->per_page ?? 10;
        $users = $query->paginate($perPage);

        return response()->json([
            'users' => $users->items(),
            'total' => $users->total(),
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'last_page' => $users->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:user,vip,moderator',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:user,vip,moderator',
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'status']));

        return response()->json($user);
    }

    public function show(User $user)
    {
        $user->load('transactions', 'bets');

        $stats = [
            'totalBets' => $user->bets->count(),
            'winRate' => $user->calculateWinRate(),
            'averageBet' => $user->calculateAverageBet(),
        ];

        $recentActivity = $user->getRecentActivity();

        return response()->json([
            'user' => $user,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function resetPassword(User $user)
    {
        $status = Password::sendResetLink(
            ['email' => $user->email]
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset email sent'])
            : response()->json(['message' => 'Unable to send password reset email'], 500);
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $user->update(['status' => $request->status]);

        return response()->json($user);
    }

    public function export(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['ID', 'Name', 'Email', 'Role', 'Status', 'Created At']);

        foreach ($users as $user) {
            fputcsv($csv, [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->status,
                $user->created_at,
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="users.csv"');
    }
}