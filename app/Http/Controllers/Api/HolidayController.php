<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Holiday;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use PDOException;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $holiday = Holiday::whereStatus('active')->whereYear('date', '=', date('Y'))->orderBy('date', 'asc')->select('id', 'name', 'date')->paginate
        (25);

        return $holiday;
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
                'date' => 'required'
            ];
            $customMessages = [
                'required' => 'The :attribute field can not be blank.',
            ];
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if($validator->fails()){
                return response()->json(['errors'=> $validator->errors()], 500);
            }

            $input = $request->all();
            $input['date'] = date('Y-m-d', strtotime($request->date));
            $input['created_by'] = $input['updated_by'] = Auth::user()->id;
            $holiday = Holiday::create($input);

            return response()->json($holiday, 201);
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
        $holiday = Holiday::findOrFail($id);

        return $holiday;
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
                'date' => 'required'
            ];
            $customMessages = [
                'required' => 'The :attribute field can not be blank.',
            ];
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if($validator->fails()){
                return response()->json(['errors'=> $validator->errors()]);
            }

            $holiday = Holiday::findOrFail($id);
            $holiday->update($request->all());

            return response()->json($holiday, 200);
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
        Holiday::destroy($id);

        return response()->json(null, 204);
    }
}
