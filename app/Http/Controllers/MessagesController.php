<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\PostResource;
use App\Message;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{

    public function sendmessage(Request $request,$id)
    {
        $user2 = User::where('id', '=', $id)->first();
        if ($user2 === null){
            return response()->json(['error' => "this user doesnt exist"]);
        }
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'error']);
        }

        $user = Auth::user();
        if ($id == $user->id) {
            return response()->json(['error' => "you cant send message to your self "]);
        }
        $conversation = Conversation::with( ['messages' => function($c){
            $c->latest()->limit(5)->orderBy('created_at', 'DESC')->get() ;
        } ])->whereIn('sender_id', [$user->id, $id])
            ->whereIn('receiver_id', [$id, $user->id])
            ->first();
        if ($conversation == NULL) {
            $newConversation = new Conversation();
            $newConversation->sender_id = $user->id;
            $newConversation->receiver_id = $id;
            $newConversation->save();
        }
        $message = new Message();
        $result = $conversation == NULL ? $newConversation : $conversation;

        $message->message = $request->message;
        $message->user_id = $user->id;
        $message->conversation_id = $result->id;
        $message->save();
//        $messages = $result->messages->orderBy('created_at', 'DESC')
//            ->paginate(10);
//        $result->messages = $messages;

//        return $result;
//        return response()->json(['success' => "message sent"]);
//        $conversation2 = Conversation::with('messages')->first();
//return $conversation2;
        return new ConversationResource($result);

    }

    public function getconversation($id){
        $user2 = User::where('id', '=', $id)->first();
        if ($user2 === null){
            return response()->json(['error' => "this user doesnt exist"]);
        }
        $user = Auth::user();
        if ($id == $user->id) {
            return response()->json(['error' => "there are no conversations to your self "]);
        }

        $conversation = Conversation::with( ['messages' => function($c){
            $c->latest()->limit(5)->orderBy('created_at', 'DESC')->get() ;
        } ])->whereIn('sender_id', [$user->id, $id])
            ->whereIn('receiver_id', [$id, $user->id])
            ->first();
//        $messages = $conversation->messages->orderBy('created_at', 'DESC')
//            ->paginate(10);
//        $conversation->messages = $messages;
        if ($conversation == NULL) {
            return response()->json(['error' => "there is no conversation yet with this person, send a message now "]);
        }

//return $conversation;
        return new ConversationResource($conversation);

    }

}
