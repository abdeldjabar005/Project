<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Post;
use Illuminate\Http\Request;

class searchController extends Controller
{

    public function search(Request $request) {
        $data = $request->get('data');

        $search = Post::
            where('title', 'like', "%{$data}%")
            ->orWhere('agency_id', 'like', "%{$data}%")
            ->orWhere('agency_name', 'like', "%{$data}%")
            ->orWhere('id', 'like', "%{$data}%")
            ->orWhereHas('tags', function ($query) use ($data) {
                return $query->where('name', '=', "{$data}");
            })
            ->paginate(10);

        return response()->json([
            'data' => PostResource::collection($search)
        ]);
    }
}
