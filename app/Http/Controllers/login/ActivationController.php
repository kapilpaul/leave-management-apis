<?php

namespace App\Http\Controllers\Login;

use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\DB;
use PDOException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ActivationController extends Controller
{
    public function activateUser($email, $activationCode){

        $user = User::whereEmail($email)->first();

        //$sentinelUser = Sentinel::findById($user->id);

        if(Activation::complete($user, $activationCode)){
            return redirect('/login')->with(['success' => 'Activation Successful']);
        }else{
            return redirect('/login')->with(['error' => 'Invalid Activation Key']);
        }

    }


    public function accessKey(){
        return view('auth.licence');
    }

    public function postaccessKey(Request $request){
        try{

            $this->validate($request, [
                'key' => 'required|min:50',
            ]);

            $key = str_split($request->key);

            $duration = $key[8].$key[9];

            if(! is_numeric($duration)){
                return redirect()->back()->with(['error' => "Invalid Key"]);
            }
            $dt = Carbon::parse(Carbon::now()->year.'-'.Carbon::now()->month.'-01');

            $duration = Carbon::parse($dt->addMonths($duration))->timestamp;

            $newkey = substr_replace($request->key, '', 8, 2);

            $data = DB::table('auth')->whereAuth($newkey)->whereValidity(1)->first();


            if($data){
                DB::table('authact')->insert(['auth' => md5(md5($data->auth)), 'valid' => $duration, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                DB::table('auth')->where('id', $data->id)->update(['validity' => 2, 'updated_at' => Carbon::now()]);

                return redirect('/index');
            }else{
                return redirect()->back()->with(['error' => "Invalid Key"]);
            }

        }catch(PDOException $e){
            return redirect()->back()->with(['error' => "Something Went Wrong"]);
        }catch(QueryException $e){
            return redirect()->back()->with(['error' => "Something Went Wrong"]);
        } catch (TokenMismatchException $e){
            return redirect('/access-key');
        }  catch (MethodNotAllowedHttpException $e){
            return redirect('/access-key');
        }
    }
}
