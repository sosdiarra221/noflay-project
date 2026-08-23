@extends('partials.layouts.master-commercial')

@section('title', 'Agenda | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', 'Agenda')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
    <style>
        .fc-event { cursor: pointer; }
    </style>
@endsection

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card">
                    <div class="p-5 pb-0">
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#rdvModal" id="btnNouveauRdv">
                            <i class="bi bi-plus-lg me-1"></i> Nouveau rendez-vous
                        </button>
                    </div>
                    <div class="card-header align-items-center">
                        <h5 class="card-action-title mb-0">Types</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="badge bg-primary-subtle text-primary"><i class="bi bi-circle-fill fs-10 me-1"></i> Rendez-vous</div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="badge bg-success-subtle text-success"><i class="bi bi-circle-fill fs-10 me-1"></i> Visite</div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="badge bg-warning-subtle text-warning"><i class="bi bi-circle-fill fs-10 me-1"></i> Appel</div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="badge bg-info-subtle text-info"><i class="bi bi-circle-fill fs-10 me-1"></i> Autre</div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header align-items-center">
                        <h5 class="card-action-title mb-0">Prochains rendez-vous</h5>
                    </div>
                    <div class="card-body" data-simplebar style="max-height: 350px;">
                        @forelse ($aVenir as $rdv)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0">{{ $rdv->titre }}</h6>
                                    <span class="fs-11 text-muted">{{ $rdv->date_debut->versionLongue() }} — {{ $rdv->date_debut->format('H:i') }}</span>
                                    @if ($rdv->prospect)
                                        <br><span class="fs-11 text-muted">{{ $rdv->prospect->nom_complet }}</span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                        @empty
                            <p class="text-muted text-center mb-0 py-3">Aucun rendez-vous à venir.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Rendez-vous (création / édition) -->
        <div class="modal fade" id="rdvModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0">
                <div class="modal-header p-3">
                    <h5 class="modal-title" id="rdvModalTitle">Nouveau rendez-vous</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="formRdv">
                    <div class="modal-body p-4">
                        <input type="hidden" id="rdvId">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Titre<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" id="rdvTitre" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" id="rdvType" required>
                                    <option value="rendez_vous">Rendez-vous</option>
                                    <option value="visite">Visite</option>
                                    <option value="appel">Appel</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Statut</label>
                                <select class="form-select" id="rdvStatut">
                                    <option value="planifie">Planifié</option>
                                    <option value="termine">Terminé</option>
                                    <option value="annule">Annulé</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prospect concerné</label>
                                <select class="form-select" id="rdvProspect">
                                    <option value="">—</option>
                                    @foreach ($prospects as $prospect)
                                        <option value="{{ $prospect->id }}">{{ $prospect->nom_complet }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lieu</label>
                                <input type="text" class="form-control" id="rdvLieu">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Début<span class="text-danger ms-1">*</span></label>
                                <input type="datetime-local" class="form-control" id="rdvDebut" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fin</label>
                                <input type="datetime-local" class="form-control" id="rdvFin">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="rdvDescription" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-danger d-none" id="btnSupprimerRdv">Supprimer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>
    </main>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const evenements = {!! json_encode($evenements) !!};
            const csrfToken = '{{ csrf_token() }}';
            const urlStore = '{{ route('commercial.agenda.store') }}';
            const urlUpdateBase = '{{ url('commercial/agenda') }}';

            const prospectSelect = new Choices(document.getElementById('rdvProspect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un prospect...',
                searchPlaceholderValue: 'Rechercher...',
            });

            const rdvModalEl = document.getElementById('rdvModal');
            const rdvModal = new bootstrap.Modal(rdvModalEl);
            const form = document.getElementById('formRdv');
            const btnSupprimer = document.getElementById('btnSupprimerRdv');

            function resetForm() {
                form.reset();
                document.getElementById('rdvId').value = '';
                prospectSelect.setChoiceByValue('');
                btnSupprimer.classList.add('d-none');
                document.getElementById('rdvModalTitle').textContent = 'Nouveau rendez-vous';
            }

            function toLocalInput(iso) {
                if (!iso) return '';
                const d = new Date(iso);
                const pad = (n) => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            }

            function ouvrirNouveau(dateDebut) {
                resetForm();
                if (dateDebut) {
                    document.getElementById('rdvDebut').value = toLocalInput(dateDebut);
                }
                rdvModal.show();
            }

            document.getElementById('btnNouveauRdv').addEventListener('click', () => ouvrirNouveau(null));

            function ouvrirEdition(evt) {
                resetForm();
                const props = evt.extendedProps;
                document.getElementById('rdvModalTitle').textContent = 'Modifier le rendez-vous';
                document.getElementById('rdvId').value = evt.id;
                document.getElementById('rdvTitre').value = evt.title;
                document.getElementById('rdvType').value = props.type;
                prospectSelect.setChoiceByValue(props.prospect_id ? String(props.prospect_id) : '');
                document.getElementById('rdvDebut').value = toLocalInput(evt.start);
                document.getElementById('rdvFin').value = toLocalInput(evt.end);
                document.getElementById('rdvLieu').value = props.lieu || '';
                document.getElementById('rdvStatut').value = props.statut;
                document.getElementById('rdvDescription').value = props.description || '';
                btnSupprimer.classList.remove('d-none');
                rdvModal.show();
            }

            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
                },
                initialView: 'dayGridMonth',
                locale: 'fr',
                firstDay: 1,
                height: 650,
                editable: true,
                selectable: true,
                events: evenements,
                dateClick: function (info) {
                    ouvrirNouveau(info.dateStr);
                },
                eventClick: function (info) {
                    ouvrirEdition(info.event);
                },
                eventDrop: function (info) {
                    envoyerMiseAJour(info.event.id, {
                        date_debut: info.event.start.toISOString(),
                        date_fin: info.event.end ? info.event.end.toISOString() : null,
                    });
                },
                eventResize: function (info) {
                    envoyerMiseAJour(info.event.id, {
                        date_debut: info.event.start.toISOString(),
                        date_fin: info.event.end ? info.event.end.toISOString() : null,
                    });
                },
            });
            calendar.render();

            function envoyerMiseAJour(id, data) {
                fetch(`${urlUpdateBase}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                }).then((r) => r.json()).catch(() => {});
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const id = document.getElementById('rdvId').value;
                const payload = {
                    titre: document.getElementById('rdvTitre').value,
                    type: document.getElementById('rdvType').value,
                    prospect_id: document.getElementById('rdvProspect').value || null,
                    date_debut: document.getElementById('rdvDebut').value,
                    date_fin: document.getElementById('rdvFin').value || null,
                    lieu: document.getElementById('rdvLieu').value,
                    statut: document.getElementById('rdvStatut').value,
                    description: document.getElementById('rdvDescription').value,
                };

                const url = id ? `${urlUpdateBase}/${id}` : urlStore;
                const method = id ? 'PUT' : 'POST';

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                })
                    .then((r) => r.json())
                    .then((data) => {
                        if (data.success) {
                            rdvModal.hide();
                            window.location.reload();
                        }
                    });
            });

            btnSupprimer.addEventListener('click', function () {
                const id = document.getElementById('rdvId').value;
                if (!id) return;

                Swal.fire({
                    title: 'Supprimer ce rendez-vous ?',
                    text: 'Cette action est irréversible.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Supprimer',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#f06548',
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`${urlUpdateBase}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                        })
                            .then((r) => r.json())
                            .then((data) => {
                                if (data.success) {
                                    rdvModal.hide();
                                    window.location.reload();
                                }
                            });
                    }
                });
            });
        });
    </script>
@endsection
