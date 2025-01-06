<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index($postId)
    {
        return response()->json(Tag::where('post_id', $postId)->get());
    }

    /**
     * Delete a specific tag
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }
}
