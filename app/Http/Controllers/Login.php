<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Auth\Guard;


class Login extends Controller
{
    public function Do_login()
    {
    	$username = Input::post('username');
    	$password = Input::post('password');
		if (Auth::attempt(['username' => $username, 'password' => $password])) {
                $users_data = DB::table('users')->select('*')->where('username', $username)->get();
				Session::put('Login', $users_data);
                date_default_timezone_set("Asia/Bangkok");
                $today = now();                
                DB::table('users')
                ->where('username', $username)
                ->update(['updated_at'  => $today]);
		        return redirect('Dashboard');
		}
		elseif (Auth::attempt(['email'=> $username, 'password' => $password])) {
				$users_data = DB::table('users')->select('*')->where('username', $username)->get();
				Session::put('Login', $users_data);
                date_default_timezone_set("Asia/Bangkok");
                $today = now();                
                DB::table('users')
                ->where('username', $username)
                ->update(['updated_at'  => $today]);
		        return redirect('Dashboard');
		} 
		else {
				return back()->withInput();
		}
    }

    public function Dashboard()
    {
        $create_link = DB::select("select * from create_link");
    	return view('dashboard',[
            'create_link' => $create_link,
            ]);
    }

    public function User()
    {
        $user = DB::select("select * from users");
        return view('user',[
            'user' => $user]);
    }

    public function Do_add_user()
    {
        date_default_timezone_set("Asia/Bangkok");
        $today    = now();          
        $name     = Input::post('inputname');
        $username = Input::post('inputusername');
        $password = Input::post('inputpassword');
        $email    = Input::post('inputpemail');
        $type     = Input::post('Type');
        $token    = Input::post('_token');
        $repassword = bcrypt($password);
        DB::table('users')->insert(
            [
            'name' => $name, 
            'email' => $email,
            'username' => $username,
            'password' => $repassword,
            'status' => $type,
            'remember_token' => $token,
            'created_at' => $today,
            'updated_at' => $today
        ]
        ); 
        return redirect('User'); 
    }

    public function Create_Link()
    {
        $room = DB::select("select * from room");
        return view('create_link',[
            'room' => $room]);
    }

    public function Do_create_link()
    {
        $groupname = Input::post('groupname');
        $numberprople = Input::post('numberprople');
        $stayin = Input::post('stayin');
        $room = Input::post('room');
        $restayin = str_replace('/', '-', $stayin);
        $resultstayin  = date('Y-m-d', strtotime($restayin));
        $stayto = Input::post('stayto');
        $restayto = str_replace('/', '-', $stayto);
        $resultstayto  = date('Y-m-d', strtotime($restayto));
        $today = date("Y-m-d");
        DB::table('create_link')->insert(
            [
            'link_create' => $today, 
            'link_room' => $room,
            'link_group_name' => $groupname,
            'link_gust_in' => $numberprople,
            'link_staying_from' => $resultstayin,
            'link_staying_to' => $resultstayto,
            'link_encode' => $groupname,
            'link_en' => '/en'.'/'.$groupname,
            'link_th' => '/th'.'/'.$groupname
        ]
        );  
        return redirect('Dashboard');      
    }

    public function Logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('Admin');
    }
}
