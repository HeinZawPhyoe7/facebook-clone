<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();

        return response()->json([
            'posts' => $posts,
            'message' => 'success'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function createPost(Request $request)
    {
        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        if ($request->hasFile('images')) {
            $newimage = $request->file('images')->store('images', 'public');
            $imagedata = Storage::disk('public')->get($newimage);
            $base64 = base64_encode($imagedata);
        }
        $post->images = json_encode($base64);
        $post->save();
        return  response()->json([
            'message' => 'success',
            'title' => $post->title,
            'desc' => $post->description,
            'dbimg' => json_encode([$base64]),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function updatePost(Request $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found!'
            ], 404);
        }

        $post->title = $request->title;
        $post->description = $request->description;

        if ($request->hasFile('images')) {
            $base64Images = []; // Array to store base64-encoded images

            // If the request contains a single file instead of multiple
            $files = is_array($request->file('images')) ? $request->file('images') : [$request->file('images')];

            foreach ($files as $image) {
                $imagePath = $image->store('images', 'public');
                $imageData = Storage::disk('public')->get($imagePath);
                $base64Images[] = base64_encode($imageData);
            }

            $post->images = json_encode($base64Images);
        }

        $post->save();

        return response()->json([
            'message' => 'Post updated successfully!',
            'title' => $post->title,
            'description' => $post->description,
            'images' => json_decode($post->images) ?? [],
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if($post){
            $post->delete();
        }
        return response()->json([
            'message' => 'post is deleted'
        ]);
    }
}
