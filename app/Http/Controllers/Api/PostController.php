<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::approved()->with('user')->get();
        return PostResource::collection($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tags = $request->input('tags');
        if (is_string($tags)) {
            $tags = json_decode($tags, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Invalid JSON format for tags'], 422);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
        ]);

        $post = $request->user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $post->tags()->create(['name' => $tag]);
            }
        }

        return new PostResource($post->load('tags', 'user'));
    }



    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load('user');
        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'tags' => 'array',
            'tags.*' => 'string|max:50'
        ]);

        $post->update($validated);
        if (!empty($validated['tags'])) {
            $post->tags()->delete(); // Hapus semua tags lama
            foreach ($validated['tags'] as $tag) {
                $post->tags()->create(['name' => $tag]);
            }
        }

        return new PostResource($post->load('tags', 'user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();
        return response()->json(['message' => 'Post Deleted Successfully'], 200);
    }
}
