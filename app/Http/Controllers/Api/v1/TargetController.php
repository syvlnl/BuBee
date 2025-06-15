<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\TargetCollection;
use App\Http\Resources\v1\TargetResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Services\v1\TargetQuery;

class TargetController extends Controller
{
    public function index($user, Request $request)
    {
        $filter = new TargetQuery();
        $queryItems = $filter->transform($request);
        $targets = Target::with('user')
            ->where('user_id', $user)
            ->where($queryItems)
            ->get();
        return new TargetCollection($targets);
    }

    public function show(Target $target)
    {
        return new TargetResource($target->load('user'));
    }
}
