<?

function crypte($param)
{
	$param = (1234567*$param);
	$param = str_replace("1","a",$param);
	$param = str_replace("3","x",$param);
	$param = str_replace("4","y",$param);
	$param = str_replace("8","b",$param);
	$param = str_replace("0","m",$param);
	$param = str_replace("5","e",$param);
	return ($param);
}

function decrypte($param)
{
	$param= str_replace("a","1",$param);
	$param= str_replace("x","3",$param);
	$param= str_replace("y","4",$param);
	$param= str_replace("b","8",$param);
	$param= str_replace("m","0",$param);
	$param= str_replace("e","5",$param);
	$param = $param/1234567;
	return $param;
}

//Function format_date Heure
// Langue possible : FR, EN, ES
// parametre choix :
// Choix :
//     0 : Ex : 27 Decembre 2003
//     1 : Ex : 15:30
//     2 : Ex : 27 Decembre 2003 ‡ 15:30
//	   3 : Ex : 27/12
function clean_text($text,$encodage) {
        $text = str_replace('&lt;','<',$text);
        $text = str_replace('&gt;','>',$text);
        $text = str_replace('&quot;','"',$text);
        $text = str_replace('&amp;','&',$text);
        //if ($encodage == 'utf-8') {
	        $text = utf8_decode($text);
        //}
        return $text;
}
Function xtTraiter($nompage) {
     $nompage = strtolower($nompage);
     $nompage = strtr($nompage,"‡‚‰ÓÔÙˆ˘˚¸ÈËÍÎÁ","aaaiioouuueeeec");
	 $nompage = str_replace(":","",$nompage);
	 $nompage = str_replace(" - ","-",$nompage);
 	 $nompage = str_replace("...","",$nompage);
	 $nompage = str_replace("/","-",$nompage);
     $nompage = eregi_replace("[^a-z0-9_:~\\\/\-]","-",$nompage);
     return($nompage);
} 

function diff_date($date_deb,$date_fin)
{
	list(
		$annee_deb,
		$mois_deb,
		$jour_deb) = split('[: -]',$date_deb,3);

	list(
		$annee_fin,
		$mois_fin,
		$jour_fin) = split('[: -]',$date_fin,3);

	$DateLong1 = mktime( 0, 0, 0, $mois_deb, $jour_deb, $annee_deb);
	$DateLong2 = mktime( 0, 0, 0, $mois_fin, $jour_fin, $annee_fin);

	$nbDays = ($DateLong2 - $DateLong1) / (60*60*24);

	return $nbDays;
}

function format_date($date_traiter,$choix,$langue)
{
	list(
		$annee,
		$mois,
		$jour,
		$heure,
		$minute,
		$seconde ) = split('[: -]',$date_traiter,6);
	
	$mois_bon = $mois;
	$mois = $mois / 1;

	//Francais
	if ($langue==1)
		$date_mois = array("Janvier","FÈvrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","DÈcembre");

	//Anglais
	if ($langue==2)
		$date_mois = array("Janvier","FÈvrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","DÈcembre");

	//espagnol
	if ($langue==3)
		$date_mois = array("Janvier","FÈvrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","DÈcembre");

	if ($choix==0)
	{
		$new_date = $jour . " " . $date_mois[$mois-1] . " " . $annee;
	}

	if ($choix==1)
	{
		$new_date = $heure . "h" . $minute;
	}

	if ($choix==2)
	{
		$new_date = $jour . " " . $date_mois[$mois-1] . " " . $annee . " ‡ " . $heure . ":" . $minute;
	}
	if ($choix==3)
	{
		sprintf($mois_bon,"%.2d",$mois_bon);
		$new_date = $jour . "/" . $mois_bon;
	}
	
	if ($choix==4)
	{
		$new_date = $jour . "/" . $mois_bon . "/" . $annee . " " . $heure . ":" . $minute;
	}

	if ($choix==5)
	{
		$new_date = $jour . " " . $date_mois[$mois-1] . " " . $heure . "h" . $minute;
	}

	if ($choix==6)
	{
		$new_date = $jour . "/" . $mois . "/" . $annee;
	}
	
	if ($choix==7)
	{
		sprintf($mois_bon,"%.2d",$mois_bon);
		$new_date = $annee . "-" . $mois_bon . "-" . $jour;
	}
	
	if ($choix==9)
	{
		$new_date = $heure . "h" . $minute;
	}

	if ($choix==8)
	{
		$new_date = $jour;
	}

	if ($choix==10)
	{
		sprintf($mois_bon,"%.2d",$mois_bon);
		$new_date = $jour . "/" . $mois_bon . " " . $heure . "h" . $minute;
	}
	
	if ($choix==11)
	{
		sprintf($mois_bon,"%.2d",$mois_bon);
		$new_date = $jour . "/" . $mois_bon . "/" . $annee;
	}
		
	if ($choix==12)
	{
		$new_date = $heure . ":" . $minute . ":00";
	}
	
	if ($choix==13)
	{
		$new_date = $date_mois[$mois-1] . " " . $annee;
	}
	return $new_date;	
}


function format_date_rss($date_traiter,$choix)
{
	
	if ($choix==1)
		return substr($date_traiter,5,12);
	if ($choix==2)
		return substr($date_traiter,5,18);
	if ($choix==3)
		return substr($date_traiter,18,20);
}		

// Remplace tous les caracteres speciaux 
// d'une chaine de caractere
function Replace_Carac_Special($chaine)
{	
  for ($i = 161; $i < 255; $i++)
     $chaine = ereg_replace(chr($i), "&#$i;", $chaine);

   return $chaine;
}

function Remet_Carac_Special($chaine)
{	
  for ($i = 128; $i < 255; $i++)
     $chaine = ereg_replace("&#$i;",chr($i), $chaine);

   return $chaine;
}

// ajoute des caractËres ‡ une chaine
function Comble_Chaine_Vide($chaine,$nbcar,$carac,$pos)
{	
  $lgchaine = strlen($chaine);
  $chaineremp = $carac;
  $champ = "";
  $pos = $pos;
  
  if ($lgchaine<$nbcar)
  {
    for ($i = 0; $i <$nbcar-$lgchaine ; $i++)
       $champ .= $chaineremp;
    if ($pos=="prec")
      $champ .= $chaine;
    elseif ($pos=="suiv")
      $champ = $chaine.$champ;
  }
  elseif ($lgchaine==$nbcar)
       $champ .= $chaine;
  else
       $champ = "";

   return $champ;
}

function recupValeurEntreBalise($text, $baliseDebut, $baliseFin) {
	$i  = 0;
	$ii = 0;
	$textModif="";
	$textFinal=array();
	while ($i < strlen($text)) {
		if ($text[$i]==$baliseDebut){
			while ($ii < strlen($text)) {
				$textModif = $textModif.$text[$ii];
				if ($text[$i]==$baliseFin) {
					$textModif = str_replace(array($baliseDebut,$baliseFin), "", $textModif);
					array_push($textFinal, $textModif);
					$textModif="";
					break;
				}
				$i++;
				$ii++;
			}
		}     
		$i++;
		$ii++;
	}
	return $textFinal;
}
?>
