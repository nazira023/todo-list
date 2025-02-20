<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ItemController extends Controller
{
    public function index($checklistId)
    {
        try {
            $items = Item::where('checklist_id', $checklistId)->get();

            return response()->json([
                'success' => true,
                'message' => 'Items retrieved successfully',
                'data' => $items
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $checklistId)
    {
        try {
            $validated = $request->validate(['name' => 'required|string']);

            $item = Item::create([
                'name' => $validated['name'],
                'is_done' => false,
                'checklist_id' => $checklistId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($checklistId, $itemId)
    {
        try {
            $item = Item::where('checklist_id', $checklistId)->find($itemId);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item retrieved successfully',
                'data' => $item
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $checklistId, $itemId)
    {
        try {
            $item = Item::where('checklist_id', $checklistId)->find($itemId);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                    'data' => null
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string',
                'is_done' => 'sometimes|boolean'
            ]);

            $item->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($checklistId, $itemId)
    {
        try {
            $item = Item::where('checklist_id', $checklistId)->find($itemId);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                    'data' => null
                ], 404);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Item $item)
    {
        try {
            if ($item->checklist->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                    'data' => null
                ], 403);
            }

            $item->update(['is_done' => !$item->is_done]);

            return response()->json([
                'success' => true,
                'message' => 'Item status updated successfully',
                'data' => $item
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
