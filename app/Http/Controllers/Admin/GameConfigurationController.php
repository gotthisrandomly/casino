<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::with('category');

        // Apply filters
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $games = $query->get();

        return response()->json($games);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'categoryId' => 'required|exists:game_categories,id',
            'rtp' => 'required|numeric|min:1|max:100',
            'volatility' => 'required|string|in:low,medium,high',
            'minBet' => 'required|numeric|min:0',
            'maxBet' => 'required|numeric|gt:minBet',
            'features' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048', // 2MB max
        ]);

        $game = new Game([
            'name' => $request->name,
            'category_id' => $request->categoryId,
            'rtp' => $request->rtp,
            'volatility' => $request->volatility,
            'min_bet' => $request->minBet,
            'max_bet' => $request->maxBet,
            'features' => $request->features,
            'status' => 'inactive', // Default to inactive
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('games/thumbnails', 'public');
            $game->thumbnail = $path;
        }

        $game->save();

        return response()->json($game, 201);
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'categoryId' => 'required|exists:game_categories,id',
            'rtp' => 'required|numeric|min:1|max:100',
            'volatility' => 'required|string|in:low,medium,high',
            'minBet' => 'required|numeric|min:0',
            'maxBet' => 'required|numeric|gt:minBet',
            'features' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $game->fill([
            'name' => $request->name,
            'category_id' => $request->categoryId,
            'rtp' => $request->rtp,
            'volatility' => $request->volatility,
            'min_bet' => $request->minBet,
            'max_bet' => $request->maxBet,
            'features' => $request->features,
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($game->thumbnail) {
                Storage::disk('public')->delete($game->thumbnail);
            }

            $path = $request->file('thumbnail')->store('games/thumbnails', 'public');
            $game->thumbnail = $path;
        }

        $game->save();

        return response()->json($game);
    }

    public function updateStatus(Request $request, Game $game)
    {
        $request->validate([
            'status' => 'required|string|in:active,inactive,maintenance',
        ]);

        $game->update(['status' => $request->status]);

        return response()->json($game);
    }

    public function categories()
    {
        $categories = GameCategory::withCount('games')->get();
        return response()->json($categories);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:game_categories',
            'description' => 'nullable|string',
            'icon' => 'required|string|in:slots,cards,table,live,instant',
        ]);

        $category = GameCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($category, 201);
    }

    public function updateCategory(Request $request, GameCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:game_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'required|string|in:slots,cards,table,live,instant',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($category);
    }

    public function deleteCategory(GameCategory $category)
    {
        // Check if category has games
        if ($category->games()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with associated games'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}