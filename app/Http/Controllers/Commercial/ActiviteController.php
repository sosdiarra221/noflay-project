<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Activite;
use App\Models\Commercial\Prospect;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function store(Request $request, Prospect $prospect)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:appel,email,whatsapp,sms,visite,rendez_vous,note,relance,document,autre'],
            'objet' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_activite' => ['nullable', 'date'],
        ]);

        $data['prospect_id'] = $prospect->id;
        $data['date_activite'] = $data['date_activite'] ?? now();
        $data['user_id'] = auth()->id();

        Activite::create($data);

        return back()->with('success', 'Activité ajoutée avec succès.');
    }
}
