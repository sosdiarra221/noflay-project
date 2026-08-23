<table class="entete-table">
    <tr>
        <td>
            @if ($reglage->logo && file_exists(public_path($reglage->logo)))
                <img class="logo-img" src="{{ public_path($reglage->logo) }}" alt="Logo">
            @endif
            <p class="societe-nom">{{ $reglage->nom_societe ?: config('app.name') }}</p>
            <p class="societe-tagline">LOCATION &bull; GESTION &bull; CONSEIL</p>
        </td>
        <td class="coord-droite">
            @if ($reglage->adresse)<div>{{ $reglage->adresse }}</div>@endif
            @if ($reglage->telephone)<div>{{ $reglage->telephone }}</div>@endif
            @if ($reglage->email)<div>{{ $reglage->email }}</div>@endif
        </td>
    </tr>
</table>
<div class="trait-entete">&nbsp;</div>
