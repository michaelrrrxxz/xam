<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'test_type' => 'required|in:verbal,non-verbal',


            'question' => 'required_without:question_file|string|max:250',
            'question_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',


            'ch1' => 'required|string|max:100',
            'ch2' => 'required|string|max:100',
            'ch3' => 'required|string|max:100',
            'ch4' => 'required|string|max:100',
            'ch5' => 'required|string|max:100',


            'answer' => [
                'required',
                'in:ch1,ch2,ch3,ch4,ch5',
            ],


            'qtype' => [
                'required',
                function ($attribute, $value, $fail) {
                    $testType = $this->input('test_type');
                    $verbalTypes = ['verbal reasoning', 'verbal comprehension'];
                    $nonVerbalTypes = ['quantitative reasoning', 'figural reasoning'];

                    if ($testType === 'verbal' && !in_array($value, $verbalTypes)) {
                        $fail('For verbal tests, qtype must be verbal reasoning or verbal comprehension.');
                    }

                    if ($testType === 'non-verbal' && !in_array($value, $nonVerbalTypes)) {
                        $fail('For non-verbal tests, qtype must be quantitative reasoning or figural reasoning.');
                    }
                }
            ],
        ];
    }
}
