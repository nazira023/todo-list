<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ChecklistController extends Controller
{
    public function index()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'data' => null
                ], 403);
            }

            $checklists = Auth::user()->checklists()->with('items')->get();

            return response()->json([
                'success' => true,
                'message' => 'Checklists retrieved successfully',
                'data' => $checklists
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate(['title' => 'required|string']);

            $checklist = Checklist::create([
                'title' => $validated['title'],
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checklist created successfully',
                'data' => $checklist
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Checklist $checklist)
    {
        try {
            if ($checklist->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                    'data' => null
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Checklist retrieved successfully',
                'data' => $checklist->load('items')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Checklist $checklist)
    {
        try {
            if ($checklist->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                    'data' => null
                ], 403);
            }

            $validated = $request->validate(['title' => 'required|string']);
            $checklist->update(['title' => $validated['title']]);

            return response()->json([
                'success' => true,
                'message' => 'Checklist updated successfully',
                'data' => $checklist
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Checklist $checklist)
    {
        try {
            if ($checklist->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                    'data' => null
                ], 403);
            }

            $checklist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Checklist deleted successfully',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
