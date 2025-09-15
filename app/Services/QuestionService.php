<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\Storage;

class QuestionService
{
    public function store(array $validated, $request): Question
    {
        // Handle question image
        if ($request->hasFile('question_image')) {
            $path = $request->file('question_image')->store('questions', 'public');
            $validated['question'] = $path;
        }

        // Handle choice images
        foreach (['ch1', 'ch2', 'ch3', 'ch4', 'ch5'] as $choice) {
            if ($request->hasFile($choice . '_image')) {
                $path = $request->file($choice . '_image')->store('choices', 'public');
                $validated[$choice] = $path;
            }
        }

        $question = Question::create($validated);

        return $this->transformUrls($question);
    }

    public function transformUrls(Question $question): Question
    {
        $baseUrl = asset('storage');

        foreach (['question', 'ch1', 'ch2', 'ch3', 'ch4', 'ch5'] as $field) {
            if ($question->$field && !str_starts_with($question->$field, 'http')) {
                $question->$field = $baseUrl . '/' . $question->$field;
            }
        }

        return $question;
    }
}
