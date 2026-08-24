{{-- Clavier PIN réutilisable : 4 cases + pavé numérique, alimente un champ caché $champNom. --}}
<div class="pin-pad-conteneur" data-champ="{{ $champNom }}">
    <input type="hidden" name="{{ $champNom }}" class="pin-pad-valeur">
    <div class="d-flex justify-content-center gap-2 mb-4">
        @for ($i = 0; $i < 4; $i++)
            <input type="password" inputmode="numeric" maxlength="1" class="form-control text-center fs-3 pin-pad-case" style="width: 56px; height: 56px;" autocomplete="off">
        @endfor
    </div>
    <div class="row g-2 mx-auto" style="max-width: 260px;">
        @foreach ([1,2,3,4,5,6,7,8,9] as $chiffre)
            <div class="col-4"><button type="button" class="btn btn-light w-100 py-3 pin-pad-touche">{{ $chiffre }}</button></div>
        @endforeach
        <div class="col-4"><button type="button" class="btn btn-light-danger w-100 py-3 pin-pad-effacer">Effacer</button></div>
        <div class="col-4"><button type="button" class="btn btn-light w-100 py-3 pin-pad-touche">0</button></div>
        <div class="col-4"><button type="button" class="btn btn-light-secondary w-100 py-3 pin-pad-retour"><i class="bi bi-backspace"></i></button></div>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.pin-pad-conteneur').forEach(function (conteneur) {
                const cases = [...conteneur.querySelectorAll('.pin-pad-case')];
                const champValeur = conteneur.querySelector('.pin-pad-valeur');

                function synchroniser() {
                    champValeur.value = cases.map(c => c.value).join('');
                }

                function focaliserPremiereVide() {
                    const vide = cases.find(c => c.value === '');
                    (vide || cases[cases.length - 1]).focus();
                }

                cases.forEach(function (champ, index) {
                    champ.addEventListener('input', function () {
                        champ.value = champ.value.replace(/\D/g, '').slice(0, 1);
                        if (champ.value && index < cases.length - 1) {
                            cases[index + 1].focus();
                        }
                        synchroniser();
                    });
                    champ.addEventListener('keydown', function (e) {
                        if (e.key === 'Backspace' && ! champ.value && index > 0) {
                            cases[index - 1].focus();
                        }
                    });
                });

                conteneur.querySelectorAll('.pin-pad-touche').forEach(function (touche) {
                    touche.addEventListener('click', function () {
                        const vide = cases.find(c => c.value === '');
                        if (vide) {
                            vide.value = touche.textContent.trim();
                            synchroniser();
                            focaliserPremiereVide();
                        }
                    });
                });

                const boutonRetour = conteneur.querySelector('.pin-pad-retour');
                if (boutonRetour) {
                    boutonRetour.addEventListener('click', function () {
                        for (let i = cases.length - 1; i >= 0; i--) {
                            if (cases[i].value) {
                                cases[i].value = '';
                                cases[i].focus();
                                break;
                            }
                        }
                        synchroniser();
                    });
                }

                const boutonEffacer = conteneur.querySelector('.pin-pad-effacer');
                if (boutonEffacer) {
                    boutonEffacer.addEventListener('click', function () {
                        cases.forEach(c => c.value = '');
                        synchroniser();
                        cases[0].focus();
                    });
                }
            });
        });
    </script>
@endonce
