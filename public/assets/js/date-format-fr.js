/**
 * Transforme automatiquement tous les <input type="date"> du DOM en champ
 * texte affichant le format jj/mm/aaaa (via AirDatepicker), tout en
 * conservant un champ caché avec la valeur ISO (aaaa-mm-jj) réellement
 * soumise au serveur — évite de devoir modifier chaque formulaire un par un.
 */
(function () {
    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function isoToFr(iso) {
        var p = (iso || '').split('-');
        return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : '';
    }

    function frToIso(fr) {
        var m = (fr || '').trim().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        return m ? m[3] + '-' + m[2] + '-' + m[1] : '';
    }

    var localeFr = {
        days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        daysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
        daysMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
        months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        monthsShort: ['Jan', 'Fév', 'Mars', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
        today: "Aujourd'hui",
        clear: 'Effacer',
        dateFormat: 'dd/MM/yyyy',
        timeFormat: 'HH:mm',
        firstDay: 1,
    };

    function transformer(original) {
        original.setAttribute('data-date-fr-init', '1');

        var name = original.getAttribute('name');
        if (!name) {
            return;
        }

        var isoValue = original.value || '';
        var min = original.getAttribute('min');
        var max = original.getAttribute('max');

        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = name;
        hidden.value = isoValue;
        original.parentNode.insertBefore(hidden, original.nextSibling);
        original.removeAttribute('name');

        original.type = 'text';
        original.setAttribute('autocomplete', 'off');
        original.setAttribute('inputmode', 'numeric');
        if (!original.getAttribute('placeholder')) {
            original.setAttribute('placeholder', 'jj/mm/aaaa');
        }
        original.value = isoToFr(isoValue);

        var ADP = window.AirDatepicker && (window.AirDatepicker.default || window.AirDatepicker);
        if (ADP) {
            // eslint-disable-next-line no-new
            new ADP(original, {
                locale: localeFr,
                dateFormat: 'dd/MM/yyyy',
                autoClose: true,
                container: 'body',
                minDate: min || undefined,
                maxDate: max || undefined,
                onSelect: function (o) {
                    var d = Array.isArray(o.date) ? o.date[0] : o.date;
                    hidden.value = d ? (d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate())) : '';
                },
            });
        }

        original.addEventListener('input', function () {
            var iso = frToIso(original.value);
            if (iso || original.value.trim() === '') {
                hidden.value = iso;
            }
        });
    }

    function init() {
        document.querySelectorAll('input[type="date"]:not([data-date-fr-init])').forEach(transformer);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Certaines lignes (tableaux dynamiques, modales injectées) sont ajoutées après coup.
    document.addEventListener('shown.bs.modal', init);
})();
