<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Documents\Document;
use App\Models\Documents\DocumentTemplate;
use Illuminate\Support\Facades\Gate;

class DocumentsDashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('documents.gerer');

        $stats = [
            'modeles_actifs' => DocumentTemplate::where('status', DocumentTemplate::STATUT_ACTIVE)->count(),
            'modeles_total' => DocumentTemplate::count(),
            'documents_generes' => Document::count(),
            'documents_ce_mois' => Document::whereMonth('generated_at', now()->month)->whereYear('generated_at', now()->year)->count(),
        ];

        $derniersDocuments = Document::with(['template', 'documentable'])->latest('generated_at')->take(8)->get();
        $modeles = DocumentTemplate::withCount('documents')->orderBy('name')->get();

        return view('documents.dashboard', compact('stats', 'derniersDocuments', 'modeles'));
    }
}
