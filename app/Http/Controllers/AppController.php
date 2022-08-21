<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\AppSaveRequest;
use App\Http\Resources\App\App as AppResource;
use App\Http\Resources\App\AppCollection;
use App\Models\App;
use Illuminate\Http\JsonResponse;


class AppController extends Controller
{

    public function show(App $App): AppResource
    {
        return new AppResource($App);
    }

    public function index(): AppCollection
    {
         return new AppCollection(App::all());
    }

    public function store(AppSaveRequest $request): AppResource
    {
        return new AppResource(App::create($request->validated()));
    }

    public function destroy(App $app): JsonResponse
    {

        $app->delete();
        return response()->json([
            'message' => 'Successfully deleted App',
        ], 200);
    }
}
