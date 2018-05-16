<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use PDOException;
use Validator;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $leavetype = LeaveType::paginate(25);

        return $leavetype;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        try{
            $rules = [
                'name' => 'required',
                'days' => 'required|numeric'
            ];
            $customMessages = [
                'required' => 'The :attribute field can not be blank.',
            ];
            $validator = Validator::make($request->all(), $rules, $customMessages);

            if($validator->fails()){
                return response()->json(['errors'=> $validator->errors()]);
            }

            $input = $request->all();
            $input['created_by'] = $input['updated_by'] = Auth::user()->id;

            $leavetype = LeaveType::create($input);

            return response()->json($leavetype, 201);
        } catch(PDOException $e){
            return response()->json(['error' => 'Something Went Wrong!'], 500);
        } catch(QueryException $e){
            return response()->json(['error' => 'Something Went Wrong!'], 500);
        } catch (MethodNotAllowedHttpException $e){
            return response()->json(['error' => 'Something Went Wrong!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $leavetype = LeaveType::findOrFail($id);

        return $leavetype;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        try{
            $rules = [
                'name' => 'required',
                'days' => 'required|numeric'
            ];
            $customMessages = [
                'required' => 'The :attribute field can not be blank.',
            ];
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if($validator->fails()){
                return response()->json(['errors'=> $validator->errors()]);
            }

            $leavetype = LeaveType::findOrFail($id);
            $leavetype->update($request->all());

            return response()->json($leavetype, 200);
        } catch(PDOException $e){
            return response()->json(['error' => 'Something Went Wrong!'], 500);
        } catch(QueryException $e){
            return response()->json(['error' => 'Something Went Wrong!'], 500);
        } catch (MethodNotAllowedHttpException $e){
            return response()->json(['error' => 'Something Went Wrong!'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        LeaveType::destroy($id);

        return response()->json(null, 204);
    }
}
