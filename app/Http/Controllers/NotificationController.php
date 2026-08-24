<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->take(100)->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Endpoint JSON léger interrogé périodiquement par le clavier de notifications (cloche)
     * de tous les en-têtes de l'application pour un rafraîchissement quasi temps réel sans
     * dépendre d'un serveur WebSocket.
     */
    public function recentes()
    {
        $utilisateur = auth()->user();

        return response()->json([
            'non_lues' => $utilisateur->unreadNotifications()->count(),
            'notifications' => $utilisateur->notifications()->take(8)->get()->map(fn ($n) => [
                'id' => $n->id,
                'lu' => ! is_null($n->read_at),
                'type' => $n->data['type'] ?? null,
                'titre' => $n->data['titre'] ?? '',
                'message' => $n->data['message'] ?? '',
                'icone' => $n->data['icone'] ?? 'bi-bell',
                'couleur' => $n->data['couleur'] ?? 'secondary',
                'lien' => $n->data['lien'] ?? null,
                'date' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function marquerLu(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $lien = $notification->data['lien'] ?? route('notifications.index');

        return redirect($lien);
    }

    public function marquerToutLu()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
