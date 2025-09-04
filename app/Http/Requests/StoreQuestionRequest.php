<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreQuestionRequest extends FormRequest
{
    /**
     * Authorize the request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */


    /**
     * Validation rules
     *
     */


    public function rules(): array
{
    return [
        'format' => 'required|in:text,photo',

 
        'question' => Rule::when(
            request('format') === 'text',
            ['required', 'string', 'max:100']
        ),
        'question_image' => Rule::when(
            request('format') === 'photo',
            ['required', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048']
        ),

        'ch1' => 'required_if:format,text|string|max:100',
        'ch1_image' => 'required_if:format,photo|image|mimes:jpg,jpeg,png,gif|max:2048',

        'ch2' => 'required_if:format,text|string|max:100',
        'ch2_image' => 'required_if:format,photo|image|mimes:jpg,jpeg,png,gif|max:2048',

        'ch3' => 'required_if:format,text|string|max:100',
        'ch3_image' => 'required_if:format,photo|image|mimes:jpg,jpeg,png,gif|max:2048',

        'ch4' => 'required_if:format,text|string|max:100',
        'ch4_image' => 'required_if:format,photo|image|mimes:jpg,jpeg,png,gif|max:2048',

        'ch5' => 'required_if:format,text|string|max:100',
        'ch5_image' => 'required_if:format,photo|image|mimes:jpg,jpeg,png,gif|max:2048',

        'answer' => 'required|in:ch1,ch2,ch3,ch4,ch5',
        'test_type' => 'required|in:verbal,non-verbal',
        'qtype' => [
            'required',
            function ($attribute, $value, $fail) {
                $testType = $this->input('test_type');
                $allowedTypes = $testType === 'verbal'
                    ? ['verbal reasoning', 'verbal comprehension']
                    : ['quantitative reasoning', 'figural reasoning'];

                if (!in_array($value, $allowedTypes)) {
                    $fail("For {$testType} tests, qtype must be one of: " . implode(', ', $allowedTypes) . '.');
                }
            }
        ],
    ];
}

}
