<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Story;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    // Display story list
    public function index()
    {
        // Get only stories from the last 24 hours
        $stories = Story::where('created_at', '>=', now()->subDay())
            ->with('user')
            ->get()
            ->groupBy('user_id');

        return view('stories.index', compact('stories'));
    }

    // Story posting process
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
            'text' => $request->input('text'),
        ]);

        return redirect()->back()->with('success', 'Story posted!');
    }

    // ストーリー投稿フォーム表示
    public function create()
    {
        return view('stories.create');
    }

    // ✅ ストーリー削除処理
    public function destroy(Story $story)
    {
        // 自分のストーリーじゃない場合は403
        if ($story->user_id !== auth()->id()) {
            abort(403);
        }

        // 画像ファイルも削除（あれば）
        if (Storage::exists($story->image_path)) {
            Storage::delete($story->image_path);
        }

        // DBから削除
        $story->delete();
        return back();
    }
}
