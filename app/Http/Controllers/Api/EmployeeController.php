<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use PDOException;
use Validator;
use Sentinel;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
//        $employee = Employee::paginate(25);
        $employee = DB::table('users')
                    ->join('employees', 'users.id', '=', 'employees.user_id')
                    ->join('designations', 'employees.designation_id', '=', 'designations.id')
                    ->join('departments', 'designations.department_id', '=', 'departments.id')
                    ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'designations.name as designation', 'departments.name as department')
                    ->paginate(25);

        return response()->json($employee, 201);

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
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'user_name' => 'required',
                'password' => 'required',
                'employee_number' => 'required',
                'joining_date' => 'required',
                'contact' => 'required',
                'company_id' => 'required',
                'designation_id' => 'required'
            ];

            $customMessages = [
                'required' => 'The :attribute field can not be blank.',
            ];

            $validator = Validator::make($request->all(), $rules, $customMessages);
            if($validator->fails()){
                return response()->json(['errors'=> $validator->errors()], 500);
            }

            $input = $request->only(['first_name', 'last_name', 'email', 'user_name', 'password']);

            $role = Sentinel::findRoleBySlug('employee');
            $user = Sentinel::registerAndActivate($input);
            $role->users()->attach($user);



            $employeeInput = $request->except(['first_name', 'last_name', 'email', 'user_name', 'password']);
            $employeeInput['joining_date'] = date('Y-m-d', strtotime($request->joining_date));
            $employeeInput['user_id'] = $user->id;
            $employeeInput['created_by'] = $employeeInput['updated_by'] = Auth::user()->id;

            if((isset($request->supervisior_id)) && ($request->supervisior_id == -1)) {
                $employeeInput['supervisior_id'] = null;
            }

            $employee = Employee::create($employeeInput);

            return response()->json(['success' => 'Employee Created Successfully'], 201);
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
