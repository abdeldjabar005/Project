<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function createTag(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags|max:255'
        ]);

        $tag = new Tag();
            $tag->name = $request->name;
            $tag->save();
            return response()->json(['tag'=>'Tag added succesfully']);

            //        return response()->json(['tag'=>"Tag already exists"]);

    }
}
