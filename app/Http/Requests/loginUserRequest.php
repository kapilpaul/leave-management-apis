<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class loginUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'email.required'  => 'Please Enter an Email address',
            'email.email'  => 'Please Enter a valid Email address',
            'password.required'  => 'Please Enter a password',
            'password.min'  => 'Password must be 6 Characters',
        ];
    }
}
