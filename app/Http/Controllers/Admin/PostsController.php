<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class PostsController extends Controller
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function index(Request $request)
    {
        $query = Post::with(['user', 'categoryPost.category'])->withTrashed();

        if ($request->filled('owner')) {
            $query->where('user_id', $request->owner);
        }

        if ($request->filled('category')) {
            $query->whereHas('categoryPost', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        if ($request->status === 'visible') {
            $query->whereNull('deleted_at');
        } elseif ($request->status === 'hidden') {
            $query->onlyTrashed();
        }

        $all_posts = $query->latest()->paginate(10);

        // オーナー（ユーザー）一覧とカテゴリ一覧を取得
        $owners = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.index', compact('all_posts', 'owners', 'categories'));
    }


    public function hide($id)
    {
        $this->post->destroy($id);
        return redirect()->back();
    }

    public function unhide($id)
    {
        $this->post->onlyTrashed()->findOrFail($id)->restore();
        return redirect()->back();
    }
}
