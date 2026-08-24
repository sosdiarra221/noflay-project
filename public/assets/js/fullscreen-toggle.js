/**
 * Bouton d'agrandissement (plein écran navigateur) présent dans l'en-tête de chaque
 * module — utilise la Fullscreen API standard, avec repli silencieux si le navigateur
 * ne la supporte pas (ex: certains contextes iframe).
 */
(function () {
    function init() {
        const bouton = document.getElementById('toggleFullscreen');
        if (! bouton) return;

        const icone = bouton.querySelector('i');

        function estPleinEcran() {
            return !! (document.fullscreenElement || document.webkitFullscreenElement);
        }

        function mettreAJourIcone() {
            if (! icone) return;
            icone.classList.toggle('bi-arrows-fullscreen', ! estPleinEcran());
            icone.classList.toggle('bi-fullscreen-exit', estPleinEcran());
        }

        bouton.addEventListener('click', function () {
            if (! estPleinEcran()) {
                const element = document.documentElement;
                (element.requestFullscreen || element.webkitRequestFullscreen || function () {}).call(element);
            } else {
                (document.exitFullscreen || document.webkitExitFullscreen || function () {}).call(document);
            }
        });

        document.addEventListener('fullscreenchange', mettreAJourIcone);
        document.addEventListener('webkitfullscreenchange', mettreAJourIcone);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
