<?php

namespace App\Http\Controllers;

use App\Http\Requests\EndNode\EndNodeSaveRequest;
use App\Http\Resources\EndNode\EndNode as EndNodeResource;
use App\Http\Resources\EndNode\EndNodeCollection;
use App\Http\Resources\HistoricalData\HistoricalDataCollection;
use App\Models\EndNode;
use Illuminate\Http\JsonResponse;

class EndNodeController extends Controller
{
    public function show(EndNode $endNode): EndNodeResource
    {
        return new EndNodeResource($endNode);
    }


    public function index(): EndNodeCollection
    {
         return new EndNodeCollection(EndNode::all());
    }

    public function indexLastData(EndNode $endNode): HistoricalDataCollection
    {
         return new HistoricalDataCollection($endNode->historicalData()->latest()->take(50)->get());
    }

    public function store(EndNodeSaveRequest $request): EndNodeResource
    {
        return new EndNodeResource(EndNode::create($request->validated()));
    }

    public function destroy(EndNode $endNode): JsonResponse
    {
        $endNode->delete();
        return response()->json([
            'message' => 'Successfully deleted EndNode',
        ], 200);
    }
}
