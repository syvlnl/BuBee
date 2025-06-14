<?php

namespace App\Http\Controllers\Api\v1;

use App\Filament\Admin\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CategoryCollection;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return new CategoryCollection(Category::with('user')->get());
    }

    public function show(Category $category)
    {
        return new CategoryResource($category->load('user'));
    }
}
