<?php

namespace App\Http\Controllers;


use App\Http\Resources\AgencyResource;
use App\Http\Resources\Agency;
use App\Http\Resources\UserResource;
use App\Image;
use App\Like;
use App\User;
use Carbon\Carbon;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registerAgency (Request $request){
        $validator = Validator::make($request->all(),[
            'agencyName'      => 'required|string|max:255',
            'registrationNumber'      => 'required|string|max:255|unique:users',
            'email'     => 'required|string|email|max:255|unique:users',
            'phone' =>'required|string|max:20|unique:users',
            'bio' =>'string|max:300',
            'password'  => 'required|string|max:255|min:6|confirmed',
        ]);
         if ($validator->fails()){
            return response(['errors' => $validator->errors()], 422);
//             return response()->json(['error'=>$validator->errors()], 401);

         }
         $user = new User();

         $user->role_id = 1;
         $user->agency_name = $request->agencyName;
         $user->registrationNumber = $request->registrationNumber;
         $user->email = $request->email;
         $user->phone= $request->phone;
         $user->password = bcrypt($request->password);
        $file = $request->file('image');
        if($request->hasFile('image')) {
            $getImage = $request->image;
            $imageName = time() .'-'.uniqid(). '.' . $file->extension();
            $file->storeAs('/images/users',$imageName, ['disk' =>   'my_files']);
            $user->profile_picture = $imageName;
        }
        $user->save();
        return $this->getAgencyResponses($user);
    }

    public function registerClient (Request $request){
        $validator = Validator::make($request->all(),[
            'firstName'      => 'required|string|max:255',
            'lastName'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'phone' =>'required|string|max:20|unique:users',
            'bio' =>'string|max:300',
            'password'  => 'required|string|max:255|min:6|confirmed',
        ]);
        if ($validator->fails()){
            return response(['errors' => $validator->errors()], 422);
        }
        $user = new User();
        $user->role_id = 2;
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $file = $request->file('image');
        if($request->hasFile('image')) {
            $getImage = $request->image;
            $imageName = time() .'-'.uniqid(). '.' . $file->extension();
            $file->storeAs('/images/users',$imageName, ['disk' =>   'my_files']);
            $user->profile_picture = $imageName;
        }
//        if(!$file->isValid()) {
//            return response()->json(['invalid_file_upload'], 400);
//        }

        $user->save();

        return $this->getClientResponses($user);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|max:255'
        ]);


        $credentials = \request(['email', 'password']);

        if(Auth::attempt($credentials)){
            $user = $request->user();
            if ($user->role_id == 1){
            return  $this->getAgencyResponses($user);
            }else{
                return  $this->getClientResponses($user);
            }
            }

        return response(['errors' => $validator->errors()], 422);

    }


    public function updatebio(Request $request){


        $request->validate([
            'bio' => 'required',
        ]);
        $user = Auth::user();
        $user->bio = $request->bio;
        $user->save();
        return response('Bio updated successfully', 200);
    }
    public function profilepic(Request $request){
        $user = Auth::user();
        $file = $request->file('image');
        if($request->hasFile('image')) {
            $getImage = $request->image;
            $imageName = time() .'-'.uniqid(). '.' . $file->extension();
            $file->storeAs('/images/users',$imageName, ['disk' =>   'my_files']);
            $user->profile_picture = $imageName;
            $user->save();
            if ($request->user()->role_id == 1){
                return new AgencyResource($request->user());
            } else{
                return new UserResource($request->user());
            }
        }else {
            return response()->json(['upload pic'], 400);
        }
    }

    public function logout(Request $request){
         $request->user()->token()->revoke();
         return response('lougged out successfully', 200);
    }
    public function user(Request $request){

        if ($request->user()->role_id == 1){
            return new AgencyResource($request->user());

        } else{
            return new UserResource($request->user());
        }
    }
    public function userfinder($id){
        $user = User::where('id', $id)->first();

        if ($user->role_id == 1){
            return new AgencyResource($user);
        } else{
            return new UserResource($user);
        }
    }


    private function getAgencyResponses(User $user){

        $tokenResult =   $user->createToken("Personal Access Token");
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();


        return  response([
            'accessToken' => $tokenResult->accessToken,
            'tokenType' => "Bearer",
            'expiresAt' => Carbon::parse($token->expires_at)->toDateTimeString(),
            'user' => new AgencyResource($user)
        ],200);

    }
    private function getClientResponses(User $user){

        $tokenResult =   $user->createToken("Personal Access Token");
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();


        return  response([
            'accessToken' => $tokenResult->accessToken,
            'tokenType' => "Bearer",
            'expiresAt' => Carbon::parse($token->expires_at)->toDateTimeString(),
            'user' => new UserResource($user)
        ],200);
    }

}
