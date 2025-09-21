<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        'format' => 'required|in:text,photo',

        // Question
        'question' => Rule::when(
            request('format') === 'text',
            ['required', 'string', 'max:100']
        ),
        'question_image' => Rule::when(
            request()->hasFile('question_image'),
            ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048']
        ),

        // Choices
        'ch1' => Rule::when(
            request('format') === 'text',
            ['required', 'string', 'max:100']
        ),
        'ch1_image' => Rule::when(
            request()->hasFile('ch1_image'),
            ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048']
        ),

        'ch2' => Rule::when(
            request('format') === 'text',
            ['required', 'string', 'max:100']
        ),
        'ch2_image' => Rule::when(
            request()->hasFile('ch2_image'),
            ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048']
        ),

        'ch3' => Rule::when(
            request('format') === 'text',
            ['required', 'string', 'max:100']
        ),
        'ch3_image' => Rule::when(
            request()->hasFile('ch3_image'),
            ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048']
        ),

        'ch4' => Rule::when(
            request('format') === 'text',
            ['required', 'string', 'max:100']
        ),
        'ch4_image' => Rule::when(
            request()->hasFile('ch4_image'),
            ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048']
        ),

        'ch5' => Rule::when(
            request('format') === 'text',
            ['required', 'string', 'max:100']
        ),
        'ch5_image' => Rule::when(
            request()->hasFile('ch5_image'),
            ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048']
        ),

        // Answer & types
        'answer' => 'required|in:ch1,ch2,ch3,ch4,ch5',
        'test_type' => 'required|in:Verbal,non-verbal',
        'qtype' => [
            'required',
            function ($attribute, $value, $fail) {
                $testType = $this->input('test_type');
                $allowedTypes = $testType === 'Verbal'
                    ? ['Verbal Reasoning', 'Verbal Comprehension']
                    : ['Quantitative Reasoning', 'Figural Reasoning'];

                if (!in_array($value, $allowedTypes)) {
                    $fail("For {$testType} tests, qtype must be one of: " . implode(', ', $allowedTypes) . '.');
                }
            }
        ],
    ];
}

}
