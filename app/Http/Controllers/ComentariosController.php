<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comentario;
use Illuminate\Support\Facades\Auth;

class ComentariosController extends Controller
{

    public function __construct()
    {
        // Solo usuarios autenticados pueden acceder al foro
        $this->middleware('auth');
    }

    public function store(Request $request, $conversacion_id)
    {
        $request->validate([
            'texto' => 'required|string',
        ]);

        $comentario = Comentario::create([
            'texto' => $request->texto,
            'user_id' => Auth::id(),
            'conversacion_id' => $conversacion_id,
        ]);

        return response()->json(['message' => 'Comentario creado', 'comentario' => $comentario], 201);
    }

    // Editar un comentario
    public function update(Request $request, $id)
    {
        $comentario = Comentario::findOrFail($id);

        if ($comentario->user_id !== Auth::id()) {
            return response()->json(['error' => 'No tienes permiso para editar este comentario.'], 403);
        }

        $request->validate([
            'texto' => 'required|string',
        ]);

        $comentario->update(['texto' => $request->texto]);

        return response()->json(['message' => 'Comentario actualizado', 'comentario' => $comentario]);
    }

    // Eliminar un comentario
    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);

        if ($comentario->user_id !== Auth::id()) {
            return response()->json(['error' => 'No tienes permiso para eliminar este comentario.'], 403);
        }

        $comentario->delete();
        return response()->json(['message' => 'Comentario eliminado']);
    }
}
