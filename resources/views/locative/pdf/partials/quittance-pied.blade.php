@php
    $piedElements = array_filter([$reglage->adresse, $reglage->telephone, $reglage->email, $reglage->site_web]);
@endphp
<table class="pied-table">
    <tr>
        <td class="pied-bar">
            {{ $piedElements ? implode('  |  ', $piedElements) : ($reglage->nom_societe ?: config('app.name')) }}
        </td>
    </tr>
</table>
<p class="pied-tagline">Votre satisfaction, notre priorité.</p>
