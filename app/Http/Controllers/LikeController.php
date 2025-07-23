<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    private $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }

    public function store($post_id)
    {
        $this->like->user_id = Auth::user()->id;
        $this->like->post_id = $post_id;
        $this->like->save();

        // Create notification for post owner
        $post = Post::find($post_id);
        if ($post && $post->user_id !== Auth::user()->id) {
            Auth::user()->createNotification(
                'like',
                $post->user_id,
                $post
            );
        }

        return redirect()->back();
    }

    public function destroy($post_id)
    {
        $this->like
            ->where('user_id', Auth::user()->id)
            ->where('post_id', $post_id)
            ->delete();

        return redirect()->back();
    }
}
