<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrolledStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        return [
        'id_number'    => 'sometimes|required|string|max:8|unique:enrolled_students,id_number',
        'last_name'    => 'required|string|max:100',
        'first_name'   => 'required|string|max:100',
        'middle_name'  => 'nullable|string|max:100',
        'birth_day'    => 'nullable|date',
        'course'       => 'required|string|max:255',
        'gender'       => 'required|string|in:Male,Female,Other',
    ];
    }
}
