<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Story;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    // ストーリー一覧表示
    public function index()
    {
        // 最新の24時間以内のストーリーだけ取得
        $stories = Story::where('created_at', '>=', now()->subDay())
            ->with('user')
            ->get()
            ->groupBy('user_id');

        return view('stories.index', compact('stories'));
    }

    // ストーリー投稿処理
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'text' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('stories', 'public');

        Story::create([
            'user_id' => Auth::id(),
            'image_path' => $path,
            'text' => $request->text,
        ]);

        return redirect()->back()->with('success', 'Story posted!');
    }
}
