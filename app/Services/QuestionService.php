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
        $validated['question'] = '<img src="' . asset("storage/$path") . '">';
    }

    // Handle choice images
    foreach (['ch1', 'ch2', 'ch3', 'ch4', 'ch5'] as $choice) {
        if ($request->hasFile($choice . '_image')) {
            $path = $request->file($choice . '_image')->store('questions', 'public');
            $validated[$choice] = '<img src="' . asset("storage/$path") . '">';
        }
    }

        $question = Question::create($validated);

        return $this->transformUrls($question);
    }


public function update(Question $question, array $validated, $request): Question
{
    // Handle question image
   if ($request->hasFile('question_image')) {
        $path = $request->file('question_image')->store('questions', 'public');
        $validated['question'] = '<img src="' . asset("storage/$path") . '">';
    }

    // Handle choice images
    foreach (['ch1', 'ch2', 'ch3', 'ch4', 'ch5'] as $choice) {
        if ($request->hasFile($choice . '_image')) {
            $path = $request->file($choice . '_image')->store('questions', 'public');
            $validated[$choice] = '<img src="' . asset("storage/$path") . '">';
        }
    }

    $question->update($validated);

    return $this->transformUrls($question);
}




public function transformUrls(Question $question): Question
{
    $baseUrl = asset('storage');

    foreach (['question', 'ch1', 'ch2', 'ch3', 'ch4', 'ch5'] as $field) {
        $value = $question->$field;

        if (!$value) continue;

        // If it starts with <img src=, treat it as HTML image
        if (str_starts_with(trim($value), '<img src=')) {
            // Leave it as is
            continue;
        }

        // // If it's not an URL and doesn't contain spaces or HTML, treat as file path
        // if (!str_starts_with($value, 'http') && !str_contains($value, ' ') && !str_contains($value, '<')) {
        //     $question->$field = $baseUrl . '/' . $value;
        // }
        // Otherwise treat it as plain text, leave as is
    }

    return $question;
}


}
