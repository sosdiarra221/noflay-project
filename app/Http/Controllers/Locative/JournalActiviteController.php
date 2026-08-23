<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\JournalActivite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JournalActiviteController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('locative.journal');

        $entrees = JournalActivite::with(['utilisateur.departement'])
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->when($request->filled('entity_type'), fn ($q) => $q->where('entity_type', $request->entity_type))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->limit(200)
            ->get();

        $utilisateurs = User::orderBy('name')->get();
        $typesEntites = JournalActivite::select('entity_type')->distinct()->pluck('entity_type');

        return view('locative.journal.index', compact('entrees', 'utilisateurs', 'typesEntites'));
    }
}
