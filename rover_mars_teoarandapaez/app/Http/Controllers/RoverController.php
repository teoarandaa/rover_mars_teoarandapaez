<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rover;
use App\Models\MovementHistory;
use Illuminate\Validation\Rule;

class RoverController extends Controller
{
    public function execute(Request $request)
    {
        $validated = $request->validate([
            'x' => 'required|integer|min:0|max:199',
            'y' => 'required|integer|min:0|max:199',
            'direction' => ['required', Rule::in(['N', 'E', 'S', 'W'])],
            'commands' => [
                'required',
                'string',
                'max:100',
                'regex:/^[FLR]+$/i' // Solo permite F, L, R (case insensitive)
            ],
            'obstacles' => 'nullable|array',
            'obstacles.*' => 'array|size:2',
            'obstacles.*.0' => 'integer|min:0|max:199',
            'obstacles.*.1' => 'integer|min:0|max:199',
        ], [
            'x.required' => 'La posición X es requerida',
            'x.integer' => 'La posición X debe ser un número entero',
            'x.min' => 'La posición X debe ser mayor o igual a 0',
            'x.max' => 'La posición X debe ser menor o igual a 199',
            'y.required' => 'La posición Y es requerida',
            'y.integer' => 'La posición Y debe ser un número entero',
            'y.min' => 'La posición Y debe ser mayor o igual a 0',
            'y.max' => 'La posición Y debe ser menor o igual a 199',
            'direction.required' => 'La dirección es requerida',
            'direction.in' => 'La dirección debe ser N, E, S o W',
            'commands.required' => 'Los comandos son requeridos',
            'commands.string' => 'Los comandos deben ser texto',
            'commands.max' => 'Los comandos no pueden exceder 100 caracteres',
            'commands.regex' => 'Los comandos solo pueden contener F, L o R',
            'obstacles.array' => 'Los obstáculos deben ser un array',
            'obstacles.*.array' => 'Cada obstáculo debe ser un array de 2 elementos',
            'obstacles.*.size' => 'Cada obstáculo debe tener exactamente 2 coordenadas',
            'obstacles.*.0.integer' => 'La coordenada X del obstáculo debe ser un número entero',
            'obstacles.*.0.min' => 'La coordenada X del obstáculo debe ser mayor o igual a 0',
            'obstacles.*.0.max' => 'La coordenada X del obstáculo debe ser menor o igual a 199',
            'obstacles.*.1.integer' => 'La coordenada Y del obstáculo debe ser un número entero',
            'obstacles.*.1.min' => 'La coordenada Y del obstáculo debe ser mayor o igual a 0',
            'obstacles.*.1.max' => 'La coordenada Y del obstáculo debe ser menor o igual a 199',
        ]);

        // Convertir comandos a mayúsculas para consistencia
        $validated['commands'] = strtoupper($validated['commands']);

        // Validar que no haya obstáculos en la posición inicial
        if (!empty($validated['obstacles'])) {
            foreach ($validated['obstacles'] as $obstacle) {
                if ($obstacle[0] == $validated['x'] && $obstacle[1] == $validated['y']) {
                    return response()->json([
                        'error' => 'No puede haber un obstáculo en la posición inicial del rover'
                    ], 422);
                }
            }
        }

        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {
        try {
            $movements = MovementHistory::orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json($movements);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar el historial: ' . $e->getMessage()
            ], 500);
        }
    }
} 