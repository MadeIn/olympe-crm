<? include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param.inc"); 
$titre_page = "Gestion des produits - Olympe Mariage";
$desc_page = "Gestion des produits - Olympe Mariage";

$nom_table = "produits";
$nom_champ = "produit";
?>

<? include( $chemin . "/mod/head.php"); ?>
<script language="Javascript">
function displayReponse(sText, place) {
	var info = document.getElementById(place);
	info.innerHTML = sText;
}

function addWidget(id) {	
	//alert(id);
	var oXmlHttp = null; 
	 
	//alert(id);
	if(window.XMLHttpRequest)
		oXmlHttp = new XMLHttpRequest();
	else if(window.ActiveXObject)
	{
	   try  {
                oXmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                oXmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
	}
	
	ref = document.getElementById("ref_" + id).value;
	link = "display.php?ref="+ ref + "&produit=" + id;
	oXmlHttp.open("get",link, true);
	oXmlHttp.onreadystatechange = function () {
		if (oXmlHttp.readyState == 4) {
				if (oXmlHttp.status == 200) {
					//alert('OK : ' + oXmlHttp.responseText);
					//displayReponse(oXmlHttp.responseText, "select_client");
					if (type!=1) {
						displayReponse("", "select_acompte");
					}
				}
				else {
					//alert('Erreur : ' + oXmlHttp.statusText);
					displayReponse("Erreur : " + oXmlHttp.statusText, "select_client");
				}
		}
	};
	oXmlHttp.send(null);
}
</script>
    <body class="page-header-fixed page-sidebar-closed-hide-logo">
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <? include( $chemin . "/mod/top.php"); ?>
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN BREADCRUMBS -->
                    <div class="breadcrumbs">
                        <h1>Olympe Mariage</h1>
                        <ol class="breadcrumb">
                            <li>
                                <a href="/home.php">Accueil</a>
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
								<? 
										$sql = "select * from md_produits p, categories c, marques m where p.categorie_num=c.categorie_num and p.marque_num=m.marque_num";
										$sql .= " order by categorie_nom ASC, marque_nom ASC, produit_nom ASC";
										$cc = mysql_query($sql);
										$nbr_produit = mysql_num_rows($cc);
								?>
									<table class="table table-striped table-bordered table-advance table-hover">
								<? if ($nbr_produit>0) { ?>
									 <thead>
										<tr>
											<th><i class="fa fa-check"></i> Nom</th>
											<th><i class="fa fa-list"></i> Categ</th>
											<th><i class="fa fa-font"></i> Marques</th>
											<th><i class="fa fa-euro"></i> Ref</th>
										</tr>
									</thead>
										   <tbody>
											  <? while ($rcc=mysql_fetch_array($cc)) {  ?>
												<tr>
													<td class="highlight"><a href="produit.php?modif_num=<? echo crypte($rcc[$nom_champ . "_num"]) ?>"> <? echo $rcc["produit_nom"] ?></a></td>
													<td><? echo $rcc["categorie_nom"] ?></td>
													<td><? echo $rcc["marque_nom"] ?></td>
													<td><input type="text" name="ref_<? echo $rcc["produit_num"] ?>" id="ref_<? echo $rcc["produit_num"] ?>" value="<? echo $rcc["produit_ref"] ?>" onChange="addWidget(<? echo $rcc["produit_num"] ?>)"></td>
												</tr>
											  <? } ?>
												  
											</tbody>
								<? } else { ?>
									<tbody>
										<tr><td align="center">Pas de résultat !</td></tr>                          
									</tbody>
								<? } ?>
									</table>
								</div>
							</div>
						</div>
                    </div>
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <? include( $chemin . "/mod/footer.php"); ?>
            </div>
        </div>
         <? include( $chemin . "/mod/bottom.php"); ?>
    </body>

</html>