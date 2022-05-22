<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Resources\CommentResource as ResourcesComment;
use App\Http\Resources\ImageResource;
use App\Http\Resources\LikeResource;
use App\Http\Resources\PostResource;
use App\Image;
use App\Like;
use App\Post;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function new_post(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'title' => 'required|max:150',
            'description' => 'required',
            'price' =>'required|string|max:20',
            'location' => 'required',
            'space' => 'required',
            'bedrooms' => 'required',
            'bathrooms' => 'required',
            'garages' => 'required',
            ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'error']);
        }

        $user = Auth::user();
        $post = new Post();
        $post->type = $request->type;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->agency_id = $user->id;
        $post->agency_name = $user->agency_name;
        $post->location = $request->location;
        $post->price = $request->price;
        $post->space = $request->space;
        $post->bedrooms = $request->bedrooms;
        $post->bathrooms = $request->bathrooms;
        $post->garages = $request->garages;

        $post->save();
        $tags = $request->tag;
        $tagNames = [];
        if (!empty($tags)) {
            if (is_array($tags) || is_object($tags)) {
                foreach ($tags as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    if ($tag) {
                        $tagNames[] = $tag->id;
                    }
                }
                $post->tags()->syncWithoutDetaching($tagNames);
            }
        }
        $file = $request->file('image');
        if($request->hasFile('image')) {
            foreach ($file as $imagefile) {
                $image = new Image;
                $imageName = time() . '-' . uniqid() . '.' . $imagefile->extension();
                $imagefile->storeAs('/images/posts', $imageName, ['disk' => 'my_files']);
                $image->post_id = $post->id;
                $image->image_url = $imageName;
                $image->save();
            }
        }else{
            $image = new Image;
            $imageName = 'default.jpg';
            $image->post_id = $post->id;
            $image->image_url = $imageName;
            $image->save();
        }
        return new PostResource($post);

    }
    public function update(Request $request, $postId){


        Validator::make($request->all(), [
            'type' => 'required',
            'title' => 'required|max:150',
            'description' => 'required',
            'price' =>'required|string|max:20',
            'location' => 'required',
            'space' => 'required',
            'bedrooms' => 'required',
            'bathrooms' => 'required',
            'garages' => 'required',
        ]);
        $post = Post::where('id', $postId)->first();

        $user = Auth::user();
        $post->type = $request->type;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->agency_id = $user->id;
        $post->agency_name = $user->agency_name;
        $post->location = $request->location;
        $post->price = $request->price;
        $post->space = $request->space;
        $post->bedrooms = $request->bedrooms;
        $post->bathrooms = $request->bathrooms;
        $post->garages = $request->garages;

        $post->save();
        $tags = $request->tag;
        $tagNames = [];
        if (!empty($tags)) {
            if (is_array($tags) || is_object($tags)) {
                foreach ($tags as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    if ($tag) {
                        $tagNames[] = $tag->id;
                    }
                }
                $post->tags()->syncWithoutDetaching($tagNames);
            }
        }
        $file = $request->file('image');
        if($request->hasFile('image')) {
            foreach ($file as $imagefile) {
                $image = new Image;
                $imageName = time() . '-' . uniqid() . '.' . $imagefile->extension();
                $imagefile->storeAs('/images/posts', $imageName, ['disk' => 'my_files']);
                $image->post_id = $post->id;
                $image->image_url = $imageName;
                $image->save();
            }
        }else{
            $image = new Image;
            $imageName = 'default.jpg';
            $image->post_id = $post->id;
            $image->image_url = $imageName;
            $image->save();
        }
    }


    public function destroy($postId)
    {
        $post = Post::with('comments')->with('images')->where('id', $postId)->first();
    foreach ($post->images as $image ) {
         $image_path = public_path().'/images/posts/'.$image->image_url;
         unlink($image_path);
    }
        $post->delete();
        $post->tags()->detach();
        $post->likes()->delete();
        $post->images()->delete();
        $post->comments()->delete();

        return response()->json(['success'=>"post deleted"]);
    }

    public function post($postId)
    {
        $post = Post::with('comments')->where('id', $postId)->get();
        return PostResource::collection($post);
    }

    public function posts()
    {
        $posts = Post::with('comments')->paginate(5);
        return PostResource::collection($posts);
    }

    public function like($postId)
    {

        if(!Auth::check()) {
            return response()->json(['you need to sign in'], 401);
        }
        $post = Post::where('id', $postId)->first();
        $user = Auth::user();
        $likecheck = Like::where('post_id', $post->id)->where('user_id', $user->id)->first();

        if($likecheck==null)
        {
            $like= new Like();
            $like->user_id=$user->id;
            $like->post_id=$post->id;
            $like->save();
            return response()->json(['success'=>"post liked"]);

        }
        else
        {
            Like::where('user_id',$user->id)->where('post_id', $post->id)->first()->delete();
            return response()->json(['success'=>"post Unliked"]);
        }

    }
    public function comment(Request $request)
    {

        $request->validate([
            'comment' => 'required',
        ]);

        $user = Auth::user();

        $comment=new Comment();

        if (Post::where('id', '=', $request->post_id)->exists()) {
            $comment->user_id=$user->id;
            $comment->post_id=$request->post_id;

            $comment->comment=$request->comment;
            $comment->save();

            return response()->json(['success'=>"comment posted"]);

        }else{
            return response()->json(['failed'=>"post doesn't exist"]);

        }

    }


    public function postComments($postId)
    {

        $comments = Comment::where('post_id', $postId)->get();

        if($comments!=null) {

            return response()->json([ResourcesComment::collection($comments)]);
        }
        return response()->json(['comment'=>"there is no comment"]);
    }

    public function postImages($postId)
    {

        $images = Image::where('post_id', $postId)->get();

        if($images!=null){


            return response()->json([ImageResource::collection($images)]);
        }

        return response()->json(['image'=>"there is no image "]);
    }
    public function postLikes($postId)
    {

            $Likes = Like::where('post_id', $postId)->get();

        if($Likes!=null){


            return response()->json([LikeResource::collection($Likes)]);
        }

        return response()->json(['Likes'=>"there is no Likes "]);
    }

    public function favorite()
    {
        $user = Auth::user();
        $try = [];
        $Likes = Like::where('user_id', $user->id)->get('post_id');
        foreach ($Likes as $id){
            $try[] =$id->post_id;
        }
        if($try==null) {
            //return response()->json(['Posts' => "this user doesnt have any posts in favorite "]);
			return response()->json(['data' => "false"], 201);
        }
        $posts = Post::whereIn('id', $try)->get();

        if($posts!=null){
            return response()->json(['data' => PostResource::collection($posts)]);
        }
    }

}
