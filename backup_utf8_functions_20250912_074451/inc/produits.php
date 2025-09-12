<?
	function RecupPrix($produit) {
		$sql = "select * from md_produits p, prix pp, tva t where p.prix_num=pp.prix_num and p.tva_num=t.tva_num and produit_num='" . $produit . "'";
		$pp = mysql_query($sql);
		if ($rpp = mysql_fetch_array($pp)) {
			$prix_ht = $rpp["prix_montant_ht"];
			$remise = $rpp["produit_remise_type"];
			$remise_montant = $rpp["produit_montant_remise"];
			$tva_taux = $rpp["tva_taux"];
			$montant_tva = $rpp["prix_montant_ht"]*($rpp["tva_taux"]/100);
			$prix_ttc = round($prix_ht + $montant_tva);
			
			$prix_ht_remise = 0;
			$montant_tva_remise = 0;
			$prix_ttc_remise = 0;
			// On regarde si il y a une remise 			
			switch ($rpp["produit_remise_type"]) {
				case 1: // Remise en %
					$prix_ht_remise = $prix_ht*(1-($remise_montant/100));
					$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
					$prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
				break;
				
				case 2: // Remise en euro
					$prix_ht_remise = $prix_ht - $remise;
					$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
					$prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
				break;
			}			
			
			$prix = array (
				'montant_ht' 			=> $prix_ht,
				'montant_remise_type'	=> $remise,
				'montant_remise'		=> $remise_montant,
				'tva_taux'				=> $tva_taux,
				'montant_tva'			=> $montant_tva,
				'montant_ttc'			=> $prix_ttc,
				'montant_ht_remise'		=> $prix_ht_remise,
				'montant_tva_remise'	=> $montant_tva_remise,
				'montant_ttc_remise'	=> $prix_ttc_remise
			);
			
			return $prix;
		} else
			return false;
	}
	
	function AffichePrix($produit) {
		$sql = "select * from md_produits p, prix pp, tva t where p.prix_num=pp.prix_num and p.tva_num=t.tva_num and produit_num='" . $produit . "'";
		$pp = mysql_query($sql);
		if ($rpp = mysql_fetch_array($pp)) {
			$prix_ht = $rpp["prix_montant_ht"];
			$remise = $rpp["produit_remise_type"];
			$remise_montant = $rpp["produit_montant_remise"];
			$tva_taux = $rpp["tva_taux"];
			$montant_tva = $rpp["prix_montant_ht"]*($rpp["tva_taux"]/100);
			$prix_ttc = round($prix_ht + $montant_tva);
			
			if ($remise==0) {
				$prix = number_format($prix_ttc,2) . " &euro;";
			} else {
				switch ($rpp["produit_remise_type"]) {
					case 1: // Remise en %
						$prix_ht_remise = $prix_ht*(1-($remise_montant/100));
						$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
						$prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
					break;
					
					case 2: // Remise en euro
						$prix_ht_remise = $prix_ht - $remise;
						$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
						$prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
					break;
				}
				$prix = '<strike>' . number_format($prix_ttc,2,"."," ") . ' &euro;</strike> ' . number_format($prix_ttc_remise,2,"."," ") . ' &euro;';
			}
			return $prix;
		} else
			return false;
	}
	
	function AffichePrixHT($produit) {
		$sql = "select * from md_produits p, prix pp, tva t where p.prix_num=pp.prix_num and p.tva_num=t.tva_num and produit_num='" . $produit . "'";
		$pp = mysql_query($sql);
		if ($rpp = mysql_fetch_array($pp)) {
			$prix_ht = $rpp["prix_montant_ht"];
			$remise = $rpp["produit_remise_type"];
			$remise_montant = $rpp["produit_montant_remise"];
			$tva_taux = $rpp["tva_taux"];
			$montant_tva = $rpp["prix_montant_ht"]*($rpp["tva_taux"]/100);
			$prix_ttc = round($prix_ht + $montant_tva);
			
			if ($remise==0) {
				$prix = number_format($prix_ht,2) . " &euro;";
			} else {
				switch ($rpp["produit_remise_type"]) {
					case 1: // Remise en %
						$prix_ht_remise = $prix_ht*(1-($remise_montant/100));
						$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
						//$prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
					break;
					
					case 2: // Remise en euro
						$prix_ht_remise = $prix_ht - $remise;
						$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
						$prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
					break;
				}
				$prix = '<strike>' . number_format($prix_ht,2,"."," ") . ' &euro;</strike> ' . number_format($prix_ht_remise,2,"."," ") . ' &euro;';
			}
			return $prix;
		} else
			return false;
	}
	
	function AffichePrixTVA($produit) {
		$sql = "select * from md_produits p, prix pp, tva t where p.prix_num=pp.prix_num and p.tva_num=t.tva_num and produit_num='" . $produit . "'";
		$pp = mysql_query($sql);
		if ($rpp = mysql_fetch_array($pp)) {
			$prix_ht = $rpp["prix_montant_ht"];
			$remise = $rpp["produit_remise_type"];
			$remise_montant = $rpp["produit_montant_remise"];
			$tva_taux = $rpp["tva_taux"];
			$montant_tva = $rpp["prix_montant_ht"]*($rpp["tva_taux"]/100);
			//$prix_ttc = round($prix_ht + $montant_tva);
			
			if ($remise==0) {
				$prix = number_format($montant_tva,2) . " &euro;";
			} else {
				switch ($rpp["produit_remise_type"]) {
					case 1: // Remise en %
						$prix_ht_remise = $prix_ht*(1-($remise_montant/100));
						$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
					break;
					
					case 2: // Remise en euro
						$prix_ht_remise = $prix_ht - $remise;
						$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
					break;
				}
				$prix = number_format($montant_tva_remise,2,"."," ") . ' &euro;';
			}
			return $prix;
		} else
			return false;
	}
	
	function RecupPrixInit($produit) {
		$sql = "select * from md_produits p, prix pp, tva t where p.prix_num=pp.prix_num and p.tva_num=t.tva_num and produit_num='" . $produit . "'";
		$pp = mysql_query($sql);
		if ($rpp = mysql_fetch_array($pp)) {
			$prix_ht = $rpp["prix_montant_ht"];
			$remise = $rpp["produit_remise_type"];
			$remise_montant = $rpp["produit_montant_remise"];
			$tva_taux = $rpp["tva_taux"];
			$montant_tva = $rpp["prix_montant_ht"]*($rpp["tva_taux"]/100);
			$prix_ttc = round($prix_ht + $montant_tva);
			
			$prix_ht_remise = 0;
			$montant_tva_remise = 0;
			$prix_ttc_remise = 0;
			// On regarde si il y a une remise 			
			switch ($rpp["produit_remise_type"]) {
				case 1: // Remise en %
					$prix_ht_remise = $prix_ht*(1-($remise_montant/100));
					$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
					$prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
				break;
				
				case 2: // Remise en euro
					$prix_ht_remise = $prix_ht - $remise;
					$montant_tva_remise = $prix_ht_remise * ($rpp["tva_taux"]/100);
					$prix_ttc_remise = $prix_ht_remise + $montant_tva_remise;
				break;
			}			
			
			if ($remise==0)
				$prix = $prix_ttc;
			else
				$prix = $prix_ttc_remise;
			
			return $prix;
		} else
			return false;
	}
	
	function RecupPhotoProduit($produit) {
		$sql = "select * from md_produits_photos where produit_num='" . $produit . "' and photo_pos=1";
		$ph = mysql_query($sql);
		if ($rph=mysql_fetch_array($ph)) {
			$photo = array(
				'min'		=> "/photos/produits/min/" . $rph["photo_chemin"],
				'norm'		=> "/photos/produits/norm/" . $rph["photo_chemin"],
				'zoom'		=> "/photos/produits/zoom/" . $rph["photo_chemin"],
			);
		} else {
			$photo = array(
				'min'		=> "http://www.placehold.it/50x50/EFEFEF/AAAAAA&amp;text=no+image",
				'norm'		=> "http://www.placehold.it/500x500/EFEFEF/AAAAAA&amp;text=no+image",
				'zoom'		=> "http://www.placehold.it/1000x1000/EFEFEF/AAAAAA&amp;text=no+image",
			);
		}
		return $photo;
	}
	
	function montantCommande($id) {
		$sql = "select * from commandes where id='" . $id . "'";
		$co = mysql_query($sql);
		if ($rco=mysql_fetch_array($co)) {
			$commande_remise_ttc = 0;
			$remise = 0;
			if ($rco["commande_remise_type"]!=0) {
				switch ($rco["commande_remise_type"]) {
					case 1:
						$commande_remise_ttc = $rco["commande_ttc"]*(1-($rco["commande_remise"]/100));
						$remise = '-' . $rco["commande_remise"] . "%";
					break;
					
					case 2:
						$commande_remise_ttc = $rco["commande_ttc"] - $rco["commande_remise"];
						$remise = '-' . $rco["commande_remise"] . "€";
					break;
				}
			}
			$montant = array (
				'commande_ht'			=> $rco["commande_ht"],
				'commande_tva'			=> $rco["commande_tva"],
				'commande_ttc'			=> $rco["commande_ttc"],
				'commande_remise_type'	=> $rco["commande_remise_type"],
				'commande_remise'		=> $rco["commande_remise"],
				'remise'				=> $remise,
				'commande_remise_ttc'	=> $commande_remise_ttc
			);
			return $montant;
		} else 
			return false;
	}
	
	function montantCommandeTTC($id) {
		$sql = "select * from commandes where id='" . $id . "'";
		$co = mysql_query($sql);
		if ($rco=mysql_fetch_array($co)) {
			if ($rco["commande_remise_type"]==0)
				return $rco["commande_ttc"];
			else {
				switch ($rco["commande_remise_type"]) {
					case 1:
						$montant = $rco["commande_ttc"]*(1-($rco["commande_remise"]/100));
					break;
					
					case 2:
						$montant = $rco["commande_ttc"] - $rco["commande_remise"];
					break;
				}
				return $montant;
			}
		} else 
			return false;
	}
	
	function resteAPayerCommande($id) {
		$montantTTC = montantCommandeTTC($id);
		$sql = "select sum(paiement_montant) val from commandes_paiements where id='" . $id . "'";
		$pp = mysql_query($sql);
		if ($rpp=mysql_fetch_array($pp)) {
			$paye = $rpp["val"];
			$reste_a_payer = $montantTTC - $paye;
			if ($reste_a_payer<0)
				$reste_a_payer = 0;
		} else
			$reste_a_payer = 0;
		
		return $reste_a_payer;
	}
	
	
	function montantCommandeHT($id) {
		$sql = "select * from commandes where id='" . $id . "'";
		$co = mysql_query($sql);
		if ($rco=mysql_fetch_array($co)) {
			if ($rco["commande_remise_type"]==0)
				return $rco["commande_ht"];
			else {
				switch ($rco["commande_remise_type"]) {
					case 1:
						$montant = $rco["commande_ht"]*(1-($rco["commande_remise"]/100));
					break;
					
					case 2:
						$montant = $rco["commande_ht"] - $rco["commande_remise"];
					break;
				}
				return $montant;
			}
		} else 
			return false;
	}
	
	function majStockWeb($id) {
		
		global $base;
		// On recupere les produits de la commande pour les enlever du stock
		$sql = "select * from commandes_produits cp, md_produits p where cp.produit_num=p.produit_num and id='" . decrypte($id) . "'";
		$co = mysql_query($sql);
		$produits = array();
		while ($rco=mysql_fetch_array($co)) {
			$produit = array(
				'ref'		=> $rco["produit_ref"],
				'taille'	=> $rco["taille_num"],
				'qte'		=> $rco["qte"]
			);
			array_push($produits,$produit);
		}					
		$base->Deconnect();
		
		// On se connecte à la base SHOP
		$bddshop = new DbShop();
		$bddshop->Connect();
		
		
		foreach ($produits as $pp) {
			$sql = "select * from md_produits p, md_stocks s where p.produit_num=s.produit_num and produit_ref='" . $pp["ref"] . "' and taille_num='" . $pp["taille"] . "'";
			$ss = mysql_query($sql);
			
			if ($rss = mysql_fetch_array($ss)) {
				$stock_virtuel = $rss["stock_virtuel"] - $pp["qte"];
				$stock_reel = $rss["stock_reel"] - $pp["qte"];
				
				$sql = "update md_stocks set stock_virtuel='" . $stock_virtuel . "', stock_reel='" . $stock_reel . "' where produit_num='" . $rss["produit_num"] . "' and taille_num='" . $rss["taille_num"] . "'";
				mysql_query($sql);
			}
		}
		$bddshop->Deconnect();
		
		// On reconnect la base CRM
		$base = new Db();
		$base->Connect();
	}
?>