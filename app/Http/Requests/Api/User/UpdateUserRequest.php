<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:250'],
            'surname' =>  ['required', 'string', 'max:250'],
            'phone_number' =>  ['required', 'string', Rule::unique('users', 'phone_number')->ignore($this->request->user)],
            'address' => 'nullable|string',
            'email' => 'nullable|email'
        ];
    }
}
