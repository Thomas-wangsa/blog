<?php

namespace App\Http\Controllers;

// namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Routing\UrlGenerator;
use App\User;
use Illuminate\Cookie\CookieJar;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->middleware('auth');
        $this->url = $url;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CookieJar $cookieJar)
    {   
        $cookie_name = "cookies_id";
        $cookie_value = Auth::user()->id;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
        // dd($cookieJar);die;
        $url = $this->url->to('/');
        date_default_timezone_set("Asia/Jakarta");
        $insert_data = array(
            'id'=>date('Y-m-d H:i:s')."_".Auth::user()->id,
            'user_id'=>Auth::user()->id,
            'ip_address'=>$_SERVER['REMOTE_ADDR'],
            'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
            'payload'=> "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
            'last_activity'=>strtotime(date('Y-m-d H:i:s'))
            );

        DB::table('sessions')->insert($insert_data);
        session(['user_id'=>Auth::user()->id,'user_name'=>Auth::user()->name]);
        $user_id = session('user_id');
        $user_name = session('user_name');

        // $data = null;
        $data = DB::table('users')
                    ->select('users.id','users.name','email','remember_token',DB::raw('(select last_activity FROM sessions WHERE sessions.user_id =  users.id ORDER BY last_activity DESC LIMIT 1) as last_activity'))
                    ->where('users.id', '!=', Auth::user()->id)
                    ->orderBy('id','ASC')
                    ->get();

        // $data = json_decode(json_encode($data), true);
        // dd($data);
        return view('home', compact('user_id','user_name','data','url'));
    }

    public function deleteuser(Request $request) {
        $id             = $request->input('id');
        $delete = DB::table('users')->where('id', '=', $id)->limit(1)->delete();
        if($delete) { 
            return response()->json(['response' => '1', 'data'=>'delete sukses']);
        } else {
            return response()->json(['response' => '0', 'data'=> 'delete gagal']);
        }
    }

    public function adduser(Request $request) {
        if ($request->isMethod('post')){
            $type = $request->input('type');
            if($type == "adduser") {
                $url =  $request->input('url');

                $name           = $request->input('name');
                $email          = $request->input('email');
                $password       = $request->input('password');
                $password_2     = $request->input('password_confirmation');

                if($password !== $password_2) {
                    echo "<script> alert('Password tidak sesuai') </script>";
                    echo "<script> var actual_link = '".$url."'; window.location = actual_link;   </script>";
                }

                $cek_email = DB::table('users')
                            ->select('id')
                            ->where('email', $email)
                            ->first();
                $cek_email = json_decode(json_encode($cek_email), true);
  
                if($cek_email['id'] !== null) {
                    echo "<script> alert('Email sudah terdaftar') </script>";
                    echo "<script> var actual_link = '".$url."'; window.location = actual_link;   </script>";
                }

                $create = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt($password),
                ]);
                if($create) { 
                    echo "<script> alert('Insert Sukses') </script>";
                } else {
                    echo "<script> alert('Insert Failed') </script>";
                }
                echo "<script> var actual_link = '".$url."'; window.location = actual_link;   </script>";
            } else {
                echo "<script> alert('Method Get not Allowed') </script>";
                echo "<script> var actual_link = '".$url."'; window.location = actual_link;   </script>";    
            }       
        } 
    }

    public function edituser(Request $request) {
        if ($request->isMethod('post')){
            $type = $request->input('type');
            if($type == "edituser") {
                $url            =  $request->input('url');
                $id             = $request->input('id');
                $name           = $request->input('name');
                $email          = $request->input('email');

                // $cek_email = DB::table('users')
                //             ->select('id','email')
                //             ->where('email', $email)
                //             ->first();
                // $cek_email = json_decode(json_encode($cek_email), true);
  
                // if($cek_email['id'] !== null) {
                //     echo "<script> alert('Email sudah terdaftar') </script>";
                //     echo "<script> var actual_link = '".$url."'; window.location = actual_link;   </script>";
                // }

                $update = DB::table('users')
                        ->where('id', $id)
                        ->limit(1)
                        ->update(['name' =>$name,'email'=>$email]);
                if($update) { 
                    echo "<script> alert('Update Sukses') </script>";
                } else {
                    echo "<script> alert('Update Failed') </script>";
                }
                echo "<script> var actual_link = '".$url."'; window.location = actual_link;   </script>";
            } else {
                echo "<script> alert('Method Get not Allowed') </script>";
                echo "<script> var actual_link = '".$url."'; window.location = actual_link;   </script>";    
            }       
        } 
    }
}
