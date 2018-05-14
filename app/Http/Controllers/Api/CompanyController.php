<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use PDOException;
use Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $company = Company::paginate(25);

        return $company;
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
                'contact_person' => 'required',
                'address' => 'required',
                'country' => 'required',
                'city' => 'required',
                'email' => 'required'
            ];
            $customMessages = [
                'required' => 'The :attribute field can not be blank.',
            ];
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if($validator->fails()){
                return response()->json(['errors'=> $validator->errors()]);
            }
            $input = $request->all();
            $input['photo_id'] = 1;
            $input['created_by'] = Auth::user()->id;
            $company = Company::create($input);
            return response()->json($company, 201);
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
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $company = Company::findOrFail($id);

        return $company;
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
                'contact_person' => 'required',
                'address' => 'required',
                'country' => 'required',
                'city' => 'required',
                'email' => 'required'
            ];

            $customMessages = [
                'required' => 'The :attribute field can not be blank.',
            ];
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if($validator->fails()){
                return response()->json(['errors'=> $validator->errors()]);
            }

            $company = Company::findOrFail($id);
            $company->update($request->all());

            return response()->json($company, 200);
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
        Company::destroy($id);

        return response()->json(null, 204);
    }
}
