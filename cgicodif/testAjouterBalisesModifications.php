<?php

require_once __DIR__ . '/ajouterBalisesModifications.php';

$nbTests = 0;
$nbEchecs = 0;

function verifier($nom, $avant, $apres, $debut, $fin, $attendu)
{
    global $nbTests, $nbEchecs;

    $nbTests++;
    $obtenu = ajouterBalisesModifications($avant, $apres, $debut, $fin);
    if ($obtenu === $attendu) {
        echo "OK   : $nom\n";
    } else {
        $nbEchecs++;
        echo "ECHEC: $nom\n";
        echo "  attendu : $attendu\n";
        echo "  obtenu  : $obtenu\n";
    }
}

verifier(
    'mots modifies dans une cellule',
    '<table><tr><td>aaa bbb ccc ddd eee fff ggg</td></tr></table>',
    '<table><tr><td>aaa xxx ccc ddd yyy eee ggg</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa <u>xxx</u> ccc ddd <u>yyy</u> eee ggg</td></tr></table>'
);

verifier(
    'modification a l interieur d une balise',
    '<table><tr><th width=20>aaa</th></tr><tr><td>bbb</td></tr></table>',
    '<table><tr><th width=30>aaa</th></tr><tr><td>bbb</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><th width=30>aaa</th></tr><tr><td>bbb</td></tr></table>'
);

verifier(
    'aucune modification',
    '<table><tr><td>aaa bbb</td></tr></table>',
    '<table><tr><td>aaa bbb</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa bbb</td></tr></table>'
);

verifier(
    'balises de mise en evidence multiples',
    '<table><tr><td>aaa bbb ccc</td></tr></table>',
    '<table><tr><td>aaa xxx ccc</td></tr></table>',
    '<font color=blue><u>',
    '</u></font>',
    '<table><tr><td>aaa <font color=blue><u>xxx</u></font> ccc</td></tr></table>'
);

verifier(
    'plusieurs mots consecutifs modifies : un seul groupe',
    '<table><tr><td>aaa bbb ccc ddd</td></tr></table>',
    '<table><tr><td>aaa xxx yyy ddd</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa <u>xxx yyy</u> ddd</td></tr></table>'
);

verifier(
    'mots ajoutes',
    '<table><tr><td>aaa ddd</td></tr></table>',
    '<table><tr><td>aaa bbb ccc ddd</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa <u>bbb ccc</u> ddd</td></tr></table>'
);

verifier(
    'mots supprimes : rien a marquer',
    '<table><tr><td>aaa bbb ccc ddd</td></tr></table>',
    '<table><tr><td>aaa ddd</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa ddd</td></tr></table>'
);

verifier(
    'ligne inseree',
    '<table><tr><td>aaa</td></tr><tr><td>ccc</td></tr></table>',
    '<table><tr><td>aaa</td></tr><tr><td>bbb</td></tr><tr><td>ccc</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa</td></tr><tr><td><u>bbb</u></td></tr><tr><td>ccc</td></tr></table>'
);

verifier(
    'ligne supprimee',
    '<table><tr><td>aaa</td></tr><tr><td>bbb</td></tr><tr><td>ccc</td></tr></table>',
    '<table><tr><td>aaa</td></tr><tr><td>ccc</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa</td></tr><tr><td>ccc</td></tr></table>'
);

verifier(
    'colonne inseree',
    '<table><tr><td>aaa</td><td>ccc</td></tr><tr><td>ddd</td><td>fff</td></tr></table>',
    '<table><tr><td>aaa</td><td>bbb</td><td>ccc</td></tr>'
        . '<tr><td>ddd</td><td>eee</td><td>fff</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa</td><td><u>bbb</u></td><td>ccc</td></tr>'
        . '<tr><td>ddd</td><td><u>eee</u></td><td>fff</td></tr></table>'
);

verifier(
    'colonne supprimee',
    '<table><tr><td>aaa</td><td>bbb</td><td>ccc</td></tr></table>',
    '<table><tr><td>aaa</td><td>ccc</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa</td><td>ccc</td></tr></table>'
);

verifier(
    'modification du texte et du style simultanement',
    '<table><tr><td width=10>aaa bbb</td></tr></table>',
    '<table><tr><td width=50 style="color:red">aaa xxx</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td width=50 style="color:red">aaa <u>xxx</u></td></tr></table>'
);

verifier(
    'espaces et retours a la ligne preserves',
    "<table>\n<tr><td>aaa bbb</td></tr>\n</table>",
    "<table>\n<tr><td>aaa   xxx</td></tr>\n</table>",
    '<u>',
    '</u>',
    "<table>\n<tr><td>aaa   <u>xxx</u></td></tr>\n</table>"
);

verifier(
    'cellule entierement nouvelle en fin de ligne',
    '<table><tr><td>aaa</td></tr></table>',
    '<table><tr><td>aaa</td><td>bbb ccc</td></tr></table>',
    '<u>',
    '</u>',
    '<table><tr><td>aaa</td><td><u>bbb ccc</u></td></tr></table>'
);

echo "\n$nbTests tests, $nbEchecs echec(s).\n";
exit($nbEchecs === 0 ? 0 : 1);
