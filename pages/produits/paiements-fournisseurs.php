<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Commandes Fournisseurs - Olympe Mariage";
$desc_page = "Commandes Fournisseurs - Olympe Mariage";
  
  $mois_nom = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre");
  $mois_jour = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
  
  if (!isset($showroom)) {
		if ($u->mShowroom==0)
			$showroom=1;
		else
			$showroom = $u->mShowroom;
	}
  
  if (!isset($mois))
	$mois = 0;

  if (!isset($categorie))
	$categorie = -1;

 if (!isset($produitauto)) {
	$produitauto = "";
	$produit_num = 0;
 } else {
	 $tabproduit = recupValeurEntreBalise($produitauto,"[","]");
	 $produit_num = $tabproduit[0];
 }
 
 if (!isset($marques))
	$marques = -1;

  if (!isset($annee))
	$annee = 0;

  if (($mois!=0) && ($annee!=0)) {
	  $date_deb = $annee . "-" . $mois . "-1";
	  $date_fin = $annee . "-" . $mois . "-" . $mois_jour[intval($mois)];
  } else if (($mois==0) && ($annee>0)) {
	  $date_deb = $annee . "-01-01";
	  $date_fin = $annee . "-12-31";
  } else {
	 // On calcul l'année en cours
	$mois_deb = 8;
	$mois_encours = Date("n");
	if ($mois_encours<9) 
		$annee_deb = Date("Y")-1;
	else
		$annee_deb = Date("Y");
	
	$annee_fin = $annee_deb+1;
	
	$date_deb = $annee_deb . "-09-01";
	$date_fin = $annee_fin . "-08-31";
  }
  
  $date_deb .= " 00:00:00";
  $date_fin .= " 23:59:59";
?>

<?php include TEMPLATE_PATH . 'head.php'; ?>
    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <?php include TEMPLATE_PATH . 'top.php'; ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN BREADCRUMBS -->
                    <div class="breadcrumbs">
                        <h1>Olympe Mariage</h1>
                        <ol class="breadcrumb">
                            <li>
                                <a href="/home">Accueil</a>
                            </li>
                            <li class="active">Commandes Fournisseurs</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-question font-red-sunglo"></i>
											<span class="caption-subject bold uppercase"> Recherche</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="recherche" method="POST" action="<?= form_action_same() ?>">
									<input type="hidden" name="recherche" value="ok">
									<table class="table table-striped table-bordered table-advance table-hover">
										<thead>
											<tr>
												<th>Produits</th>
												<th>Catégories</th>
												<th>Marques</th>
												<th>Date</th>
												<?php if ($u->mGroupe==0) { ?>
													<th>Showroom</th>
												<?php } ?>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><input id="produitauto" name="produitauto"  class="form-control" value="<?= $produitauto ?>"></td>
												<td>
													<select name="categorie" class="form-control">
														<option value="-1">------------</option>
														<?php															$sql = "select * from categories";
															$cc = $base->query($sql);
															foreach ($cc as $rcc) {
																echo '<option value="' . $rcc["categorie_num"] . '"';
																if ($rcc["categorie_num"]==$categorie)
																	echo " SELECTED";
																echo ">" . $rcc["categorie_nom"] . "</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select name="marques" class="form-control">
														<option value="-1">------------</option>
														<?php															$sql = "select * from marques";
															$cc = $base->query($sql);
															foreach ($cc as $rcc) {
																echo '<option value="' . $rcc["marque_num"] . '"';
																if ($rcc["marque_num"]==$marques)
																	echo " SELECTED";
																echo ">" . $rcc["marque_nom"] . "</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select name="mois" class="form-inline" style="height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;">
														<option value="0">--</option>
														<?php 
														for ($i=1;$i<13;$i++) {
															echo "<option value=\"" . sprintf($i,"%02d") . "\"";
															if (sprintf($i,"%02d")==$mois)
																echo " SELECTED";
															echo ">" . $mois_nom[$i] . "</option>\n";
														}
														?>		
													</select>
													<select name="annee" class="form-inline" style="height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;">
														<option value="0">----</option>
														<?php 
														for ($i=Date("Y");$i>2015;$i--) {
															echo "<option value=\"" .$i . "\"";
															if ($i==$annee)
																echo " SELECTED";
															echo ">" . $i . "</option>\n";
														}
														?>		
													</select>
												</td>
												<?php if ($u->mGroupe==0) { ?>
													<td>
														<select name="showroom" class="form-control input-medium">
														<?php															$sql = "select * from showrooms order by showroom_nom ASC";
															$tt = $base->query($sql);
															foreach ($tt as $rtt) {
																echo '<option value="' . $rtt["showroom_num"] . '"';
																if ($rtt["showroom_num"]==$showroom) echo " SELECTED";
																echo '>' . $rtt["showroom_nom"] . '</option>';
															}
														?>
														</select>
													</td>
												<?php } ?>
												<td><input type="submit" value="Rechercher" class="btn blue"></td>
											</tr>
										</tbody>
									</table>
									</form>
								</div>
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="portlet box red">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-black-tie"></i>Commandes Forunisseurs </div>
									</div>
									<div class="portlet-body">
										<table class="table table-striped table-bordered table-advance table-hover">
											<thead>
												<tr>
													<th>Date</th>
													<th>Fournisseur</th>
													<th>Catégorie</th>
													<th>Produit</th>
													<th>Montant TTC</th>
													<th>Date Pt cde</th>
													<th>Paiement cde</th>
													<th>Date Pt rec.</th>
													<th>Paiement rec.</th>
													<th>Reste à Payer</th>
													<th>Client</th>
													<th>Commande</th>
													<th>Commande Date</th>
												</tr>
											</thead>
											<tbody>
											<?php											  $nbr = 0;
											  $sql = "select * from commandes c, commandes_fournisseurs f, md_produits p, marques m, categories ca, clients cl where c.id=f.id and f.produit_num=p.produit_num and p.marque_num=m.marque_num and p.categorie_num=ca.categorie_num and c.client_num=cl.client_num and c.showroom_num='" . $showroom . "'";
											  if ($categorie!=-1)
												  $sql .= " and p.categorie_num=" . $categorie;
											  if ($marques!=-1)
												  $sql .= " and p.marque_num=" . $marques;
											  if ($produitauto!="")
												  $sql .= " and produit_nom like '%" . $produitauto . "%'";
											  $sql .= " and commande_fournisseur_date>='" . $date_deb . "' and commande_date<='" . $date_fin . "'";
											  $sql .= " ORDER BY commande_fournisseur_date DESC ";
											  $re = $base->query($sql);
											  $montant_total_ttc = 0;
											  $paiement_total = 0;
											  $paiement_total1 = 0;
											  $reste_total1 = 0;
											  $paiement_total2 = 0;
											  $reste_total2 = 0;
											  $reste_total = 0;
											  foreach ($re as $row) {
												$nbr++;
												$paiement = 0;
												$reste = 0;
												// On regarde les paiements
												$paiement1_payer = 0;
												$paiement2_payer = 0;
												$paiement1_val = 0;
												$paiement2_val = 0;
												if ($row["marque_paiement"]!="") {
													$pos = strpos($row["marque_paiement"],"/");
													if ($pos>0) {
														$paiement_reception = 1;
														$pourcent1 = substr($row["marque_paiement"],0,$pos);
														$pourcent2 = substr($row["marque_paiement"],$pos+1);
														if ($pourcent1==0)
															$paiement_commande = 0;
														else
															$paiement_commande = 1;
													} else {
														$paiement_commande = 0;
														$paiement_reception = 1;
														$pourcent1 = 0;
														$pourcent2 = 100;
													}
												} else {
													$paiement_commande = 0;
													$paiement_reception = 1;
													$pourcent1 = 0;
													$pourcent2 = 100;
												}
												//echo "[" . $pourcent1 . " - " . $pourcent2 . "]";
												if ($row["commande_montant"]!=0) {
													if ($pourcent1!=0) {
														$paiement1_val = ($row["commande_montant"]*$pourcent1)/100;
														$paiement2_val = ($row["commande_montant"]*$pourcent2)/100;
													} else {
														$paiement1_val = 0;
														$paiement2_val = $row["commande_montant"];
													}
												}
												//echo "[" . $paiement1_val . "€ - " . $paiement2_val . "€] ----- ";
												 // On regarde les paiements
												 $sql = "select * from commandes_fournisseurs_paiements where id='" . $row["id"] . "' and produit_num='" . $row["produit_num"] . "'";
												 $rpa = $base->queryRow($sql);
												 if ($rpa) {
													 $paiement1_payer = $rpa["paiement1"];
													 $paiement1_payer_date = $rpa["paiement1_date"];
													 $paiement2_payer = $rpa["paiement2"];
													 $paiement2_payer_date = $rpa["paiement2_date"];
												 }
												 
												 $date_reception = "N.R.";
												 // On regarde la date de reception de la robe
												 $sql = "select * from rendez_vous where type_num=2 and client_num='" . $row["client_num"] . "'";
												 $rrr = $base->queryRow($sql);
												if ($rrr) {
													$date_reception = format_date($rrr["rdv_date"],11,1);
												 }
												 
												 $reste = $row["commande_montant"] - $paiement1_payer - $paiement2_payer;
												 $montant_total_ttc += $row["commande_montant"];
												 $paiement_total += $paiement1_payer + $paiement2_payer;
												 $paiement_total1 += $paiement1_val;
												 $paiement_total2 += $paiement2_val;
												 if ($paiement1_payer==0)
													 $reste_total1 += $paiement1_val;
												if ($paiement2_payer==0)
													 $reste_total2 += $paiement2_val;
											  	 $reste_total += $reste;
											?>
												<tr>
													<td><small><?= format_date($row["commande_fournisseur_date"],11,1) ?></small></td>
													<td><small><?= $row["marque_nom"] ?></small></td>
													<td><small><?= $row["categorie_nom"] ?></small></td>
													<td><small><?= $row["produit_nom"] ?></small></td>
													<td><small><?= safe_number_format($row["commande_montant"],2,'.',' ') ?>€</small></td>
													<td><small><?php 
														if ($paiement_commande==1) { 
															if ($paiement1_payer==0)
																echo format_date($row["commande_fournisseur_date"],11,1); 
															else 
																echo format_date($paiement1_payer_date,11,1); 
														}
														?>
														</small>
													</td>
													<td><small>
													<?php 
														if ($paiement_commande==1) {
															$paid      = (float)($paiement1_payer ?? 0);
															$isPaid    = $paid > 0;
															$display   = $isPaid ? $paid : (float)$paiement1_val; // ce qu'on affiche
															$checked   = $isPaid ? ' checked' : '';               // état de la case

															echo safe_number_format($display, 2, '.', ' ') . ' € ';
															echo '<input type="checkbox"
																	class="js-paiement"
																	id="paiement1_' . (int)$row['id'] . '"
																	data-id="' . (int)$row['id'] . '"
																	data-produit="' . (int)$row['produit_num'] . '"
																	data-val="' . (float)$paiement1_val . '"
																	data-paiement="1"' . $checked . '>';
														}
													?>
													</small></td>
													<td><small><?php 
														if ($paiement2_payer==0)
															echo $date_reception;
														else 
															echo format_date($paiement2_payer_date,11,1); 
													
													?></small></td>
													<td><small>
													<?php 
														if ($paiement_reception==1) {
															$paid       = (float)($paiement2_payer ?? 0);
															$isPaid     = $paid > 0;
															$display    = $isPaid ? $paid : (float)$paiement2_val;   // montant affiché
															$canCheck   = ($date_reception ?? '') !== 'NR';          // on autorise la case seulement si réception ok
															$checked    = $isPaid ? ' checked' : '';

															echo safe_number_format($display, 2, '.', ' ') . ' € ';
															if ($canCheck) {
																echo '<input type="checkbox"
																		class="js-paiement"
																		id="paiement2_' . (int)$row['id'] . '"
																		name="paiement2_' . (int)$row['id'] . '"
																		data-id="' . (int)$row['id'] . '"
																		data-produit="' . (int)$row['produit_num'] . '"
																		data-val="' . (float)$paiement2_val . '"
																		data-paiement="2"' . $checked . '>';
															}
														}
													?>
													</small></td>
													<td><small id="reste_<?= $row["id"] ?>"><?= safe_number_format($reste,2,'.',' ') ?>€</small></td>
													<td><a href="/clients/client?client_num=<?= crypte($row["client_num"]) ?>&tab=tab_1_6"><small><?= $row["client_nom"] . " " . $row["client_prenom"] ?></small></a></td>
													<td><small><?= $row["commande_num"] ?></small></td>
													<td><small><?= format_date($row["commande_date"],11,1) ?></small></td>
												</tr>
											<?php } ?>	
											</tbody>
											<tr>
												<td><b>Total</b></td>
												<td><?= $nbr ?></td>
												<td colspan="2"></td>
												<td><?= safe_number_format($montant_total_ttc,2,'.',' ') ?> €</td>
												<td></td>
												<td><small>Reste <?= safe_number_format($reste_total1,2,'.','') ?>€<br>sur <?= safe_number_format($paiement_total1,2,'.',' ') ?>€</small></td>
												<td></td>
												<td><small>Reste <?= safe_number_format($reste_total2,2,'.','') ?>€<br>sur <?= safe_number_format($paiement_total2,2,'.',' ') ?>€</small></td>
												<td><?= safe_number_format($reste_total,2,'.',' ') ?> €</td>
												<td colspan="3"></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <?php include TEMPLATE_PATH . 'footer.php'; ?>
            </div>
        </div>
         <?php include TEMPLATE_PATH . 'bottom.php'; ?>
<script>

// EXPOSE GLOBAL pour être appelé par onClick
window.changeEtat = function(id, produit, val, paiement){
  // si la case vient d’être décochée, on force val=0 (sécurité côté client)
  const cbId = (paiement==1? 'paiement1_' : 'paiement2_') + id;
  const cb = document.getElementById(cbId);
  if (cb && !cb.checked) val = 0;

  // Optionnel : petit "loading" inline dans la cellule du "reste"
  const place = 'reste_' + id;
  const cell = document.getElementById(place);
  if (cell) cell.innerHTML = '<span class="fa fa-spinner fa-spin"></span>';

  $ol.apiPost('fournisseur-paiement', {
      mode: 'toggle',
      id: id,
      produit: produit,
      val: val,
      paiement: paiement
    })
    .then((resp) => {
      // resp = {ok:true, html:"12.34 €", place:"reste_113"} par ex.
      if (resp && resp.ok) {
        if (resp.place && document.getElementById(resp.place)) {
          document.getElementById(resp.place).innerHTML = resp.html;
        }
        // petit toast succès discret
        $ol.toastSuccess && $ol.toastSuccess('Mise à jour', 'Paiement enregistré');
      } else {
        throw new Error(resp?.error || 'Réponse invalide');
      }
    })
    .catch((e) => {
      // rollback visuel : si erreur on re-bascule la case
      if (cb) cb.checked = !cb.checked;
      // remettre l’ancien contenu si on l’a encore ?
      if (cell) cell.innerHTML = '<span class="ol-inline-error"><span class="dot"></span><span>Erreur</span></span>';

      $ol.toastError('Erreur serveur', e?.message || 'Impossible de mettre à jour le paiement');
    });
};

document.addEventListener('change', (e)=>{
  const cb = e.target.closest('input[type="checkbox"][id^="paiement"]');
  if (!cb) return;
  const id = parseInt(cb.dataset.id,10) || 0;
  const produit = parseInt(cb.dataset.produit,10) || 0;
  const val = cb.checked ? (parseFloat(cb.dataset.val)||0) : 0;
  const paiement = parseInt(cb.dataset.paiement,10) || 0;
  changeEtat(id, produit, val, paiement); // réutilise la même fonction
});
</script>

    </body>
</html>