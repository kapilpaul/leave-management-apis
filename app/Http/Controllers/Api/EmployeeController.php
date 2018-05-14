<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Employee;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use PDOException;
use Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $employee = Employee::paginate(25);

        return $employee;
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
            
//            $rules = [
//                'name' => 'required'
//            ];
//            $customMessages = [
//                'required' => 'The :attribute field can not be blank.',
//            ];
//            $validator = Validator::make($request->all(), $rules, $customMessages);
//            if($validator->fails()){
//                return response()->json(['errors'=> $validator->errors()]);
//            }

            $employee = Employee::create($request->all());

            return response()->json($employee, 201);
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
        $employee = Employee::findOrFail($id);

        return $employee;
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
            
//            $rules = [
//                'name' => 'required'
//            ];
//            $customMessages = [
//                'required' => 'The :attribute field can not be blank.',
//            ];
//            $validator = Validator::make($request->all(), $rules, $customMessages);
//            if($validator->fails()){
//                return response()->json(['errors'=> $validator->errors()]);
//            }

            $employee = Employee::findOrFail($id);
            $employee->update($request->all());

            return response()->json($employee, 200);
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
        Employee::destroy($id);

        return response()->json(null, 204);
    }
}
