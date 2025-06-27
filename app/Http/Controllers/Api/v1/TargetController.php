<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\TargetCollection;
use App\Http\Resources\v1\TargetResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\v1\TargetQuery;
use App\Models\Target;

class TargetController extends Controller
{
    public function index($user, Request $request)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $filter = new TargetQuery();
        $queryItems = $filter->transform($request);
        $targets = Target::with('user')
            ->where('user_id', $user)
            ->where($queryItems)
            ->get();
        return new TargetCollection($targets);
    }

    public function show(Target $target, $user)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return new TargetResource($target->load('user'));
    }

    
    public function store(Request $request, $user)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $rules = [
            'name' => 'required|string|max:50',
            'amount_needed' => 'required|numeric|min:0',
            'amount_collected' => 'nullable|numeric|min:0',
            'deadline' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $target = Target::create([
            'name' => $request->name,
            'user_id' => $user,
            'amount_needed' => $request->amount_needed,
            'amount_collected' => 0,
            'deadline' => $request->deadline,
        ]);

        return new TargetResource($target->load('user'));
    }

    public function update(Request $request, $user, $targetId)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $target = Target::where('user_id', $user)->where('target_id', $targetId)->first();
        if (!$target) {
            return response()->json(['message' => 'Target not found'], 404);
        }

        $rules = [
            'name' => 'required|string|max:50',
            'amount_needed' => 'required|numeric|min:0',
            'amount_collected' => 'nullable|numeric|min:0',
            'deadline' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $target->update($request->only(array_keys($rules)));

        return new TargetResource($target->load('user'));
    }
    
    public function destroy($user, $targetId)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $target = Target::where('user_id', $user)->where('target_id', $targetId)->first();
        if (!$target) {
            return response()->json(['message' => 'Target not found'], 404);
        }
        $target->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
