<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Story;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class ProfileController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function show($id)
    {
        $user = $this->user->findOrFail($id);
        $stories = Story::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->groupBy('user_id');

        $token = env('LAOZHANG_API_TOKEN');
        if (empty($token)) {
            return view('users.profile.show')->with('user', $user);
        }
       
    


        $categories = $user->posts->flatMap(function ($post) {
            if ($post->categoryPost && $post->categoryPost->isNotEmpty()) {
                return $post->categoryPost->pluck('category.name')->toArray();
            }
            return [];
        })->unique()->values()->toArray();

        $comments = $user->comments->pluck('body')->take(5)->toArray();

        $apiUrl = 'https://api.laozhang.ai/v1/chat/completions';

        $recommendedTourLink = null;

        if (!empty($categories)) {
            $categoryText = implode(', ', $categories);
            $commentText = implode("\n", $comments);

            $tourPrompt = <<<EOT
A user is interested in the following categories: {$categoryText}
Their recent comments include:
{$commentText}
Based on these inputs, generate one concise and creative tour search keyword they would enjoy — limit it to maximum two words.
Return only the query text. No explanations.
EOT;

            $tourResponse = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json'
            ])->post($apiUrl, [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'Respond with only the raw search query.'],
                            ['role' => 'user', 'content' => $tourPrompt]
                        ],
                        'max_tokens' => 20,
                        'temperature' => 0.8
                    ]);

            $tourResult = $tourResponse->json();
            if (isset($tourResult['choices'][0]['message']['content'])) {
                $searchQuery = trim($tourResult['choices'][0]['message']['content']);
                $recommendedTourLink = 'https://www.getyourguide.com/s/?q=' . urlencode($searchQuery);
            }
        }

        $personalitySummary = null;
        if (!empty($categories) || !empty($comments)) {
            $personalityPrompt = "User is interested in: " . implode(', ', $categories) . "\n";
            $personalityPrompt .= "Recent comments include:\n" . implode("\n", $comments) . "\n";
            $personalityPrompt .= "Summarize the user's personality in friendly English. Example: 'You are into sports and leave positive comments. Energetic!'";

            $personalityResponse = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json'
            ])->post($apiUrl, [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'Respond with one friendly English sentence.'],
                            ['role' => 'user', 'content' => $personalityPrompt]
                        ],
                        'max_tokens' => 80,
                        'temperature' => 0.7
                    ]);

            $personalityResult = $personalityResponse->json();
            $personalitySummary = $personalityResult['choices'][0]['message']['content'] ?? null;
        }

        return view('users.profile.show', [
            'user' => $user,
            'categories' => $categories,
            'comments' => $comments,
            'recommendedTourLink' => $recommendedTourLink,
            'personalitySummary' => $personalitySummary,
            'stories' => $stories
        ]);

    }

    public function edit()
    {
        $user = $this->user->findOrFail(Auth::user()->id);
        return view('users.profile.edit')->with('user', $user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|min:1|max:50',
            'email' => 'required|email|max:50|unique:users,email,' . Auth::user()->id,
            'avatar' => 'mimes:jpg,jpeg,gif,png|max:1048',
            'introduction' => 'max:100'
        ]);

        $user = $this->user->findOrFail(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->introduction = $request->introduction;

        if ($request->avatar) {
            $user->avatar = 'data:image/' . $request->avatar->extension() . ';base64,' . base64_encode(file_get_contents($request->avatar));
        }

        $user->save();

        return redirect()->route('profile.show', Auth::user()->id);
    }

    public function followers($id)
    {
        $user = $this->user->findOrFail($id);
        return view('users.profile.followers')->with('user', $user);
    }

    public function following($id)
    {
        $user = $this->user->findOrFail($id);
        return view('users.profile.following')->with('user', $user);
    }
}
