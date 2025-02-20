<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends Controller
{
    public function index()
    {
        if (!Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json(Auth::user()->checklists()->with('items')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string']);

        $checklist = Checklist::create([
            'title' => $request->title,
            'user_id' => Auth::id()
        ]);

        return response()->json($checklist, 201);
    }

    public function show(Checklist $checklist)
    {
        if ($checklist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json($checklist->load('items'));
    }

    public function update(Request $request, Checklist $checklist)
    {
        if ($checklist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate(['title' => 'required|string']);
        $checklist->update(['title' => $request->title]);

        return response()->json($checklist);
    }

    public function destroy(Checklist $checklist)
    {
        if ($checklist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $checklist->delete();
        return response()->json(['message' => 'Checklist deleted']);
    }
}
