<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/param.php";
$titre_page = "Gestion des produits - Olympe Mariage";
$desc_page = "Gestion des produits - Olympe Mariage";

$nom_table = "produits";
$nom_champ = "produit";
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
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="portlet light bordered">
							<div class="portlet-title">
								<div class="caption font-blue-sunglo">
									<i class="icon-settings font-blue-sunglo"></i>
									<span class="caption-subject bold uppercase"> Liste des produits</span>
								</div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable">
								<?php 
										$sql = "select * from md_produits p, categories c, marques m where p.categorie_num=c.categorie_num and p.marque_num=m.marque_num";
										$sql .= " order by categorie_nom ASC, marque_nom ASC, produit_nom ASC";
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
											<th><i class="fa fa-euro"></i> Ref</th>
										</tr>
									</thead>
										   <tbody>
											  <?php foreach ($cc as $rcc) {  ?>
												<tr>
													<td class="highlight"><a href="produit.php?modif_num=<?= crypte($rcc[$nom_champ . "_num"]) ?>"> <?= $rcc["produit_nom"] ?></a></td>
													<td><?= $rcc["categorie_nom"] ?></td>
													<td><?= $rcc["marque_nom"] ?></td>
													<td>
														<input type="text"
															name="ref_<?= (int)$rcc['produit_num'] ?>"
															id="ref_<?= (int)$rcc['produit_num'] ?>"
															value="<?= h($rcc['produit_ref']) ?>"
														>	
												</tr>
											  <?php } ?>
												  
											</tbody>
								<?php } else { ?>
									<tbody>
										<tr><td align="center">Pas de résultat !</td></tr>                          
									</tbody>
								<?php } ?>
									</table>
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
			async function sendRef(input) {
				const produit = input.dataset.produit;
				const ref = input.value.trim();
				try {
				await $ol.apiPost('produit', { mode:'changeRef', produit, ref });
				$ol.toastSuccess('Référence mise à jour !');
				// petit feedback visuel
				input.classList.add('is-saved');
				setTimeout(()=>input.classList.remove('is-saved'), 800);
				} catch(e) {
				$ol.toastError('Échec MAJ référence', e?.message || 'Erreur inconnue');
				}
			}

			document.addEventListener('DOMContentLoaded', () => {
				document.querySelectorAll('input[id^="ref_"]').forEach(inp => {
				const id = inp.id.replace(/^ref_/, '');
				inp.dataset.produit = id;

				// uniquement à la sortie du champ
				inp.addEventListener('blur', () => sendRef(inp));
				});
			});
		</script>
		<style>
		/* feedback optionnel */
		input.is-saved {
			outline: 2px solid #28a745;
			transition: outline-color .8s;
		}
		</style>
    </body>
</html>