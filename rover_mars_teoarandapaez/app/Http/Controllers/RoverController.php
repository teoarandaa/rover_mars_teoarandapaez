<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rover;
use App\Models\MovementHistory;

class RoverController extends Controller
{
    public function execute(Request $request)
    {
        $validated = $request->validate([
            'x' => 'required|integer|min:0|max:199',
            'y' => 'required|integer|min:0|max:199',
            'direction' => 'required|in:N,E,S,W',
            'commands' => 'required|string',
            'obstacles' => 'array',
            'obstacles.*' => 'array|size:2',
            'obstacles.*.0' => 'integer|min:0|max:199',
            'obstacles.*.1' => 'integer|min:0|max:199',
        ]);

        $rover = new Rover(
            $validated['x'],
            $validated['y'],
            $validated['direction'],
            $validated['obstacles'] ?? []
        );

        $result = $rover->execute($validated['commands']);

        // Guardar en el historial
        MovementHistory::create([
            'x' => $validated['x'],
            'y' => $validated['y'],
            'direction' => $validated['direction'],
            'commands' => $validated['commands'],
            'obstacles' => $validated['obstacles'] ?? [],
            'result_status' => $result['status'],
            'result_x' => $result['x'],
            'result_y' => $result['y'],
        ]);

        return response()->json($result);
    }

    public function history()
    {
        $movements = MovementHistory::orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($movements);
    }
} 