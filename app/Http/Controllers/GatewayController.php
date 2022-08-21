<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gateway\GatewaySaveRequest;
use App\Http\Resources\Gateway\Gateway as GatewayResource;
use App\Http\Resources\Gateway\GatewayCollection;
use App\Models\Gateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function show(Gateway $Gateway): GatewayResource
    {
        return new GatewayResource($Gateway);
    }


    public function index(): GatewayCollection
    {
         return new GatewayCollection(Gateway::all());
    }


    public function store(GatewaySaveRequest $request): GatewayResource
    {
        return new GatewayResource(Gateway::create($request->validated()));
    }

    public function destroy(Gateway $gateway): JsonResponse
    {

        $gateway->delete();
        return response()->json([
            'message' => 'Successfully deleted Gateway',
        ], 200);
    }
}
