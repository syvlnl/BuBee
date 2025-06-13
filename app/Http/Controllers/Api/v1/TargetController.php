<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\TargetCollection;
use App\Http\Resources\v1\TargetResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Target;

class TargetController extends Controller
{
    public function index()
    {
        return new TargetCollection(Target::with('user')->get());
    }

    public function show(Target $target)
    {
        return new TargetResource($target->load('user'));
    }
}
