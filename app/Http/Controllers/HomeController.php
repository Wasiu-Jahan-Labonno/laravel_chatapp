<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request,$id=null)
    {   $otherUser= null;
        $messages = [];
        $user_id = Auth::id();
        //dd($user_id);       
        if($id){
            $otherUser = User::findOrFail($id);
            $group_id = (Auth::id()>$id)?Auth::id().$id:$id.Auth::id();
            $set_read = chat::where(['user_id'=>$id, 'other_user_id'=>$user_id, 'is_read'=>0])->update(['is_read'=>1]);
            $messages = chat::where('group_id',$group_id)->get()->toArray();
            
           
        }
        $friends = User::where('id','!=',Auth::id())->select('*', DB::raw("(SELECT count(id) from chats where chats.other_user_id=$user_id and chats.user_id=users.id and is_read=0) as unread_message"))->get()->toArray();
        //dd($friends);
        return view('home',compact('friends','messages','otherUser','id'));
    }
}

