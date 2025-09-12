<?php include( $_SERVER['DOCUMENT_ROOT'] . "/inc/param_invite.php"); 

	$sql = "select * from selections s, showrooms sh, users u, clients c where s.client_num=c.client_num and s.showroom_num=sh.showroom_num and s.user_num=u.user_num and selection_num='" . decrypte($id) . "'";
	$cc = $base->query($sql);
	if (!$rcc=mysql_fetch_array($cc)) {
		echo "<script>document.location.href='http://www.olympe-mariage.com'</script>";
	}
	
	if ($rcc["client_genre"]==0)
		$genre = "Chère";
	else
		$genre = "Cher";
?>
<html>
<head>
<title>Votre sélection Olympe Mariage</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<META NAME="language" CONTENT="fr">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" href="css/style.css" />
<script src="js/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.lightbox.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.lightbox.css" />
<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="css/jquery.lightbox.ie6.css" />
<![endif]-->
</head>
<body>

<section class="container selections" id="showroom">
	<div class="text-center">
		<img src="img/olympe-mariage-logo.jpg">
		<p><strong><?php echo $genre ?> <?php echo $rcc["client_prenom"] ?></strong>,<br>
		vous trouverez votre sélection de produits proposés par Olympe Mariage <?php echo $rcc["showroom_ville"] ?> ci-dessous</p>
		<hr style="width: 30%; border: none; border-top: solid 1px #EAEAEA; margin: 25px 35%;">
	</div>
<?php 
	// On affiche les produits sélectionnés
	$sql = "select * from selections_produits s, md_produits p, categories c, marques m where s.produit_num=p.produit_num and p.categorie_num=c.categorie_num and p.marque_num=m.marque_num and selection_num='" . decrypte($id) . "' order by c.categorie_num ASC";
	$pp = $base->query($sql);
	$nbr_pp = mysql_num_rows($pp);
	if ($nbr_pp>0) {
		while ($rpp=mysql_fetch_array($pp)) {
			echo '<div class="row"><div class="col-sm-5 col-xs-12">
					<h2 class="text-right" style="margin-top: 20px;">' . $rpp["categorie_nom"] . ' - ' . $rpp["marque_nom"] . '<br><strong style="font-size: 22px; line-height: 35px;">' . $rpp["produit_nom"] . '</strong><br><small>' . AffichePrix($rpp["produit_num"]) . '</small></h2>
				 </div>';
				$sql = "select * from md_produits_photos where produit_num='" . $rpp["produit_num"] . "' order by photo_pos ASC";
				$ph = $base->query($sql);
				echo '<div class="col-sm-7 col-xs-12">';
				while ($rph=mysql_fetch_array($ph)) {
					$image_norm = "https://crm.olympe-mariage.com/photos/produits/norm/" . $rph["photo_chemin"];
					$image_zoom = "https://crm.olympe-mariage.com/photos/produits/zoom/" . $rph["photo_chemin"];
					echo '	<a href="' . $image_zoom . '" class="lightbox" rel="showroom' . $rpp["produit_num"] . '">
								<figure>
									<img src="' . $image_norm . '">
								</figure>
							</a>';
				}
				echo '</div></div><hr>';
		}
	}
?>	<div style="clear: both;"></div>
	<p class="text-center" style="font-family: 'nimbus', sans-serif;">
		<strong class="nomproprietaire"><?php echo $rcc["user_prenom"] ?></strong>
		Olympe Mariage <?php echo $rcc["showroom_ville"] ?><br>
		<small><?php echo $rcc["showroom_adr1"] ?> <?php if ($rcc["showroom_adr2"]!="") echo " - " . $rcc["showroom_adr2"]; ?>, <?php echo $rcc["showroom_cp"] ?> <?php echo $rcc["showroom_ville"] ?> - </small><a href="tel:<?php echo $rcc["showroom_tel"] ?>"><small><?php echo $rcc["showroom_tel"] ?></small></a><small> - </small><a href="http://www.olympe-mariage.com"><small>www.olympe-mariage.com</small></a>
	</center>
</section>

<script type="text/javascript">
  jQuery(document).ready(function($){
    $('.lightbox').lightbox();
	$( ".lightbox figure" ).each(function() {
		var srcimg = $( this ).find( 'img' ).attr( 'src' );
		var srcback = "url(" + srcimg + ")";
		$( this ).css( "background-image", srcback );
	});
  });
</script>

</body>
</html>