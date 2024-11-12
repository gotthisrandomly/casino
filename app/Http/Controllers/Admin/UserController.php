<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', false)->get();
        return response()->json($users);
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'suspended' : 'active';
        $user->save();

        return response()->json(['message' => 'User status updated successfully']);
    }

    public function updateBalance(Request $request, User $user)
    {
        $request->validate([
            'balance' => 'required|numeric|min:0',
        ]);

        $user->balance = $request->balance;
        $user->save();

        return response()->json(['message' => 'User balance updated successfully']);
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    }
}