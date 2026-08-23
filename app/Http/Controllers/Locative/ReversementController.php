<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Services\Locative\ReversementService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReversementController extends Controller
{
    public function index(Request $request, ReversementService $service)
    {
        $periode = $request->filled('periode')
            ? Carbon::createFromFormat('Y-m', $request->periode)
            : now();

        $reversements = $service->calculerPourPeriode($periode);

        return view('locative.reversements.index', compact('reversements', 'periode'));
    }
}
