<?php

namespace App\Http\Controllers;

use App\Http\Requests\registerUserRequest;
use App\Photo;
use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Sentinel::getUserRepository()->where('email', '!=', 'info@kapilpaul.me')->with('roles')->get();
        $page_count = 0;

        if(count($users) > 10) {
            $users      = Sentinel::getUserRepository()->where('email', '!=', 'info@kapilpaul.me')->with('roles')->paginate(1);
            $page_count = $users->count();
        }

//        return $users;
        return view('users.index', compact('users', 'page_count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Sentinel::getRoleRepository()->pluck('name', 'id');
        return view('users.create.blade.php', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(registerUserRequest $request)
    {
        $input = $request->all();

        if($file = $request->file('photo_id')){

            $extension = $file->getClientOriginalExtension();

            if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $name = time() . '_' . $file->getClientOriginalName();
                $file->move('img', $name);
                $photo             = Photo::create(['photo' => $name]);
                $input['photo_id'] = $photo->id;
            } else {
                return redirect()->back()->with(['error' => "You can Only Upload Jpg, jpeg, png image"]);
            }
        }

        $role = Sentinel::findRoleById($request->roles_id);

        $user = Sentinel::registerAndActivate($input);

        $role->users()->attach($user);

        return redirect()->back()->with(['success' => "$request->name created Successfully."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findorFail($id);
        $roles = Sentinel::getRoleRepository()->pluck('name', 'id');
        $user_role = Sentinel::findById($user->id)->roles;

        return view('users.edit', compact('user', 'roles', 'user_role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(trim($request->password) == ""){
            $input = $request->except('password');
        }else{
            $input = $request->all();
        }

        if($file = $request->file('photo_id')){

            $extension = $file->getClientOriginalExtension();

            if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $name = time() . '_' . $file->getClientOriginalName();
                $file->move('img', $name);
                $photo             = Photo::create(['photo' => $name]);
                $input['photo_id'] = $photo->id;
            } else {
                return redirect()->back()->with(['error' => "You can Only Upload Jpg, jpeg, png image"]);
            }
        }



        $user = Sentinel::findById($id);
        $user_role = Sentinel::findById($user->id)->roles;
        $role = Sentinel::findRoleById($user_role);

        $role->users()->detach($user);

        $user->update($input);
        $role = Sentinel::findRoleById($request->roles_id);

        $role->users()->attach($user);

        return redirect()->back()->with(['success' => "$request->name updated Successfully."]);
    }

    /**
     * Update the user in database as inactive
     *
     */
    public function inactive($id)
    {
        $user = Sentinel::findById($id);
        Activation::remove($user);
        return redirect()->back()->with(['error' => $user->name.' is now Inactive']);
    }
    /**
     * Update the user in database as inactive
     *
     */
    public function active($id)
    {
        $user = Sentinel::findById($id);

        if(!Activation::exists($user)){
            $activation = Activation::create($user);
        }else{
            $activation = Activation::exists($user);
        }

        if(Activation::complete($user, $activation->code))
            return redirect()->back()->with(['success' => $user->name.' is now Active']);
        else
            return redirect()->back()->with(['error' => $user->name.' can not activate.']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Sentinel::findById($id);

        if($user->photo){
            $image = Photo::findOrFail($user->photo_id);
            $image->delete();
            unlink(public_path() . $user->photo->photo);
        }
        Activation::remove($user);
        $user->delete();

        return redirect()->back()->with(['success' => $user->name.' is Deleted']);

    }
}
