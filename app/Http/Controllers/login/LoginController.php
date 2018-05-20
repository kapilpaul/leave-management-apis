<?php

namespace App\Http\Controllers\Login;

use App\User;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use GuzzleHttp\Client;
use League\Flysystem\Config;
use League\Flysystem\Exception;
use Sentinel;
use App\Http\Requests\loginUserRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
//    public function login(){
////        $loginUrl = $this->fbLogin();
//        return view('auth.login');
//    }

    public function postLogin(loginUserRequest $request){
        try{
            //check default roles and admin
            $this->defaultRoleAdmin();

            $remember_me = false;
            if(isset($request->remember_me))
                $remember_me = true;

            if(Sentinel::authenticate($request->all(), $remember_me)){
                if(Sentinel::inRole('admin') || Sentinel::inRole('employee')){
                    if(Sentinel::inRole('admin')){
                        $role = md5('admin');
                    }
                    if(Sentinel::inRole('employee')){
                        $role = md5('employee');
                    }

                    return response()->json(['role' => $role], 200);
                } else {
                    return response()->json(['error' => 'Error in login'], 500);
                }
            }else{
                return response()->json(['error' => 'Wrong Credentials'], 401);
            }
        }catch(ThrottlingException $e){
            $delay = $e->getDelay();
            return response()->json(['error' => "You are banned for $delay seconds"]);
        }catch(NotActivatedException $e){
            return response()->json(['error' => "Your account is not activated yet."]);
        }
    }

    public function logout(){
        Sentinel::logout();
        return response()->json(['success' => 'Logged Out'], 200);
    }

    public function defaultRoleAdmin() {
        $roles = Sentinel::getRoleRepository()->get();

        if(count($roles) == 0){
            $input['slug'] = 'admin';
            $input['name'] = 'Admin';

            Sentinel::getRoleRepository()->createModel()->create($input);

            $input['slug'] = 'employee';
            $input['name'] = 'Employee';
            Sentinel::getRoleRepository()->createModel()->create($input);
        }

        $kapil = User::whereEmail("info@kapilpaul.me")->first();

        if(!$kapil){
            $kapilData['first_name'] = 'Kapil';
            $kapilData['last_name'] = 'Paul';
            $kapilData['user_name'] = 'kapilpaul';
            $kapilData['email'] = 'info@kapilpaul.me';
            $kapilData['password'] = 'nothing1234';

            $role = Sentinel::findRoleBySlug('admin');
            $user = Sentinel::registerAndActivate($kapilData);
            $role->users()->attach($user);
        }

        return true;
    }

}
