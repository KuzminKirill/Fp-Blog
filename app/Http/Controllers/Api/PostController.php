<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request): JsonResponse
    {
        $posts = Post::with('tags')
            ->when($request->tag, function ($query, $tag) {
                return $query->whereHas('tags', fn($q) => $q->where('name', $tag));
            })
            ->latest()
            ->get();
        return response()->json(
            [
                'message' => __('messages.post_showed'),
                'posts' => $posts
            ]
        );
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'array|nullable',
            'tags.*' => 'string|max:255',
        ]);

        $post = $request->user()->posts()->create($request->only('title', 'content'));
        if ($request->tags) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->attach($tagIds);
        }

        return response()->json([
            'message' => __('messages.post_created'),
            'post' => $post->load('tags')
        ], 201);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'array|nullable',
            'tags.*' => 'string|max:255',
        ]);

        $post->update($request->only('title', 'content'));
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return response()->json([
            'message' => __('messages.post_updated'),
            'post' => $post->load('tags')
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);
        $post->delete();
        return response()->json(['message' => __('messages.post_deleted')]);
    }

    public function tags(): JsonResponse
    {
        return response()->json(Tag::all());
    }
}
