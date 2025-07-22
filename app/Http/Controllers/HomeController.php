<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\Story;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $post;
    private $user;
    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $home_posts = $this->getHomePosts();
        $suggested_users = $this->getSuggestedUsers();

        // 🟡 ストーリーを取得
        $stories = Story::where('created_at', '>=', now()->subHours(24))
            ->with('user')
            ->latest()
            ->get()
            ->groupBy('user_id');

        // ✅ return の中で渡す！
        return view('users.home')
            ->with('home_posts', $home_posts)
            ->with('suggested_users', $suggested_users)
            ->with('stories', $stories);
    }


    #Get the posts  of the users Auth follows
    public function getHomePosts()
    {
        $all_posts = $this->post->latest()->get();
        $home_posts = [];

        foreach ($all_posts as $post) {
            if ($post->user->isFollowed() || $post->user->id === Auth::user()->id) {
                $home_posts[] = $post;
            }
        }

        return $home_posts;
    }

    public function getSuggestedUsers()
    {
        $all_users = $this->user->all()->except(Auth::user()->id);
        $suggested_users = [];

        foreach ($all_users as $user) {
            if (!$user->isFollowed()) {
                $suggested_users[] = $user;
            }
        }

        return $suggested_users;
    }

    public function search(Request $request)
    {
        $users = $this->user->where('name', 'like', '%' . $request->search . '%')->get();
        return view('users.search')->with('users', $users)->with('search', $request->search);
    }
}
