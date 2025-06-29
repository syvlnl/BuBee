<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\CategoryResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CategoryCollection;
use App\Models\Category;
use App\Services\v1\CategoryQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index($user, Request $request)
    {
        $filter = new CategoryQuery();
        $queryItems = $filter->transform($request);
        $categories = Category::with('user')
            ->where('user_id', $user)
            ->where($queryItems)
            ->get();
        return new CategoryCollection($categories);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category->load('user'));
    }

    public function store(Request $request, $user)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $rules = [
            'name' => 'required|string|max:50',
            'isExpense' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::create([
            'user_id' => $user,
            'name' => $request->name,
            'is_expense' => $request->isExpense
        ]);

        return new CategoryResource($category);
    }

    public function update(Request $request, $user, $categoryId)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $category = Category::where('user_id', $user)->where('id', $categoryId)->first();
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $rules = [
            'name' => 'sometimes|required|string|max:50',
            'is_expense' => 'sometimes|required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update([
            'name' => $request->name ?? $category->name,
            'is_expense' => $request->is_expense ?? $category->is_expense,
            'image' => $request->file('image') ? $request->file('image')->store('categories', 'public') : $category->image,
        ]);

        return new CategoryResource($category);
    }

    public function destroy($user, $categoryId)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $category = Category::where('user_id', $user)->where('id', $categoryId)->first();
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
