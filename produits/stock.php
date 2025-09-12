<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.php"); 
$titre_page = "Gestion des produits - Olympe Mariage";
$desc_page = "Gestion des produits - Olympe Mariage";

$nom_table = "produits";
$nom_champ = "produit";

$sql = "select * from md_produits p, categories c, marques m, stocks s, tailles t, prixachats pp where p.categorie_num=c.categorie_num and p.marque_num=m.marque_num and p.produit_num=s.produit_num and s.taille_num=t.taille_num and pp.prixachat_num=p.prixachat_num and showroom_num='" . $showroom . "'";
$sql .= " order by categorie_nom ASC, marque_nom ASC, produit_nom ASC, taille_pos ASC";
$cc = mysql_query($sql);
$nbr_produit = mysql_num_rows($cc);

while ($rcc=mysql_fetch_array($cc)) {
	echo $rcc["categorie_nom"] .";" . $rcc["marque_nom"] . ";" . trim($rcc["produit_nom"]) . ";" . $rcc["prixachat_montant"] . ";" . $rcc["taille_nom"] . ";" . $rcc["stock_reel"] . "\n";
}
