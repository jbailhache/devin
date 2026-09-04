<?php

/**
 * Ajoute des balises de mise en evidence autour des mots ajoutes ou modifies
 * entre deux versions d'une table HTML.
 *
 * $avant : code HTML de la table initiale
 * $apres : code HTML de la table modifiee
 * $debut : balise(s) ouvrante(s) a inserer, ex. "<font color=blue><u>"
 * $fin    : balise(s) fermante(s) a inserer, ex. "</u></font>"
 *
 * Le resultat est $apres avec les balises $debut / $fin autour de chaque
 * groupe de mots entiers ajoutes ou modifies. Les modifications situees a
 * l'interieur des balises HTML (attributs, styles, largeurs, ...) ne sont
 * jamais marquees, et les balises inserees ne se retrouvent jamais a
 * l'interieur du code d'une balise HTML.
 */
function ajouterBalisesModifications($avant, $apres, $debut, $fin)
{
    $tokensAvant = decouperHtmlEnJetons($avant);
    $tokensApres = decouperHtmlEnJetons($apres);

    // Les suites d'espaces sont exclues de la comparaison : elles ne portent
    // pas d'information et fausseraient l'alignement des mots.
    $clesAvant = clesSignificatives($tokensAvant);
    $clesApres = clesSignificatives($tokensApres);

    $conservesSignificatifs = jetonsConserves(
        array_values($clesAvant),
        array_values($clesApres)
    );

    $conserves = array();
    $k = 0;
    foreach ($tokensApres as $i => $jeton) {
        if (isset($clesApres[$i])) {
            $conserves[$i] = $conservesSignificatifs[$k];
            $k++;
        } else {
            $conserves[$i] = true;
        }
    }

    $resultat = '';
    $groupeOuvert = false;
    $enAttente = '';

    foreach ($tokensApres as $i => $jeton) {
        $type = typeJetonHtml($jeton);

        if ($type === 'espace') {
            if ($groupeOuvert) {
                $enAttente .= $jeton;
            } else {
                $resultat .= $jeton;
            }
            continue;
        }

        $ajoute = ($type === 'mot') && !$conserves[$i];

        if ($ajoute) {
            if ($groupeOuvert) {
                $resultat .= $enAttente;
            } else {
                $resultat .= $debut;
                $groupeOuvert = true;
            }
            $enAttente = '';
            $resultat .= $jeton;
        } else {
            if ($groupeOuvert) {
                $resultat .= $fin . $enAttente;
                $enAttente = '';
                $groupeOuvert = false;
            }
            $resultat .= $jeton;
        }
    }

    if ($groupeOuvert) {
        $resultat .= $fin;
    }
    $resultat .= $enAttente;

    return $resultat;
}

/**
 * Decoupe du code HTML en jetons : balises, mots et suites d'espaces.
 */
function decouperHtmlEnJetons($html)
{
    $jetons = array();
    if (preg_match_all('/<[^>]*>|\s+|[^<\s]+/', $html, $correspondances)) {
        $jetons = $correspondances[0];
    }
    return $jetons;
}

/**
 * Cles de comparaison des jetons significatifs (balises et mots),
 * indexees par la position du jeton dans le tableau d'origine.
 */
function clesSignificatives($jetons)
{
    $cles = array();
    foreach ($jetons as $i => $jeton) {
        if (typeJetonHtml($jeton) !== 'espace') {
            $cles[$i] = cleJetonHtml($jeton);
        }
    }
    return $cles;
}

/**
 * Type d'un jeton : "balise", "espace" ou "mot".
 */
function typeJetonHtml($jeton)
{
    if ($jeton === '') {
        return 'espace';
    }
    if ($jeton[0] === '<') {
        return 'balise';
    }
    if (trim($jeton) === '') {
        return 'espace';
    }
    return 'mot';
}

/**
 * Cle de comparaison d'un jeton. Deux balises de meme nom sont considerees
 * comme identiques meme si leurs attributs different : les modifications
 * internes aux balises ne doivent pas etre marquees.
 */
function cleJetonHtml($jeton)
{
    switch (typeJetonHtml($jeton)) {
        case 'balise':
            if (preg_match('/^<\s*(\/?)\s*([a-zA-Z0-9!\/-]*)/', $jeton, $m)) {
                return "\x00balise:" . $m[1] . strtolower($m[2]);
            }
            return "\x00balise:" . strtolower($jeton);
        default:
            return $jeton;
    }
}

/**
 * Determine, pour chaque jeton de $clesApres, s'il fait partie de la plus
 * longue sous-suite commune avec $clesAvant (donc s'il est inchange).
 * Retourne un tableau de booleens indexe comme $clesApres.
 */
function jetonsConserves($clesAvant, $clesApres)
{
    $n = count($clesAvant);
    $m = count($clesApres);

    $conserves = array_fill(0, max($m, 1), false);
    if ($m === 0) {
        return array();
    }
    if ($n === 0) {
        return $conserves;
    }

    // Programmation dynamique : longueurs de la plus longue sous-suite commune.
    $table = array();
    $table[$n] = array_fill(0, $m + 1, 0);
    for ($i = $n - 1; $i >= 0; $i--) {
        $ligne = array_fill(0, $m + 1, 0);
        for ($j = $m - 1; $j >= 0; $j--) {
            if ($clesAvant[$i] === $clesApres[$j]) {
                $ligne[$j] = $table[$i + 1][$j + 1] + 1;
            } else {
                $ligne[$j] = max($table[$i + 1][$j], $ligne[$j + 1]);
            }
        }
        $table[$i] = $ligne;
    }

    $i = 0;
    $j = 0;
    while ($i < $n && $j < $m) {
        if ($clesAvant[$i] === $clesApres[$j]) {
            $conserves[$j] = true;
            $i++;
            $j++;
        } elseif ($table[$i + 1][$j] >= $table[$i][$j + 1]) {
            $i++;
        } else {
            $j++;
        }
    }

    return $conserves;
}
