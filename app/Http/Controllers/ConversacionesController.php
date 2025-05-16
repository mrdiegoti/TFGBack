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
        $conversaciones = Conversacion::with('user')->get();
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
    // Buscar la conversación o error 404
    $conversacion = Conversacion::findOrFail($id);

    // Validar que el usuario autenticado sea el dueño
    if ($conversacion->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Verificar que no tenga comentarios aún
    if ($conversacion->comentarios()->count() > 0) {
        return response()->json(['error' => 'No puedes editar esta conversación porque ya tiene comentarios.'], 400);
    }

    // Validar campos opcionales
    $request->validate([
        'titulo' => 'nullable|string|max:255',
        'descripcion' => 'nullable|string',
    ]);

    // Comprobar que al menos un campo venga con contenido válido
    if (!$request->filled('titulo') && !$request->filled('descripcion')) {
        return response()->json(['error' => 'Debes proporcionar al menos un campo para actualizar (título o descripción).'], 400);
    }

    // Preparar solo los campos que vienen y no son null
    $data = $request->only(['titulo', 'descripcion']);
    $data = array_filter($data, fn($val) => $val !== null);

    // Actualizar la conversación
    $conversacion->update($data);

    // Responder con la conversación actualizada
    return response()->json([
        'message' => 'Conversación actualizada',
        'conversacion' => $conversacion
    ]);
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

    public function show($id)
{
    $conversacion = Conversacion::with(['comentarios' => function ($query) {
        $query->with('user');
    }])->findOrFail($id);

    return response()->json($conversacion);
}


}
