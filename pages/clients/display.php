<?php  $chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.php"); 
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include($chemin . "/inc/email.php");
	include($chemin . "/inc/db.php");
	
	$base = new Db();
	$base->Connect();
	
	function afficheDevis($id) {
		$sql = "select * from commandes c, paiements p where c.paiement_num=p.paiement_num and id='" . $id . "'";
		$rcc = $base->queryRow($sql);
if ($rcc) {
			$commande = montantCommande($rcc["id"]);
			$sql = "select * from commandes_produits cp, md_produits p, tailles t, marques m, categories c where cp.taille_num=t.taille_num and cp.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=c.categorie_num and id='" . $id . "'";
			$pp = $base->query($sql);
			foreach ($pp as $rpp) {
				$image_pdt = RecupPhotoProduit($rpp["produit_num"]);
				$prix_total_ttc = $rpp["montant_ttc"]*$rpp["qte"];
				//$prix_total_ttc = RecupPrixInit($rpp["produit_num"])*$rpp["qte"];
				switch ($rpp["commande_produit_remise_type"]) {
					case 1: // Remise en %
						$prix_total_ttc = $prix_total_ttc*(1-($rpp["commande_produit_remise"]/100));
					break;
					
					case 2: // Remise en euro
						$prix_total_ttc = $prix_total_ttc - $rpp["commande_produit_remise"];
					break;
				}			
				// On verifie les stocke pour chaque produit
				$sql = "select * from stocks where taille_num=" . $rpp["taille_num"] . " and produit_num=" . $rpp["produit_num"] . " and showroom_num='" . $u->mShowroom . "'";
				$ss = $base->query($sql);
				if ($rss=mysql_fetch_array($ss)) {
					$stock = $rss["stock_virtuel"];
				}
				else { // Pour tester tant qu'il n'y a pas de stock, on met 10...
					$stock = 10;
				}
				echo '<tr>
					<td><img src="' . $image_pdt["min"] . '" style="width:90px"/></td>
					<td>' . $rpp["categorie_nom"] . '<br>' . $rpp["marque_nom"] . '<br><strong>' . $rpp["produit_nom"] . '</strong></td>
					<td><select name="taille_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" id="taille_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" onChange="modifTaille(' . $id . ',' . $rpp["produit_num"] . ',' . $rpp["taille_num"] . ');">';
				echo '<option value="-1">A renseigner</option>';	
				$sql = "select * from tailles t, categories_tailles c where t.taille_num=c.taille_num and c.categorie_num=" . $rpp["categorie_num"];
				$ss = $base->query($sql);
				foreach ($ss as $st) {
					echo '<option value="' . $st["taille_num"] . '"';
					if ($st["taille_num"]==$rpp["taille_num"])
						echo " SELECTED";
					echo '>' . $st["taille_nom"] . '</option>';
				}
				echo '</select></td>
					<td>' . number_format($rpp["montant_ttc"],2,"."," ") . ' €</td>
					<td align="center"><select name="qte_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" id="qte_' . $rpp["produit_num"] . '_' . $rpp["taille_num"] . '" onChange="modifQte(' . $id . ',' . $rpp["produit_num"] . ',' . $rpp["taille_num"] . ');">';
					for ($i=0;$i<=$stock;$i++) {
						echo '<option value="' . $i . '"';
						if ($i==$rpp["qte"])
							echo " SELECTED";
						echo '>' . $i . '</option>';
					}
				echo '</select></td>
					<td>';
						if (number_format($prix_total_ttc,2)<=0)
							echo "OFFERT";
						else
							echo number_format($prix_total_ttc,2,"."," ") . ' €';
				echo '</td>
					<td><input type="text" name="remise_produit_' . $rpp["produit_num"] . '" id="remise_produit_' . $rpp["produit_num"] . '" value="' . $rpp["commande_produit_remise"] . '" class="form-inline input-xsmall"> 
					<select name="remise_type_produit_' . $rpp["produit_num"] . '" id="remise_type_produit_' . $rpp["produit_num"] . '" class="form-inline input-xsmall" onChange="remiseProduit(' . $rcc["id"] . ',' . $rpp["produit_num"] . ',' . $rpp["taille_num"]  . ')">
						<option value="0">--</option>
						<option value="1"';
					if ($rpp["commande_produit_remise_type"]==1) echo " SELECTED"; 
						echo '>%</option>
						<option value="2"';
					if ($rpp["commande_produit_remise_type"]==2) echo " SELECTED";
						echo '>€</option>
					</select>	
					</td>
				</tr>';
			} 
			echo '<tr>
					<td colspan="5" align="right"><strong>Total HT</strong></td>
					<td colspan="2">' . number_format($commande["commande_ht"],2,"."," ") . ' €</td>
				</tr>
				<tr>
					<td colspan="5" align="right"><strong>TVA (20%)</strong></td>
					<td colspan="2">' .  number_format($commande["commande_tva"],2,"."," ") . ' €</td>
				</tr>
				<tr>
					<td colspan="5" align="right"><strong>Total TTC</strong></td>
					<td colspan="2">' . number_format($commande["commande_ttc"],2,"."," ") . ' €</td>
				</tr>
				<tr>
					<td colspan="5" align="right"><strong>Remise</strong></td>
					<td colspan="2"><input type="text" name="remise_montant" id="remise_montant" value="' . $commande["commande_remise"] . '" class="form-inline input-xsmall"> 
						<select name="remise_type" id="remise_type" class="form-inline input-xsmall" onChange="remiseCommande(' . $rcc["id"] . ')">
							<option value="0">--</option>
							<option value="1"';
							if ($commande["commande_remise_type"]==1) echo " SELECTED"; 
								echo '>%</option>
							<option value="2"';
							if ($commande["commande_remise_type"]==2) echo " SELECTED";
								echo '>€</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="5" align="right"><strong>Total à payer</strong></td>
					<td colspan="2">';
				if ($commande["commande_remise_type"]!=0) {
					echo number_format($commande["commande_remise_ttc"],2,"."," ");
					$montant_a_payer = number_format($commande["commande_remise_ttc"],2,".","");
				}
				else {
					echo number_format($commande["commande_ttc"],2,"."," ");
					$montant_a_payer = number_format($commande["commande_ttc"],2,".","");
				}
				echo ' €	
					</td>
				</tr>';
			echo '<tr>
					<td colspan="5" align="right"><strong>Méthode de paiement</strong></td>
					<td colspan="2">
					<select name="paiement_' . $id . '" id="paiement_' . $id . '" onChange="modifPaiement(' . $id . ')">';
						$sql = "select * from paiements order by paiement_pos ASC";
						$pp = $base->query($sql);
						foreach ($pp as $rpp) {
							echo '<option value="' . $rpp["paiement_num"] . '"';
							if ($rpp["paiement_num"]==$rcc["paiement_num"])
								echo " SELECTED";
							echo '>' . $rpp["paiement_titre"] . '</option>';
						}
			echo '	</select>
					</td>
				</tr>';
			if ($rcc["paiement_nombre"]>1) { // ON affiche les acomptes
				$echeance = explode("/",$rcc["paiement_modele"]);
				$acompte_num = 1;
				foreach ($echeance as $val) {
					$acompte_val = number_format(($montant_a_payer*($val/100)),2,"."," ");
					echo '<tr>
							<td colspan="6" align="right"><strong>Acompte ' . $acompte_num . ' (' . $val . '%)</strong></td>
							<td>' . $acompte_val . ' €</td>
						</tr>';
					$acompte_num++;
				}
			}	
			echo '<tr><td colspan="7" align="right"><a href="/clients/client.php?client_num=' . crypte($rcc["client_num"]) . '&tab=tab_1_3" class="btn red">Fermer</a> <a href="/clients/client.php?client_num=' . crypte($rcc["client_num"]) . '&tab=tab_1_4&commande_passage=' . crypte($id) . '" class="btn blue" onClick="return confirme_commande(' . $rcc["id"] . ')">Passer la commande</a></td></tr>';
		}
	}
	
	function recalculMontantCommande($id) {
		$sql = "select * from commandes_produits cp, md_produits p, tva t where cp.produit_num=p.produit_num and p.tva_num=t.tva_num and id='" . $id . "'";
		$dd = $base->query($sql);
		
		$montant_total_ht = 0;
		$montant_total_tva = 0;
		$montant_total_ttc = 0;
		
		foreach ($dd as $rdd) {
			$prixProduit = RecupPrix($rdd["produit_num"]);
						
			
			if ($prixProduit["montant_remise_type"]==0) {
				$produit_montant_ht = $prixProduit["montant_ht"]*$rdd["qte"];
				$produit_montant_tva = $prixProduit["montant_tva"]*$rdd["qte"];
				$produit_montant_ttc = $prixProduit["montant_ttc"]*$rdd["qte"];
			} else {
				$produit_montant_ht = $prixProduit["montant_ht_remise"]*$rdd["qte"];
				$produit_montant_tva = $prixProduit["montant_tva_remise"]*$rdd["qte"];
				$produit_montant_ttc = $prixProduit["montant_ttc_remise"]*$rdd["qte"];
			}
			
			switch ($rdd["commande_produit_remise_type"]) {
				case 1: // Remise en %
					$produit_montant_ttc = $produit_montant_ttc*(1-($rdd["commande_produit_remise"]/100));
					$produit_montant_ht = $produit_montant_ttc/(1+($rdd["tva_taux"]/100));
					$produit_montant_tva = $produit_montant_ttc - $produit_montant_ht;
				break;
				
				case 2: // Remise en euro
					$produit_montant_ttc = $produit_montant_ttc - $rdd["commande_produit_remise"];
					$produit_montant_ht = $produit_montant_ttc/(1+($rdd["tva_taux"]/100));
					$produit_montant_tva = $produit_montant_ttc - $produit_montant_ht;
				break;
			}			
			
			$montant_total_ht += $produit_montant_ht;
			$montant_total_tva += $produit_montant_tva;
			$montant_total_ttc +=$produit_montant_ttc ;
		}
		
		// On upadte le montant
		$sql = "update commandes set commande_ht='" . $montant_total_ht . "', commande_tva='" . $montant_total_tva . "', commande_ttc='" . $montant_total_ttc . "' where id='" . $id . "'";
		$base->query($sql);
	}

	function recalculMontantCommande2023($id) {
		$sql = "select * from commandes_produits cp, md_produits p, tva t where cp.produit_num=p.produit_num and p.tva_num=t.tva_num and id='" . $id . "'";
		$dd = $base->query($sql);
		
		$montant_total_ht = 0;
		$montant_total_tva = 0;
		$montant_total_ttc = 0;
		
		foreach ($dd as $rdd) {
			//$prixProduit = RecupPrix($rdd["produit_num"]);
			$prixProduit = array(
				"montant_ht" =>$rdd["montant_ht"],
				"montant_tva" =>$rdd["montant_tva"],
				"montant_ttc" =>$rdd["montant_ttc"],
				"montant_ht_remise" =>$rdd["montant_ht_remise"],
				"montant_tva_remise" =>$rdd["montant_tva_remise"],
				"montant_ttc_remise" =>$rdd["montant_ttc_remise"],
			);
			
			if ($rdd["montant_remise_type"]==0) {
				$produit_montant_ht = $prixProduit["montant_ht"]*$rdd["qte"];
				$produit_montant_tva = $prixProduit["montant_tva"]*$rdd["qte"];
				$produit_montant_ttc = $prixProduit["montant_ttc"]*$rdd["qte"];
			} else {
				$produit_montant_ht = $prixProduit["montant_ht_remise"]*$rdd["qte"];
				$produit_montant_tva = $prixProduit["montant_tva_remise"]*$rdd["qte"];
				$produit_montant_ttc = $prixProduit["montant_ttc_remise"]*$rdd["qte"];
			}
			
			switch ($rdd["commande_produit_remise_type"]) {
				case 1: // Remise en %
					$produit_montant_ttc = $produit_montant_ttc*(1-($rdd["commande_produit_remise"]/100));
					$produit_montant_ht = $produit_montant_ttc/(1+($rdd["tva_taux"]/100));
					$produit_montant_tva = $produit_montant_ttc - $produit_montant_ht;
				break;
				
				case 2: // Remise en euro
					$produit_montant_ttc = $produit_montant_ttc - $rdd["commande_produit_remise"];
					$produit_montant_ht = $produit_montant_ttc/(1+($rdd["tva_taux"]/100));
					$produit_montant_tva = $produit_montant_ttc - $produit_montant_ht;
				break;
			}			
			
			$montant_total_ht += $produit_montant_ht;
			$montant_total_tva += $produit_montant_tva;
			$montant_total_ttc +=$produit_montant_ttc ;
		}
		
		// On upadte le montant
		$sql = "update commandes set commande_ht='" . $montant_total_ht . "', commande_tva='" . $montant_total_tva . "', commande_ttc='" . $montant_total_ttc . "' where id='" . $id . "'";
		$base->query($sql);
	}

	/*function recalculMontantCommandeDevis($id) {
		$sql = "select * from commandes_produits  where id='" . $id . "'";
		$dd = $base->query($sql);
		
		$montant_total_ht = 0;
		$montant_total_tva = 0;
		$montant_total_ttc = 0;
		
		foreach ($dd as $rdd) {
			$produit_montant_ht = $rdd["montant_ht"]*$rdd["qte"];
			$produit_montant_tva = $rdd["montant_tva"]*$rdd["qte"];
			$produit_montant_ttc = $rdd["montant_ttc"]*$rdd["qte"];
			
			
			$montant_total_ht += $produit_montant_ht;
			$montant_total_tva += $produit_montant_tva;
			$montant_total_ttc +=$produit_montant_ttc ;
		}
		
		// On upadte le montant
		$sql = "update commandes set commande_ht='" . $montant_total_ht . "', commande_tva='" . $montant_total_tva . "', commande_ttc='" . $montant_total_ttc . "' where id='" . $id . "'";
		$base->query($sql);
	}*/
	
	switch ($mode) {
		case 1 : // On insere un produit dans la sélection
			// On regarde si le produit n'est pas déjà dans la sélection
			$sql = "select * from selections_produits where selection_num='" . $selection . "' and produit_num='" . $pdt . "'";
			$cc = $base->query($sql);
			$nbr = count($cc);
			if ($nbr==0) { // On insere le produit
				$sql = "insert into selections_produits values('" . $selection . "','" . $pdt . "')";
				$base->query($sql);
			}
			// On affiche les produits de la sélection
			$sql = "select * from selections_produits s, md_produits p where s.produit_num=p.produit_num and selection_num='" . $selection . "'";
			$pp = $base->query($sql);
			$nbr_pp = count($pp);
			if ($nbr_pp>0) {
				echo '<div class="mt-element-card mt-element-overlay">';
				foreach ($pp as $rpp) {
					$sql = "select * from md_produits_photos where produit_num='" . $rpp["produit_num"] . "' and photo_pos=1";
					$ph = $base->query($sql);
					if ($rph=mysql_fetch_array($ph)) {
						$image_pdt = "/photos/produits/min/" . $rph["photo_chemin"];
					} else 
						$image_pdt = "http://www.placehold.it/50x50/EFEFEF/AAAAAA&amp;text=no+image";
					echo '<div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
								<div class="mt-card-item">
									<div class="mt-card-avatar mt-overlay-1">
										<figure style="height:100px;overflow:hidden;position:relative;line-height:100px;">
											<img src="' . $image_pdt . '" />
										</figure>
										<div class="mt-overlay">
											<ul class="mt-info">
												<li>
													<a class="btn default btn-outline" href="javascript:addWidget(' . $selection . ',' . $rpp["produit_num"] . ',2)">
														<i class="fa fa-trash"></i>
													</a>
												</li>
											</ul>
										</div>
									</div>
									<div class="mt-card-content">
										<h5><small>' . $rpp["produit_nom"] . '</small></h5>
									</div>
								</div>
							</div>';
					}
					echo '</div>';
				} else {
					echo '<p><i>Aucun produit dans votre sélection</i></p>';
				}
		break;
		
		case 2 : // On efface un produit de la sélection
			$sql = "delete from selections_produits where selection_num='" . $selection . "' and produit_num='" . $pdt . "'";
			$base->query($sql);
			
			// On affiche les produits de la sélection
			$sql = "select * from selections_produits s, md_produits p where s.produit_num=p.produit_num and selection_num='" . $selection . "'";
			$pp = $base->query($sql);
			$nbr_pp = count($pp);
			if ($nbr_pp>0) {
				echo '<div class="mt-element-card mt-element-overlay">';
				foreach ($pp as $rpp) {
					$sql = "select * from md_produits_photos where produit_num='" . $rpp["produit_num"] . "' and photo_pos=1";
					$ph = $base->query($sql);
					if ($rph=mysql_fetch_array($ph)) {
						$image_pdt = "/photos/produits/min/" . $rph["photo_chemin"];
					} else 
						$image_pdt = "http://www.placehold.it/50x50/EFEFEF/AAAAAA&amp;text=no+image";
					echo '<div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
							<div class="mt-card-item">
								<div class="mt-card-avatar mt-overlay-1">
									<figure style="height:100px;overflow:hidden;position:relative;line-height:100px;">
										<img src="' . $image_pdt . '" />
									</figure>
									<div class="mt-overlay">
										<ul class="mt-info">
											<li>
												<a class="btn default btn-outline" href="javascript:addWidget(' . $selection . ',' . $rpp["produit_num"] . ',2)">
													<i class="fa fa-trash"></i>
												</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="mt-card-content">
									<h5><small>' . $rpp["produit_nom"] . '</small></h5>
								</div>
							</div>
						</div>';
				}
				echo '</div>';
			} else {
				echo '<p><i>Aucun produit dans votre sélection</i></p>';
			}
		break;
		
		case 3: // On modifie la taille d'un produit dans un devis
			$sql = "update commandes_produits set taille_num='" . $taille_new . "' where id='" . $devis . "' and produit_num='" . $pdt  . "' and taille_num='" . $taille . "'";
			$base->query($sql);
		break;
		
		case 4 : // On modifie la qte d'un produit dans un devis
			if ($qte_new>0)
				$sql = "update commandes_produits set qte='" . $qte_new . "' where id='" . $devis . "' and produit_num='" . $pdt  . "' and taille_num='" . $taille . "'";
			else
				$sql = "delete from commandes_produits where id='" . $devis . "' and produit_num='" . $pdt  . "' and taille_num='" . $taille . "'";
			$base->query($sql);
			
			// On recalcul le montant de la commande
			recalculMontantCommande2023($devis);
			afficheDevis($devis);			
		break;
		
		case 5: // On modifie la methode de paiement du devis
			$sql = "update commandes set paiement_num='" . $paiement . "' where id='" . $devis . "'";
			$base->query($sql);
			afficheDevis($devis);
		break;
		
		case 6:
			// ON regarde si le produit n'est pas déjà dans le devis
			$sql = "select * from commandes_produits where id='" . $devis . "' and produit_num='" . $pdt . "' and taille_num=-1";
			$tt = $base->query($sql);
			$nbr = count($tt);
			if ($nbr==0) {
				$prixProduit = RecupPrix($pdt);
				$sql = "insert into commandes_produits values ('" . $devis . "','" . $pdt . "','-1',1,'" . $prixProduit["montant_ht"] . "','" . $prixProduit["montant_tva"] . "','" . $prixProduit["montant_ttc"] . "','" . $prixProduit["montant_remise"] . "','" . $prixProduit["montant_remise_type"] . "','" . $prixProduit["montant_ht_remise"] . "','" . $prixProduit["montant_tva_remise"] . "','" . $prixProduit["montant_ttc_remise"] . "','0','0')";
				$base->query($sql);
			}
			// On recalcul le montant de la commande
			recalculMontantCommande2023($devis);
			afficheDevis($devis);			
		break;
		
		case 7:
			// On upadte le montant
			$sql = "update commandes set commande_remise='" . $remise_montant . "', commande_remise_type='" . $remise_type . "' where id='" . $devis . "'";
			$base->query($sql);
			afficheDevis($devis);
		break;
		
		case 8:
			// On upadte le montant
			$sql = "update commandes_produits set commande_produit_remise='" . $remise_montant . "', commande_produit_remise_type='" . $remise_type . "' where id='" . $devis . "' and produit_num='" . $produit . "' and taille_num='" . $taille . "'";
			$base->query($sql);
			recalculMontantCommande2023($devis);
			afficheDevis($devis);
		break;
		
		case 9:
			// On upadte la commande fournisseur
			if ($val==1) {
				$sql = "insert into commandes_fournisseurs values('" . $id . "','1','" . Date("Y-m-d H:i:s") . "')";
				$base->query($sql);
				echo Date("d/m/Y");
			} else {
				$sql = "delete from commandes_fournisseurs where id='" . $id . "'";
				$base->query($sql);
				echo "";
			}			
		break;
		
		case 10 :
			$sql = "update commandes set commande_date='" . $date_commande . " 14:30:00' where id='" . $devis . "'";
			$base->query($sql);
		break;
	}
?>