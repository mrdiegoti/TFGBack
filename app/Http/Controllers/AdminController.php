<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\User;
use App\Models\Conversacion;
use App\Models\GameComment;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function getAdminData()
{
    return response()->json([
        'users' => User::all(),
        'conversations' => Conversacion::withCount('comentarios')->get(),
        'comments' => Comentario::with(['user', 'conversacion'])->get(),
        'gameComments' => GameComment::with('user')->get()
    ]);
}

    // Usuarios
    public function getUsers()
    {
        return User::all();
    }



    public function createUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        return response()->json($user, 201);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return response()->json($user);
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    // Conversaciones
    public function getConversations()
    {
        $conversations = Conversacion::all();
        return response()->json($conversations);
    }

    public function createConversation(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);
        $conversation = Conversacion::create($data);
        return response()->json($conversation, 201);
    }

    public function updateConversation(Request $request, $id)
    {
        $conversation = Conversacion::withCount('comentarios')->findOrFail($id);

        if ($conversation->comentarios_count > 0) {
            return response()->json(['error' => 'No se puede editar esta conversación porque tiene comentarios'], 403);
        }

        $data = $request->validate([
            'titulo' => 'required|string',
            'descripcion' => 'required|string',
        ]);
        $conversation->update($data);
        return response()->json($conversation);
    }

    public function deleteConversation($id)
    {
        $conversation = Conversacion::withCount('comentarios')->findOrFail($id);

        if ($conversation->comentarios_count > 0) {
            echo "<script>alert('No se puede eliminar esta conversación porque tiene comentarios');</script>";
            return response()->json(['error' => 'No se puede eliminar esta conversación porque tiene comentarios'], 403);
        }

        $conversation->delete();
        return response()->json(null, 204);
    }

    // Comentarios de conversación
    public function getComments()
    {
        $comments = Comentario::all();
        return response()->json($comments);
    }

    public function createComment(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required|exists:conversaciones,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);
        $comment = Comentario::create($data);
        return response()->json($comment, 201);
    }

    public function updateComment(Request $request, $id)
    {
        $comment = Comentario::findOrFail($id);
        $data = $request->validate(['texto' => 'required|string']);
        $comment->update($data);
        return response()->json($comment);
    }

    public function deleteComment($id)
    {
        Comentario::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    // Comentarios de partidos
    public function getGameComments()
    {
        return GameComment::with('user')->get();
    }

    public function createGameComment(Request $request)
    {
        $data = $request->validate([
            'game_id' => 'required|integer',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);
        $comment = GameComment::create($data);
        return response()->json($comment, 201);
    }

    public function updateGameComment(Request $request, $id)
    {
        $comment = GameComment::findOrFail($id);
        $data = $request->validate(['content' => 'required|string']);
        $comment->update($data);
        return response()->json($comment);
    }

    public function deleteGameComment($id)
    {
        GameComment::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
