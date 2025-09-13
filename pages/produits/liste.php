<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Gestion des produits - Olympe Mariage";
$desc_page = "Gestion des produits - Olympe Mariage";

$alert = "Etes vous sûr de vouloir supprimer cet item ? Attention cet action peut avoir des conséquences sur les produits...";

if (!isset($recherche)) {
	$etat=1;
}


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
                            <li class="active">Rechercher un produit</li>
                        </ol>
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption font-red-sunglo">
										<i class="icon-settings font-red-sunglo"></i>
											<span class="caption-subject bold uppercase"> Rechercher un produit</span>
									</div>
								</div>
								<div class="portlet-body form">
									<form name="ajouter" method="POST" action="<?= form_action_same() ?>" enctype="multipart/form-data">
										<input type="hidden" name="recherche" value="ok">
										<table class="table table-striped table-bordered table-advance table-hover">
											<tbody>
												<tr>
													<td><label>Nom</label>
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-list"></i>
														</span>
														<input type="text" name="nom" class="form-control" value="<?= ($nom ?? '') ?>"></div></td>
												</tr>
												<tr>
													<td><label>Categorie</label>
													<div class="input-group">
														<select name="categorie" class="form-control">
														<option value="0">-----------------</option>
														<?php 
														$sql = "select * from categories order by categorie_nom ASC";
														$cc = $base->query($sql);
														foreach ($cc as $rcc)
														{
															echo '<option value="' . $rcc["categorie_num"] . '"';
															if (($categorie ?? 0)==$rcc["categorie_num"])
																echo " SELECTED";
															echo '>' . $rcc["categorie_nom"] . '</option>';
														}
													?>		
														</select>
													</div>
													</td>
												</tr>
												<tr>
													<td><label>Marques</label>
													<div class="input-group">
														<select name="marque" class="form-control">
														<option value="0">-----------------</option>
														<?php 
														$sql = "select * from marques order by marque_nom ASC";
														$cc = $base->query($sql);
														foreach ($cc as $rcc)
														{
															echo '<option value="' . $rcc["marque_num"] . '"';
															if (($marque ?? 0)==$rcc["marque_num"])
																echo " SELECTED";
															echo '>' . $rcc["marque_nom"] . '</option>';
														}
													?>		
														</select>
													</div>
													</td>
												</tr>
												<tr>
													<td><label>Etat</label>
													<div class="input-group">
														<input type="radio" name="etat" value="1" <?php if ($etat==1) echo " checked"; ?>> Visible &nbsp;
														<input type="radio" name="etat" value="0" <?php if ($etat==0) echo " checked"; ?>> Invisible
													</div>
													</td>
												</tr>
												<tr>
													<td><input type="submit" value="Rechercher" class="btn blue"></td>
												</tr>
											</tbody>
										</table>									
									</form>
								</div>
							</div>
							<!-- END SAMPLE FORM PORTLET-->
						</div>
						<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
							<div class="portlet light bordered">
							<div class="portlet-title">
								<div class="caption font-blue-sunglo">
									<i class="icon-settings font-blue-sunglo"></i>
									<span class="caption-subject bold uppercase"> Liste des produits</span>
								</div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable">
								<?php if (isset($recherche)) { 
										$sql = "select * from md_produits p, categories c, marques m where p.categorie_num=c.categorie_num and p.marque_num=m.marque_num and produit_etat='" . $etat . "'";
										if ($categorie!=0)
											$sql .= " and p.categorie_num='" . $categorie . "'";
										if ($marque!=0)
											$sql .= " and p.marque_num='" . $marque . "'";
										if ($nom!="") {
											$nom = str_replace("'","\'",$nom);
											$sql .= " and produit_nom like '%" . $nom . "%'";
										}
										$sql .= " order by categorie_nom ASC, produit_nom ASC";
										$cc = $base->query($sql);
										$nbr_produit = count($cc);
								?>
									<table class="table table-striped table-bordered table-advance table-hover">
								<?php if ($nbr_produit>0) { ?>
									 <thead>
										<tr>
											<th><i class="fa fa-check"></i> Nom</th>
											<th><i class="fa fa-list"></i> Categ</th>
											<th><i class="fa fa-font"></i> Marques</th>
											<th><i class="fa fa-euro"></i> Prix Achat</th>
											<th><i class="fa fa-euro"></i> Prix TTC</th>
											<th><i class="fa fa-th-large"></i> Etat</th>
											<th colspan="2"><i class="fa fa-bookmark"></i> Gestion</th>
										</tr>
									</thead>
										   <tbody>
											  <?php foreach ($cc as $rcc) { 
													$prix = RecupPrixInit($rcc["produit_num"]);
													$sql = "select * from md_produits_photos where produit_num='" . $rcc["produit_num"] . "'";
													$pp = $base->query($sql);
													$nbr = count($pp);	
													$sql = "select * from prixachats where prixachat_num='" . $rcc["prixachat_num"] . "'";
													$rpp = $base->queryRow($sql); 
													if ($rpp)														
														$prix_achat = $rpp["prixachat_montant"];
											  ?>
												<tr>
													<td class="highlight">
														<div class="success"></div> <a href="produit?modif_num=<?= crypte($rcc["produit_num"]) ?>"> <?= $rcc["produit_nom"] ?></a> (<?= $nbr ?>)</td>
													<td><?= $rcc["categorie_nom"] ?></td>
													<td><?= $rcc["marque_nom"] ?></td>
													<td>
														<input type="text"
															name="prixachat"
															id="prixachat_<?= (int)$rcc["prixachat_num"] ?>"
															value="<?= h($prix_achat ?? '') ?>"
															onBlur="changePrixAchat('<?= (float)$rcc["prixachat_num"] ?>')"
															class="form-control">	
													<td><?php 
													if ($prix!=0)
														echo safe_number_format($prix,2,"."," ") . ' €';
													else
														echo '<font color="red"><strong>' . safe_number_format($prix,2,"."," ") . ' € </strong></font>';
													?>
													</td>													
													<td><?php if ($rcc["produit_etat"]==1) 
															echo "Visible";
														   else
															echo "Invisible"; ?>
													</td>
													<td>
														<a href="produit?modif_num=<?= crypte($rcc["produit_num"]) ?>" class="btn btn-outline btn-circle btn-sm purple">
															<i class="fa fa-edit"></i> Edit </a> 
														<a href="#"
															class="btn btn-outline btn-circle dark btn-sm black"
															data-delete="<?= h(crypte($rcc["produit_num"])) ?>"
															data-confirm="<?= h($alert) ?>">
															<i class="fa fa-trash-o"></i> Suppr
														</a>
													</td>
												</tr>
											  <?php } ?>
												  
											</tbody>
								<?php } else { ?>
									<tbody>
										<tr><td align="center">Pas de résultat !</td></tr>                          
									</tbody>
								<?php } ?>
									</table>
								<?php } ?>
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

	// === HANDLER UNIFIÉ pour confirmations ===
	document.addEventListener('click', async (e) => {
		// Vérifier si c'est un lien de suppression (priorité)
		const deleteBtn = e.target.closest('[data-delete]');
		if (deleteBtn) {
			e.preventDefault();
			e.stopPropagation();
			
			const message = deleteBtn.dataset.confirm || "Êtes-vous sûr de vouloir supprimer cet item ?";
			
			try {
				// Attendre la confirmation
				const confirmed = await $ol.ask(message);
				if (!confirmed) return;
				
				// Loading pendant suppression
				$ol.loading(true);
				
				// Supprimer via API
				await $ol.apiPost('produit', { mode: 'delete', suppr: deleteBtn.dataset.delete });
				
				// Redirection après succès
				window.location.href = '<?= current_path() . '?recherche=ok&nom=' . ($nom ?? '') . '&categorie=' . ($categorie ?? 0) . '&etat=' . ($etat ?? 0) . '&marque=' . ($marque ?? 0) ?>';
				
			} catch (err) {
				$ol.loading(false);
				$ol.toastError("Suppression", err?.message || "Impossible de supprimer");
			}
			return;
		}
		
		// Autres liens avec confirmation simple
		const confirmLink = e.target.closest('a[data-confirm]:not([data-delete])');
		if (confirmLink) {
			e.preventDefault();
			
			const message = confirmLink.dataset.confirm || "Confirmer ?";
			const confirmed = await $ol.ask(message);
			
			if (confirmed) {
				// Si confirmé, suivre le lien
				window.location.href = confirmLink.href;
			}
		}
	});

	// === MAJ prix d'achat (EXPOSED GLOBAL) ===
	window.changePrixAchat = function(id) {
		const input = document.getElementById('prixachat_' + id);
		if (!input) return;

		const prix = input.value;
		$ol.apiPost('produit', { mode: 'changePrixAchat', id, prix })
		.then(() => {
			$ol.toastSuccess('Prix mis à jour');
		})
		.catch(() => {
			// inline badge près du champ
			$ol.showInlineError(input, "Échec mise à jour");
		});
	};
</script>
	</body>
</html>