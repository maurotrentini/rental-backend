<?php

namespace App\Http\Controllers;

use App\Models\Extra;
use App\Http\Requests\StoreExtraRequest;
use App\Http\Requests\UpdateExtraRequest;
use App\Http\Resources\ExtraResource;
use Illuminate\Http\Request;

class ExtraController extends Controller
{
    public function index(Request $request)
    {
        $query = Extra::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $extras = $query->orderBy('name')->paginate(15);

        return ExtraResource::collection($extras);
    }

    public function store(StoreExtraRequest $request)
    {
        $extra = Extra::create($request->validated());

        return new ExtraResource($extra);
    }

    public function show(Extra $extra)
    {
        return new ExtraResource($extra);
    }

    public function update(UpdateExtraRequest $request, Extra $extra)
    {
        $extra->update($request->validated());

        return new ExtraResource($extra);
    }

    public function destroy(Extra $extra)
    {
        $extra->delete();

        return response()->json(['message' => 'Extra deleted successfully']);
    }
}