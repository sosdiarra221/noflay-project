{{-- Cloche de notifications réutilisable : rafraîchie périodiquement en AJAX (pas de serveur
     WebSocket requis) via GET notifications.recentes. --}}
<div class="dropdown pe-dropdown-mega d-none d-md-block">
    <button class="btn header-btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="notifBellBouton">
        <i class="bi bi-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBellBadge" style="font-size: 10px;"></span>
    </button>
    <div class="dropdown-menu dropdown-mega-md header-dropdown-menu pe-noti-dropdown-menu p-0">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="d-flex align-items-center mb-0">Notifications <span class="badge bg-success rounded-circle align-middle ms-1 d-none" id="notifBellBadgeInterieur"></span></h6>
            <form action="{{ route('notifications.marquer-tout-lu') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link btn-sm p-0 fs-12">Tout marquer comme lu</button>
            </form>
        </div>
        <div class="p-2" id="notifBellListe" style="max-height: 360px; overflow-y: auto;">
            <p class="text-muted text-center my-4 fs-12" id="notifBellVide">Aucune notification pour le moment.</p>
        </div>
        <div class="p-2 border-top text-center">
            <a href="{{ route('notifications.index') }}" class="fs-12">Voir toutes les notifications</a>
        </div>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const badge = document.getElementById('notifBellBadge');
            const badgeInterieur = document.getElementById('notifBellBadgeInterieur');
            const liste = document.getElementById('notifBellListe');
            const vide = document.getElementById('notifBellVide');

            function couleurBootstrap(couleur) {
                return ['primary', 'success', 'danger', 'warning', 'info', 'secondary'].includes(couleur) ? couleur : 'secondary';
            }

            function rafraichir() {
                fetch('{{ route('notifications.recentes') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.ok ? r.json() : null)
                    .then(function (data) {
                        if (! data) return;

                        if (data.non_lues > 0) {
                            badge.textContent = data.non_lues > 9 ? '9+' : data.non_lues;
                            badge.classList.remove('d-none');
                            badgeInterieur.textContent = data.non_lues;
                            badgeInterieur.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                            badgeInterieur.classList.add('d-none');
                        }

                        if (! data.notifications.length) {
                            liste.innerHTML = '';
                            liste.appendChild(vide);
                            return;
                        }

                        liste.innerHTML = data.notifications.map(function (n) {
                            const c = couleurBootstrap(n.couleur);
                            return `
                                <form action="/notifications/${n.id}/lu" method="POST" class="mb-1">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn w-100 text-start p-2 rounded border-0 ${n.lu ? '' : 'bg-light-subtle'}" style="background: none;">
                                        <div class="d-flex gap-2">
                                            <div class="avatar-md d-flex align-items-center justify-content-center bg-${c}-subtle text-${c} fs-16 flex-shrink-0 rounded-circle">
                                                <i class="bi ${n.icone}"></i>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <h6 class="mb-1 fs-13">${n.titre}${n.lu ? '' : ' <span class=\"badge bg-danger rounded-circle p-1\" style=\"font-size:6px;vertical-align:middle;\"></span>'}</h6>
                                                <p class="text-muted mb-1 fs-12 text-wrap">${n.message}</p>
                                                <p class="text-muted mb-0 fs-11">${n.date}</p>
                                            </div>
                                        </div>
                                    </button>
                                </form>`;
                        }).join('');
                    })
                    .catch(function () {});
            }

            rafraichir();
            setInterval(rafraichir, 30000);
        });
    </script>
@endonce
