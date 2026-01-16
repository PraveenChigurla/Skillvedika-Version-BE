<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TermsAndConditions;
use Illuminate\Support\Facades\Validator;

class TermsAndConditionsController extends Controller
{
    // GET latest by type
    public function index(Request $request)
    {
        $type = $request->query('type');

        $query = TermsAndConditions::query();

        if ($type) {
            $query->where('type', $type);
        }

        return response()->json(
            $query->orderBy('updated_at', 'desc')->first()
        );
    }

    // PUT / POST update
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string',
            'type'    => 'required|in:student,instructor,privacy',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['last_updated_on'] = now();

        $record = TermsAndConditions::where('type', $data['type'])->first();

        if ($record) {
            $record->update($data);
        } else {
            $record = TermsAndConditions::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Terms and Conditions updated successfully',
            'data' => $record,
        ]);
    }
}
