<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Http;


class PostController extends Controller
{
    private $post;
    private $category;

    public function __construct(Post $post, Category $category)
    {
        $this->post = $post;
        $this->category = $category;
    }

    public function create()
    {
        $all_categories = $this->category->all();
        return view('users.posts.create')->with('all_categories', $all_categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|array|between:1,3',
            'description' => 'required|min:1|max:1000',
            'images' => 'required',
            'images.*' => 'mimes:jpeg,jpg,png,gif|max:1048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:255',
        ]);

        if (count($request->file('images')) > 4) {
            return back()->withErrors(['images' => 'You can upload up to 4 images only.'])->withInput();
        }

        $images = [];
        foreach ($request->file('images') as $image) {
            $base64 = 'data:image/' . $image->extension() . ';base64,' . base64_encode(file_get_contents($image));
            $images[] = $base64;
        }

        $this->post->user_id = Auth::user()->id;
        $this->post->image = count($images) === 1 ? $images[0] : json_encode($images); // String if single image, JSON if multiple
        $this->post->description = $request->description;

        // add location
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $this->post->latitude = $request->latitude;
            $this->post->longitude = $request->longitude;
            $this->post->location_name = $request->location_name;
        }

        $this->post->save();

        foreach ($request->category as $category_id) {
            $category_post[] = ['category_id' => $category_id];
        }
        $this->post->categoryPost()->createMany($category_post);

        return redirect()->route('index');
    }

    public function show($id)
    {

        $post = $this->post->findOrFail($id);

        $liked_users = Post::with('likedUsers')->findOrFail($id);

        return view('users.posts.show')
            ->with('post', $post)
            ->with('likes_users', $liked_users);
    }

    public function edit($id)
    {
        $post = $this->post->findOrFail($id);

        #If the Auth user is NOT the owner of the post, redirect to homepage.
        if (Auth::user()->id != $post->user->id) {
            return redirect()->route('index');
        }

        $all_categories = $this->category->all();

        #Get all the category IDs of this post. Save in an array.
        $selected_categories = [];
        foreach ($post->categoryPost as $category_post) {
            $selected_categories[] = $category_post->category_id;
        }

        return view('users.posts.edit')
            ->with('post', $post)
            ->with('all_categories', $all_categories)
            ->with('selected_categories', $selected_categories);
    }

    public function update(Request $request, $id)
    {
        #1. Validate the data from the form
        $request->validate([
            'category' => 'required|array|between:1,3',
            'description' => 'required|min:1|max:1000',
            'image' => 'mimes:jpg,png,jpeg,gif|max:1048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:255',
        ]);

        #2. Update the post
        $post = $this->post->findOrFail($id);
        $post->description = $request->description;

        // add location
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $post->latitude = $request->latitude;
            $post->longitude = $request->longitude;
            $post->location_name = $request->location_name;
        }

        //If there is a new image...
        if ($request->image) {
            $post->image = 'data:image/' . $request->image->extension() . ';base64,' . base64_encode(file_get_contents($request->image));
        }

        $post->save();

        #3. Delete all records from category_post related to this post
        $post->categoryPost()->delete();
        //Use the relationship Post::categoryPost() to select the records  related to a post

        #4. Save the new categories to category_post table
        foreach ($request->category as $category_id) {
            $category_post[] = ['category_id' => $category_id];
        }
        $post->categoryPost()->createMany($category_post);

        #5. Redirect show post page
        return redirect()->route('post.show', $id);
    }

    public function destroy($id)
    {
        $this->post->destroy($id);
        return redirect()->route('index');
    }

    public function showSearchResults(Request $request)
    {
        // 検索キーワード
        $query = $request->input('query');

        // 投稿ID（例: "1,5,12" という文字列）
        $matched_ids = $request->input('matched_ids');

        if ($matched_ids) {
            // カンマ区切りの文字列 → 配列へ変換
            $ids = explode(',', $matched_ids);

            // 投稿をID順に並べて取得
            $posts = Post::whereIn('id', $ids)->with('user')->get()
                ->sortBy(function ($post) use ($ids) {
                    return array_search($post->id, $ids);
                })->values();
        } else {
            $posts = collect(); // 空のコレクション
        }

        // ビューへ渡す
        return view('users/posts/search_results', compact('posts', 'query'));
    }

    public function searchFromAI(Request $request)
    {
        $token = env('LAOZHANG_API_TOKEN');
        $query = $request->input('query');

        if (empty($token) || empty($query)) {
            return redirect()->back()->with('error', 'Search query or API token is missing.');
        }

        $apiUrl = 'https://api.laozhang.ai/v1/chat/completions';

        // Get all posts (ID, category name, description)
        $allPosts = Post::with('categoryPost.category')->get();

        // Prepare structured data to send to AI
        $postSummaries = $allPosts->map(function ($post) {
            $categories = $post->categoryPost->pluck('category.name')->toArray();
            return [
                'id' => $post->id,
                'categories' => implode(', ', $categories),
                'description' => $post->description,
            ];
        })->toArray();

        $postDataText = collect($postSummaries)->map(function ($p) {
            return "ID: {$p['id']}\nCategory: {$p['categories']}\nDescription: {$p['description']}";
        })->implode("\n\n");

        // Prompt (provide posts and keyword, extract relevant post IDs)
        $prompt = <<<EOT
Below are user-submitted posts, each with an ID, category, and description.

{$postDataText}

Among these, find up to 5 posts that are most related to the search keyword "{$query}" and return their IDs as a comma-separated list.
Format: 1,5,9
Do not include any explanation or text. Only return the list of IDs.
EOT;

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json'
        ])->post($apiUrl, [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Only return a comma-separated list of IDs.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 30,
                    'temperature' => 0.5,
                ]);

        $result = $response->json();
        $content = trim($result['choices'][0]['message']['content'] ?? '');

        // Validate response format
        if (!preg_match('/^\d+(,\d+)*$/', $content)) {
            return redirect()->back()->with('error', 'Invalid search result from AI.');
        }

        return redirect()->route('post.searchResults', [
            'query' => $query,
            'matched_ids' => $content,
        ]);
    }   
}
