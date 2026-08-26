@if (isset($licenceAlerte))
    <div class="modal fade" id="licenceAlerteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h6 class="modal-title"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Licence bientôt expirée</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Votre licence expire
                        @if ($licenceAlerte['jours'] <= 0)
                            <strong>aujourd'hui</strong>
                        @else
                            dans <strong>{{ $licenceAlerte['jours'] }} jour{{ $licenceAlerte['jours'] > 1 ? 's' : '' }}</strong>
                        @endif
                        (le {{ \Illuminate\Support\Carbon::parse($licenceAlerte['date_fin'])->format('d/m/Y') }}).
                    </p>
                    <p class="mb-0 text-muted fs-13">Passé ce délai, l'accès à l'application sera suspendu. Contactez le support client ou l'administrateur du logiciel pour renouveler votre licence.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">J'ai compris</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('licenceAlerteModal')).show();
        });
    </script>
@endif
