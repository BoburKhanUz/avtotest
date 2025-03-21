<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionController extends Controller
{
    public function index(Test $test)
    {
        $questions = $test->questions;
        return view('admin.questions.index', compact('test', 'questions'));
    }

    public function create(Test $test)
    {
        return view('admin.questions.create', compact('test'));
    }

    public function store(Request $request, Test $test)
    {
        $validated = $request->validate([
            'content_uz' => 'required|string',
            'content_ru' => 'required|string',
            'options_uz' => 'required|array|min:2',
            'options_ru' => 'required|array|min:2',
            'correct_option' => 'required|string',
        ]);

        $test->questions()->create($validated);
        return redirect()->route('admin.questions.index', $test)->with('success', 'Savol muvaffaqiyatli qo‘shildi');
    }

    public function edit(Test $test, Question $question)
    {
        return view('admin.questions.edit', compact('test', 'question'));
    }

    public function update(Request $request, Test $test, Question $question)
    {
        $validated = $request->validate([
            'content_uz' => 'required|string',
            'content_ru' => 'required|string',
            'options_uz' => 'required|array|min:2',
            'options_ru' => 'required|array|min:2',
            'correct_option' => 'required|string',
        ]);

        $question->update($validated);
        return redirect()->route('admin.questions.index', $test)->with('success', 'Savol muvaffaqiyatli yangilandi');
    }

    public function destroy(Test $test, Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions.index', $test)->with('success', 'Savol o‘chirildi');
    }

    public function import(Request $request, Test $test)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            $test->questions()->create([
                'content_uz' => $record['content_uz'],
                'content_ru' => $record['content_ru'],
                'options_uz' => json_decode($record['options_uz'], true),
                'options_ru' => json_decode($record['options_ru'], true),
                'correct_option' => $record['correct_option'],
            ]);
        }

        return redirect()->route('admin.questions.index', $test)->with('success', 'Savollar muvaffaqiyatli import qilindi');
    }

    public function export(Test $test)
    {
        $questions = $test->questions;

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"questions_test_{$test->id}.csv\"",
        ];

        $callback = function () use ($questions) {
            $writer = Writer::createFromFileObject(new \SplTempFileObject());
            $writer->insertOne(['content_uz', 'content_ru', 'options_uz', 'options_ru', 'correct_option']);

            foreach ($questions as $question) {
                $writer->insertOne([
                    $question->content_uz,
                    $question->content_ru,
                    json_encode($question->options_uz),
                    json_encode($question->options_ru),
                    $question->correct_option,
                ]);
            }

            $writer->output();
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}