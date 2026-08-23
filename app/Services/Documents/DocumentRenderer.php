<?php

namespace App\Services\Documents;

/**
 * Moteur de rendu — substitution SÛRE et littérale de {{ chemin.variable }} dans du HTML.
 *
 * Règles non négociables (cf. spécification) :
 *  - jamais d'eval() ni d'exécution de PHP/Blade arbitraire depuis le contenu du modèle ;
 *  - jamais d'appel de méthode arbitraire sur un objet à partir de la chaîne du modèle ;
 *  - seuls les chemins CONNUS du DocumentVariableRegistry (présents dans $context) sont remplacés ;
 *  - un chemin inconnu ou mal formé est laissé tel quel dans le HTML produit.
 */
class DocumentRenderer
{
    /**
     * @param  string  $html  Contenu HTML du modèle ou du document (source de vérité).
     * @param  array<string,string>  $context  Chemin => valeur texte, tel que produit par
     *                                          DocumentVariableRegistry::contexte().
     */
    public function render(string $html, array $context): string
    {
        return preg_replace_callback(
            '/\{\{\s*([a-z_]+\.[a-z_]+)\s*\}\}/i',
            function (array $matches) use ($context) {
                $chemin = strtolower($matches[1]);

                if (! array_key_exists($chemin, $context)) {
                    // Chemin inconnu ou mal formé : on laisse le texte original intact.
                    return $matches[0];
                }

                return e((string) $context[$chemin]);
            },
            $html
        ) ?? $html;
    }

    /**
     * Liste les chemins {{ ... }} effectivement présents dans un contenu HTML (pour information/debug),
     * qu'ils soient connus du registre ou non.
     */
    public function extraireChemins(string $html): array
    {
        preg_match_all('/\{\{\s*([a-z_]+\.[a-z_]+)\s*\}\}/i', $html, $matches);

        return array_values(array_unique(array_map('strtolower', $matches[1] ?? [])));
    }
}
