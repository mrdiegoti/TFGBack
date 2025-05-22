<?php

namespace App\Http\Controllers;

use App\Models\GameComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameCommentController extends Controller
{
    // Obtener comentarios de un partido
    public function index($id)
    {
        $comments = GameComment::with('user:id,name')
            ->where('game_id', $id)
            ->latest()
            ->get();

        return response()->json($comments);
    }

    // Guardar un nuevo comentario
    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = GameComment::create([
            'user_id' => Auth::id(),
            'game_id' => $id,
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user:id,name'), 201);
    }

    public function update(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = GameComment::findOrFail($commentId);

        // Verificar que el usuario autenticado es dueño del comentario
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $comment->content = $request->content;
        $comment->save();

        return response()->json($comment);
    }

    public function destroy($commentId)
    {
        $comment = GameComment::findOrFail($commentId);

        // Verificar que el usuario autenticado es dueño del comentario
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentario eliminado']);
    }
}
