<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    private const ERROR_FAQ_NOT_FOUND = 'FAQ not found';

    public function index()
    {
        return response()->json([
            "faqs" => Faq::orderBy("id", "DESC")->get()
        ]);
    }

    public function show($id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json(["message" => self::ERROR_FAQ_NOT_FOUND], 404);
        }

        return response()->json($faq);
    }

    public function store(Request $request)
    {
        $request->validate([
            "question" => "required|string",
            "answer" => "nullable|string",
            "show" => "required|boolean"
        ]);

        $faq = Faq::create($request->all());

        return response()->json([
            "message" => "FAQ created successfully",
            "faq" => $faq
        ]);
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json(["message" => self::ERROR_FAQ_NOT_FOUND], 404);
        }

        $request->validate([
            "question" => "required|string",
            "answer" => "nullable|string",
            "show" => "required|boolean"
        ]);

        $faq->update($request->all());

        return response()->json([
            "message" => "FAQ updated successfully",
            "faq" => $faq
        ]);
    }

    public function destroy($id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json(["message" => self::ERROR_FAQ_NOT_FOUND], 404);
        }

        $faq->delete();

        return response()->json([
            "message" => "FAQ deleted successfully"
        ]);
    }
}
