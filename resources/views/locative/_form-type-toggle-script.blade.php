<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-form-type-toggle]').forEach(function (conteneur) {
            const typeSelect = conteneur.querySelector('select[name="type"], select[name="type_locataire"]');
            if (! typeSelect) return;

            const champPrenom = conteneur.querySelector('.champ-prenom');
            const champNom = conteneur.querySelector('.champ-nom');
            const texteLabelNom = conteneur.querySelector('.texte-label-nom');
            const champsEntreprise = conteneur.querySelectorAll('.champ-entreprise');

            const colDefaut = champNom ? (champNom.dataset.colDefaut || 'col-md-6') : null;
            const colEntreprise = champNom ? (champNom.dataset.colEntreprise || 'col-md-12') : null;

            function synchroniser() {
                const estEntreprise = typeSelect.value === 'entreprise';

                if (champPrenom) champPrenom.classList.toggle('d-none', estEntreprise);
                if (texteLabelNom) texteLabelNom.textContent = estEntreprise ? 'Raison sociale' : 'Nom';
                if (champNom) {
                    champNom.classList.remove(colDefaut, colEntreprise);
                    champNom.classList.add(estEntreprise ? colEntreprise : colDefaut);
                }
                champsEntreprise.forEach(function (champ) {
                    champ.classList.toggle('d-none', ! estEntreprise);
                });
            }

            typeSelect.addEventListener('change', synchroniser);
            synchroniser();
        });
    });
</script>
