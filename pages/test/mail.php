<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";

echo "=== DIAGNOSTIC PROBLÈME RÉEL ===<br><br>";

// Test avec votre cas exact : 83.3 * 1.20 qui donne soit 100 soit 99.96
$prix_ht = 83.3;
$taux_tva = 1.20;

echo "Cas problématique : $prix_ht * $taux_tva<br>";
echo "Calcul direct : " . ($prix_ht * $taux_tva) . "<br>";
echo "round() : " . round($prix_ht * $taux_tva, 2) . "<br>";

// Test si le problème vient d'ailleurs dans votre chaîne de calcul
$montant_ht = 83.3;
$tva_taux = 20;

// Reproduction de votre logique exacte
$montant_tva = $montant_ht * ($tva_taux / 100);
$montant_ttc = $montant_ht + $montant_tva;

echo "<br>Votre logique actuelle :<br>";
echo "HT: $montant_ht<br>";
echo "TVA 20%: $montant_tva<br>";
echo "TTC: $montant_ttc<br>";
echo "TTC arrondi: " . round($montant_ttc, 2) . "<br>";

// Vérifier si le problème vient de la provenance des données
echo "<br>Test avec données de BDD (simulé) :<br>";
$prix_depuis_bdd = "83.30"; // Comme venant de MySQL
$prix_float = (float)$prix_depuis_bdd;
echo "Prix string: '$prix_depuis_bdd'<br>";
echo "Prix float: $prix_float<br>";
echo "Calcul: " . ($prix_float * 1.20) . "<br>";
echo "Arrondi: " . round($prix_float * 1.20, 2) . "<br>";
?>
?>