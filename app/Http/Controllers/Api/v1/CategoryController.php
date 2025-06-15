<?php

namespace App\Http\Controllers\Api\v1;

use App\Filament\Admin\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CategoryCollection;
use App\Models\Category;
use App\Services\v1\CategoryQuery;
use Illuminate\Http\Request;

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
}
