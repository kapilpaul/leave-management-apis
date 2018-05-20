<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Employee;
use App\User;
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
                    ->select('users.id', 'users.first_name', 'users.last_name', 'users.user_name', 'users.email', 'designations.name as designation', 'departments.name as department')
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
        $employee = DB::table('users as usr1')
                      ->join('employees', 'usr1.id', '=', 'employees.user_id')
                      ->join('companies', 'employees.company_id', '=', 'companies.id')
                      ->join('designations', 'employees.designation_id', '=', 'designations.id')
                      ->join('departments', 'designations.department_id', '=', 'departments.id')
                      ->leftJoin('users as usr2', 'usr2.id', '=', 'employees.supervisior_id')
                      ->where('usr1.user_name', $id)
                      ->select(
                          'usr1.id as userid',
                          'usr1.first_name',
                          'usr1.last_name',
                          'usr1.user_name',
                          'usr1.email',
                          'employees.blood_group',
                          'employees.contact',
                          'employees.current_address',
                          'employees.date_of_birth',
                          'employees.driving_licence',
                          'employees.education',
                          'employees.emergency_contact_name',
                          'employees.emergency_contact_number',
                          'employees.employee_number',
                          'employees.experience',
                          'employees.fathers_name',
                          'employees.fathers_number',
                          'employees.joining_date',
                          'employees.leaving_date',
                          'employees.linkedin',
                          'employees.mothers_name',
                          'employees.mothers_number',
                          'employees.nid',
                          'employees.official_number',
                          'employees.passport',
                          'employees.permanent_address',
                          'employees.photo_id',
                          'employees.relation_emergency_contact',
                          'employees.skills',
                          'employees.spouse_name',
                          'employees.spouse_number',
                          'designations.name as designation',
                          'designations.id as designation_id',
                          'departments.name as department',
                          'companies.name as company',
                          'companies.id as company_id',
                          'usr2.first_name as supervisior_first_name',
                          'usr2.last_name as supervisior_last_name',
                          'usr2.id as supervisior_id'
                      )->first();

//        $employee = Employee::whereUserId($id)
//                    ->select('id', 'user_id', 'employee_number', 'joining_date', 'company_id', 'supervisior_id', 'designation_id', 'blood_group', 'linkedin', 'contact', 'official_number', 'fathers_name', 'fathers_number', 'mothers_name', 'mothers_number', 'spouse_name', 'spouse_number', 'current_address', 'permanent_address', 'nid', 'passport', 'driving_licence', 'date_of_birth', 'emergency_contact_name', 'emergency_contact_number', 'relation_emergency_contact', 'skills', 'education', 'experience', 'leaving_date')->with([
//            'user' => function ($query) {
//                $query->select('id', 'first_name', 'last_name', 'email', 'user_name');
//            },
//            'supervisior' => function ($query) {
//                $query->select('id', 'first_name', 'last_name');
//            }
//        ])->first();
        if(! $employee) {
            return response()->json(['error' => 'No employee found'], 500);
        }

        return response()->json($employee, 201);
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
//        return $request->all();
        try{
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
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
                return response()->json(['errors'=> $validator->errors()]);
            }

            $user = User::whereUserName($id)->first();

            if(! $user) {
                return response()->json(['error' => 'Error In server'], 500);
            }
            $input = $request->only(['first_name', 'last_name', 'email']);
            $user->update($input);

            $employeeInput = $request->except(['first_name', 'last_name', 'user_name', 'email', 'supervisior_first_name', 'supervisior_last_name', 'userid']);
            $employeeInput['date_of_birth'] = date('Y-m-d', strtotime($request->date_of_birth));
            $employeeInput['joining_date'] = date('Y-m-d', strtotime($request->joining_date));
            $employeeInput['education'] = null;

            if(isset($request->education)) {
                if(count($request->education) > 0) {
                    $employeeInput['education'] = json_encode($request->education);
                }
            }

            $employeeInput['updated_by'] = Auth::user()->id;

            if((isset($request->supervisior_id)) && ($request->supervisior_id == -1)) {
                $employeeInput['supervisior_id'] = null;
            }

            $employee = Employee::where('user_id', $user->id)->first();
            $employee->update($employeeInput);

            return response()->json(['success' => 'Updated successfully'], 200);
        } catch(PDOException $e){
            return $e->getMessage();
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
