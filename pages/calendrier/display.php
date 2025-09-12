<?php  $chemin = $_SERVER['DOCUMENT_ROOT'];
	include($chemin . "/inc/users.php"); 
	include($chemin . "/inc/divers.php");
	include($chemin . "/inc/object.php");
	include($chemin . "/inc/produits.php");
	include($chemin . "/inc/email.php");
	include($chemin . "/inc/db.php");
	
	$base = new Db();
	$base->Connect();
	
	switch ($mode) {
		case 1:
			if ($type==1) {
				echo '<div class="form-group">
					<label>Type</label>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-share-alt"></i>
						</span>
						<select name="type" class="form-control">';
						
				$sql = "select * from rdv_types where type_num NOT IN (2,3) order by type_pos ASC";
				$tt = mysql_query($sql);
				while ($rtt=mysql_fetch_array($tt)) {
					echo '<option value="' . $rtt["type_num"] . '"';
					if ($rtt["type_num"]==1)
						echo " SELECTED";
					echo '>' . $rtt["type_nom"] . '</option>';
				}
						
				echo '	</select>
					</div>
				</div>
				<div class="form-group">
					<label>Client</label>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-search"></i>
						</span>
						<input type="text" name="client" id="client" class="form-control" placeholder="Nom du client" onChange="addAcompte();">
					</div>
				</div>';
			} else {
				echo '<input type="hidden" name="type" value="0"><input type="hidden" name="client" value="0">';
			}
		break;
		
		case 2:
			$client_search = recupValeurEntreBalise($client,"[","]");
			if (count($client_search)>0) {
				$client_num = $client_search[0];
				// On recherche le client 
				$sql = "select * from clients where client_num='" . $client_num . "'";
				$cl = mysql_query($sql);
				if ($rcl = mysql_fetch_array($cl)) {
					// On recherche les commandes en cours non facturée
					$sql = "select * from commandes where client_num='" . $client_num . "' and devis_num!=0 and commande_num!=0 and facture_num=0 order by commande_date DESC";
					$co = mysql_query($sql);
					$nbr_commande = mysql_num_rows($co);
					if ($nbr_commande>0) {
						echo '<label>Acompte</label>
						  <div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-euro"></i>
							</span>
							<select name="dernier_acompte" class="form-control">';
						while ($rco=mysql_fetch_array($co)) {
							$dernier_acompte = number_format(resteAPayerCommande($rco["id"]),2,"."," ");
							echo '<option value="' . $dernier_acompte . '">Commande : ' . $rco["commande_num"] . ' - Acompte : ' . $dernier_acompte . ' €</option>';
						}
						echo '</select>
							</div>';
					} else
						echo '<input type="hidden" name="dernier_acompte" value="0">';
				} else 
					echo '<input type="hidden" name="dernier_acompte" value="0">';
			} else
				echo '<input type="hidden" name="dernier_acompte" value="0">';
		break;
	}
?>