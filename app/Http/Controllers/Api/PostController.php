<?php

namespace App\Http\Controllers\Api;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
       $post = Post::get()->toJson(JSON_PRETTY_PRINT);
        return response($post, 200); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Post::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $post->update($request->all());
        return $post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $post = Post::destroy($id);
       return response()->json([
          "message" => "Post record Deleted"
        ], 200);
    } 

    public function create(Request $request) {
       // dd($request->all());//exit;
        $post = new Post;
        $post->title = $request->title;
        $post->slug = $request->slug;
        $post->likes = $request->likes;
        $post->description = $request->description;
        $post->save();
  
        return response()->json([
          "message" => "Post record created"
        ], 201);
      }
}
