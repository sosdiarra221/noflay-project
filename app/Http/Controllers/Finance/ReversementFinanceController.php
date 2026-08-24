<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Events\VersementEffectue;
use App\Models\Reglage;
use App\Models\ReversementBailleur;
use App\Services\Locative\NumeroService;
use App\Services\Locative\ReversementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReversementFinanceController extends Controller
{
    public function index(Request $request, ReversementService $service)
    {
        Gate::authorize('finance.consulter');

        $periode = $request->filled('periode')
            ? Carbon::createFromFormat('Y-m', $request->periode)
            : now();

        $calculs = $service->calculerPourPeriode($periode);

        $existants = ReversementBailleur::where('periode_annee', $periode->year)
            ->where('periode_mois', $periode->month)
            ->get()
            ->keyBy('bailleur_id');

        $lignes = collect($calculs)->map(function ($ligne) use ($existants) {
            $ligne['reversement'] = $existants->get($ligne['bailleur']->id);

            return $ligne;
        });

        return view('finance.reversements.index', compact('lignes', 'periode'));
    }

    public function marquerVerse(Request $request)
    {
        Gate::authorize('finance.gerer');

        $data = $request->validate([
            'bailleur_id' => ['required', 'exists:bailleurs,id'],
            'periode_annee' => ['required', 'integer'],
            'periode_mois' => ['required', 'integer', 'min:1', 'max:12'],
            'montant_encaisse' => ['required', 'numeric', 'min:0'],
            'montant_frais_gestion' => ['required', 'numeric', 'min:0'],
            'montant_depenses' => ['nullable', 'numeric', 'min:0'],
            'montant_net' => ['required', 'numeric', 'min:0'],
            'date_versement' => ['required', 'date'],
            'mode_paiement_id' => ['nullable', 'exists:modes_paiement,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $reversement = ReversementBailleur::where('bailleur_id', $data['bailleur_id'])
            ->where('periode_annee', $data['periode_annee'])
            ->where('periode_mois', $data['periode_mois'])
            ->first();

        $valeurs = [
            'montant_encaisse' => $data['montant_encaisse'],
            'montant_frais_gestion' => $data['montant_frais_gestion'],
            'montant_depenses' => $data['montant_depenses'] ?? 0,
            'montant_net' => $data['montant_net'],
            'statut' => 'verse',
            'date_versement' => $data['date_versement'],
            'mode_paiement_id' => $data['mode_paiement_id'] ?? null,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'effectue_par_id' => auth()->id(),
        ];

        if ($reversement) {
            $reversement->update($valeurs);
        } else {
            $reversement = ReversementBailleur::create($valeurs + [
                'numero' => NumeroService::genererNumero(ReversementBailleur::class, 'REV'),
                'bailleur_id' => $data['bailleur_id'],
                'periode_annee' => $data['periode_annee'],
                'periode_mois' => $data['periode_mois'],
            ]);
        }

        event(new VersementEffectue($reversement));

        return back()->with('success', 'Reversement marqué comme versé avec succès.');
    }

    public function historique(Request $request)
    {
        Gate::authorize('finance.consulter');

        $reversements = ReversementBailleur::with(['bailleur', 'modePaiement', 'effectuePar'])
            ->when($request->filled('bailleur_id'), fn ($q) => $q->where('bailleur_id', $request->bailleur_id))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->orderByDesc('periode_annee')
            ->orderByDesc('periode_mois')
            ->get();

        $bailleurs = \App\Models\Bailleur::orderBy('nom')->get();

        return view('finance.reversements.historique', compact('reversements', 'bailleurs'));
    }

    public function apercuBordereau(ReversementBailleur $reversement)
    {
        [$reglage, $ville] = $this->donneesBordereau();
        $reversement->load('bailleur', 'modePaiement', 'effectuePar');

        $pdf = Pdf::loadView('finance.pdf.bordereau-reversement', compact('reversement', 'reglage', 'ville'));

        return $pdf->stream($reversement->numero.'.pdf');
    }

    public function telechargerBordereau(ReversementBailleur $reversement)
    {
        [$reglage, $ville] = $this->donneesBordereau();
        $reversement->load('bailleur', 'modePaiement', 'effectuePar');

        $pdf = Pdf::loadView('finance.pdf.bordereau-reversement', compact('reversement', 'reglage', 'ville'));

        return $pdf->download($reversement->numero.'.pdf');
    }

    protected function donneesBordereau(): array
    {
        $reglage = Reglage::courant();
        $ville = $reglage->adresse ? trim(explode(',', $reglage->adresse)[0]) : 'Dakar';

        return [$reglage, $ville];
    }
}
