<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversacionesController extends Controller
{
    public function __construct()
    {
        // Solo usuarios autenticados pueden acceder al foro
        $this->middleware('auth');
    }

    // Mostrar todas las conversaciones
    public function index()
    {
        $conversaciones = Conversacion::with('comentarios')->get(); // Cargar conversaciones con sus comentarios
        return response()->json($conversaciones);
    }

    // Crear una nueva conversación
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $conversacion = Conversacion::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Conversación creada exitosamente', 'conversacion' => $conversacion], 201);
    }

    // Editar una conversación
    public function update(Request $request, $id)
    {
        $conversacion = Conversacion::findOrFail($id);

        // Verificar que nadie haya comentado aún en la conversación
        if ($conversacion->comentarios()->count() > 0) {
            return response()->json(['error' => 'No puedes editar esta conversación porque ya tiene comentarios.'], 400);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $conversacion->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
        ]);

        return response()->json(['message' => 'Conversación actualizada', 'conversacion' => $conversacion]);
    }

    // Eliminar una conversación
    public function destroy($id)
    {
        $conversacion = Conversacion::findOrFail($id);

        // Verificar que nadie haya comentado aún en la conversación
        if ($conversacion->comentarios()->count() > 0) {
            return response()->json(['error' => 'No puedes eliminar esta conversación porque ya tiene comentarios.'], 400);
        }

        $conversacion->delete();
        return response()->json(['message' => 'Conversación eliminada']);
    }
}
