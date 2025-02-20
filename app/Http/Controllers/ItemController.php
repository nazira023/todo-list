<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index($checklistId)
    {
        return response()->json(Item::where('checklist_id', $checklistId)->get());
    }

    public function store(Request $request, $checklistId)
    {
        $request->validate(['name' => 'required|string']);

        $item = Item::create([
            'name' => $request->name,
            'is_done' => false,
            'checklist_id' => $checklistId
        ]);

        return response()->json($item, 201);
    }

    public function show($checklistId, $itemId)
    {
        dd("WOW");
        $item = Item::where('checklist_id', $checklistId)->find($itemId);

        return $item ? response()->json($item) : response()->json(['message' => 'Item not found'], 404);
    }

    public function update(Request $request, $checklistId, $itemId)
    {
        $item = Item::where('checklist_id', $checklistId)->find($itemId);
        if (!$item) return response()->json(['message' => 'Item not found'], 404);

        $item->update($request->only(['name', 'is_done']));
        return response()->json($item);
    }

    public function destroy($checklistId, $itemId)
    {
        $item = Item::where('checklist_id', $checklistId)->find($itemId);
        if (!$item) return response()->json(['message' => 'Item not found'], 404);

        $item->delete();
        return response()->json(['message' => 'Item deleted']);
    }
    public function toggleStatus(Item $item)
    {
        if ($item->checklist->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    
        $item->update(['is_done' => !$item->is_done]);
    
        return response()->json($item);
    }
}
