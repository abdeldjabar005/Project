<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Resources\CommentResource as ResourcesComment;
use App\Http\Resources\ImageResource;
use App\Http\Resources\LikeResource;
use App\Http\Resources\LikeResource as ResourcesLike;
use App\Http\Resources\PostResource;
use App\Image;
use App\Like;
use App\Post;
use App\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function new_post(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:150',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'error']);
        }

        $user = Auth::user();
        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        $post->agency_id = $user->id;
        $post->agency_name = $user->agency_name;

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
//        $images = $request->file('image');
//        $temp1 = [];
//
//        foreach ($images as $getImage) {
//        if (!$request->hasFile('image')) {
//            return response()->json(['upload_file_not_found'], 400);
//        }
////        $file = $request->file('image');
////        if (!$file->isValid()) {
////            return response()->json(['invalid_file_upload'], 400);
////        }
////            return response()->json(['sw']);
//            $temp1[] = 1;
//            print_r($temp1);
//
//            $image = new Image;
////        $getImage = $request->image;
//        $imageName = time() . '.' . $getImage->extension();
//        $imagePath = public_path() . '/images';
//        $image->post_id = $post->id;
//        $image->image_url = $imageName;
//
//        $getImage->move($imagePath, $imageName);
//
//        $image->save();
//    }

        foreach ($request->file('image') as $imagefile) {
            $image = new Image;
            $imageName = time() .'-'.uniqid(). '.' . $imagefile->extension();
            $imagefile->storeAs('/images/posts',$imageName, ['disk' =>   'my_files']);
            $image->post_id = $post->id;
            $image->image_url = $imageName;
            $image->save();
        }
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


    public function post($postId)
    {
        $post = Post::with('comments')->where('id', $postId)->get();
        return response()->json([PostResource::collection($post)]);
    }

    public function posts()
    {
        $posts = Post::with('comments')->get();
        return response()->json([PostResource::collection($posts)]);
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

}
