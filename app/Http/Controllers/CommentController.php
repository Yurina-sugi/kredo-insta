<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    private $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function store(Request $request, $post_id)
    {
        $request->validate(
            [
                'comment_body' . $post_id => 'required|max:150'
            ],
            [
                'comment_body' . $post_id . '.required' => 'You cannot submit an empty comment.',
                'comment_body' . $post_id . '.max' => 'The comment must not have more than 150 characters.'
            ]
        );

        $this->comment->body = $request->input('comment_body' . $post_id);
        $this->comment->user_id = Auth::user()->id;
        $this->comment->post_id = $post_id;
        $this->comment->save();

        // Create notification for post owner
        $post = Post::find($post_id);
        if ($post && $post->user_id !== Auth::user()->id) {
            Auth::user()->createNotification(
                'comment',
                $post->user_id,
                $post,
                ['comment_id' => $this->comment->id]
            );
        }

        return redirect()->route('post.show', $post_id);
    }

    public function destroy($id)
    {
        $this->comment->destroy($id);
        return redirect()->back();
    }
}
