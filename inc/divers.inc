<?
	function GenereIntro($desc)
	{
		$desc = strip_tags($desc);
		$pos_pt = strpos($desc,".");
		if ($pos_pt<120)
			$desc = trim(substr($desc,0,$pos_pt));
		else
		{
			$pos_pt = strpos($desc," ",120);
			$desc = trim(substr($desc,0,$pos_pt));
		}
		$desc .= "...";

		return $desc;
	}
	
	function GenereIntroLg($desc,$lg)
	{
		$desc = strip_tags($desc);
		$pos_pt = strpos($desc,".");
		if ($pos_pt<$lg)
			$desc = trim(substr($desc,0,$pos_pt));
		else
		{
			$pos_pt = strpos($desc," ",$lg);
			$desc = trim(substr($desc,0,$pos_pt));
		}
		$desc .= "...";

		return $desc;
	}
	
	function img_resize($f, $size, $save_dir, $save_name, $maxisheight = 0 )
    {
		$save_dir   .= (substr($save_dir,-1) != "/") ? "/" : "";
		$gis        = getimagesize($f);
		$type       = $gis[2];
		$type_content = $gis['mime'];
		
		$img_nom = "";
		
		$imgFunc = ''; 
		switch($type) 
		{ 
			case '1': 
				$save_name .= ".gif";
				$img = ImageCreateFromGIF($f); 
				$imgFunc = 'ImageGIF'; 
				$transparent_index = ImageColorTransparent($img); 
				if($transparent_index!=(-1)) $transparent_color = ImageColorsForIndex($img,$transparent_index); 
				break; 
			case '2':
				$save_name .= ".jpg";
				$img = ImageCreateFromJPEG($f); 
				$imgFunc = 'ImageJPEG'; 
				break; 
			case '3': 
				$save_name .= ".png";
				$img = ImageCreateFromPNG($f); 
				ImageAlphaBlending($img,true); 
				ImageSaveAlpha($img,true); 
				$imgFunc = 'ImagePNG'; 
				break; 
			default: 
				die("ERROR - no image found"); 
				break; 
		}
	
		if(!empty($size)) 
		{ 
			list($w,$h) = GetImageSize($f); 
			if( $w==0 or $h==0 ) die("ERROR - zero image size"); 
			$percent = $size / (($w>$h)?$w:$h); 
			
			//if($percent>0 and $percent<=1) 
			//{ 
				$nw = intval($w*$percent); 
				$nh = intval($h*$percent); 
				$img_resized = ImageCreateTrueColor($nw,$nh); 
				if($type=='3') 
				{ 
					ImageAlphaBlending($img_resized,false); 
					ImageSaveAlpha($img_resized,true); 
				} 
				if(!empty($transparent_color)) 
				{ 
					$transparent_new = ImageColorAllocate($img_resized,$transparent_color['red'],$transparent_color['green'],$transparent_color['blue']); 
					$transparent_new_index = ImageColorTransparent($img_resized,$transparent_new); 
					ImageFill($img_resized, 0,0, $transparent_new_index); 
				} 
				if(ImageCopyResized($img_resized,$img, 0,0,0,0, $nw,$nh, $w,$h )) 
				{ 
					switch($type) 
					{ 
						case '1': 
							imagegif($img_resized, $save_dir.$save_name);
							break; 
						case '2':
							imagejpeg($img_resized, $save_dir.$save_name);
							break; 
						case '3': 
							imagepng($img_resized, $save_dir.$save_name);
							break; 
						default: 
							die("ERROR - no image found"); 
							break; 
					} 					
					$img_nom = $save_name;
				} 
			//}
		} 
		//$imgFunc($img); 
		unlink($f);
		ImageDestroy($img);
		return $img_nom;
    }
		
	function uploadPhotoProfil($userfile_acc,$nom_photo,$size) {
		$base = $_SERVER['DOCUMENT_ROOT'];
		$rep =  $base . "/photos/users/";
		$retour = "";
		if($userfile_acc["size"]>0) {
			$savefile =  $rep . $userfile_acc["name"];
			if (move_uploaded_file($userfile_acc["tmp_name"], $savefile)) { 
				$img = $rep . $userfile_acc["name"];
				$nom_image = xtTraiter($nom_photo);
				$retour = img_resize($savefile, $size, $rep , $nom_image);
			}
		}
		return $retour;
	}
	
	function uploadDoc($userfile_acc,$chemin_site)
	{
		$base = "/homez.387/madeinpr";
		$rep =  $base . $chemin_site . "docs/";
		$retour = false;
		
		if($userfile_acc["size"]>0)
		{
			$savefile =  $rep . $userfile_acc["name"];
			if (move_uploaded_file($userfile_acc["tmp_name"], $savefile)) 
				$retour = $userfile_acc["name"];
		}
		return $retour;
	}
	
	function uploadPhoto($userfile_acc,$nom_photo,$chemin_site)
	{
		$rep = "/homez.387/madeinpr" . $chemin_site . "produits/photos/UPLOAD/";
		$rep_save = "/homez.387/madeinpr" . $chemin_site . "produits/photos/";
		//echo $rep . " [" . $userfile_acc["size"] . "]<br>";
		$retour = "";
		if ($userfile_acc["size"]>0)
		{
			$savefile =  $rep . $userfile_acc["name"];
			//echo $savefile . "<br>";
			if (move_uploaded_file($userfile_acc["tmp_name"], $savefile)) 
			{ 
				$img = $rep . $userfile_acc["name"];
				$nom_image = xtTraiter($nom_photo) . "-" . Date("YmdHis") . ".jpg";
				
				$img_traite_zoom = $rep_save . "zoom/" . $nom_image;
				$img_traite = $rep_save . "norm/" . $nom_image;
				$img_traite_min = $rep_save . "min/" . $nom_image;


				copy($img, $img_traite_zoom);
				copy($img, $img_traite);
				copy($img, $img_traite_min);
			
				// On recupere les dimensions de la photo
				$dim=getimagesize($img); 
				
				if ($dim[0]>$dim[1])
					$portrait=0;
				else
					$portrait=1;
				
				// Calcul du ratio
				$var = $dim[0] / $dim[1];
		
				// Dimension de la grande photo
				$largeur = 1000;
				$hauteur = round($largeur / $var,0);
	 
				// Dimension de la normal
				$largeur_norm = 600;
				$hauteur_norm = round($largeur_norm / $var,0);


				// Dimension de la normal
				$largeur_min = 200;
				$hauteur_min = round($largeur_min / $var,0);
	 
				// On redimensionne la photo
				if (!$source_zoom = @imagecreatefromjpeg($img_traite_zoom))
				{ 
					$mess_erreur = "Impossible de traiter l'image. Celle-ci est altérée.";
					$erreur=1;
					unlink ($img_traite_zoom); 
					unlink ($img_traite); 
					unlink ($img_traite_min);
					unlink ($img); 
				}
				else
				{
					$dest = imagecreatetruecolor($largeur, $hauteur);
					imagecopyresampled($dest, $source_zoom, 0, 0, 0, 0, $largeur, $hauteur,$dim[0],$dim[1]);
					imagejpeg($dest,$img_traite_zoom);
					unset($source_zoom);
					// On crée la normale
					if (!$source_norm = @imagecreatefromjpeg($img_traite))
					{ 
						$mess_erreur = "Impossible de traiter l'image. Celle-ci est altérée.";
						$erreur=1;
						unlink ($img_traite_zoom); 
						unlink ($img_traite); 
						unlink ($img_traite_min);
						unlink ($img);
					}
					else
					{
						$dest_norm = imagecreatetruecolor($largeur_norm, $hauteur_norm);
						imagecopyresampled($dest_norm, $source_norm, 0, 0, 0, 0, $largeur_norm, $hauteur_norm,$dim[0],$dim[1]);
						imagejpeg($dest_norm,$img_traite);
						unset($source_norm);

						// On crée la miniature
						if (!$source_min = @imagecreatefromjpeg($img_traite_min))
						{ 
							$mess_erreur = "Impossible de traiter l'image. Celle-ci est altérée.";
							$erreur=1;
							unlink ($img_traite_zoom); 
							unlink ($img_traite); 
							unlink ($img_traite_min);
							unlink ($img);
						}
						else
						{
							$dest_min = imagecreatetruecolor($largeur_min, $hauteur_min);
							imagecopyresampled($dest_min, $source_min, 0, 0, 0, 0, $largeur_min, $hauteur_min,$dim[0],$dim[1]);
							imagejpeg($dest_min,$img_traite_min);
							unset($source_min);
							
							// Tout est ok alors on insere dans la base
							//$sql = "insert into md_produits_photos values (0,'" . $produit_num . "','" . $nom_image . "'," . $portrait . "," . $une . ",0)";
							//mysql_query($sql);
							$retour = $nom_image;
							// On efface la source
							unlink ($img);
						}
					}
				}
			} 
		}
		return $retour;
	}
	
			
	function uploadPhotoPdt($userfile_acc,$nom_photo,$repertoire,$size1=0,$size2=0,$size3=0)
	{
		$base = $_SERVER['DOCUMENT_ROOT'];
		$rep =  $base . "/photos/" . $repertoire . "/";
		//echo $rep;
		$retour = "";
		
		if ($size1==0)	{
			// on recupere la largeur de la photo
			$size1=800;
		}
		
		if($userfile_acc["size"]>0)	{
			$savefile =  $rep . $userfile_acc["name"];
			if (move_uploaded_file($userfile_acc["tmp_name"], $savefile)) { 
				$nom_image = Slug($nom_photo);
				if ($size1!=0) {
					$rep_size = $rep . "zoom/";
					$img_size1 = $rep_size . $userfile_acc["name"];
					copy($savefile,$img_size1);					
					$retour = img_resize($img_size1, $size1, $rep_size, $nom_image);
				}
				if ($size2!=0) {
					$rep_size = $rep . "norm/";
					$img_size2 = $rep_size . $userfile_acc["name"];
					copy($savefile,$img_size2);					
					img_resize($img_size2, $size2, $rep_size, $nom_image);
				}
				if ($size3!=0) {
					$rep_size = $rep . "min/";
					$img_size3 = $rep_size . $userfile_acc["name"];
					copy($savefile,$img_size3);	
					img_resize($img_size3, $size3, $rep_size, $nom_image);
				}
				unlink($savefile);
			}
		}
		echo $retour;
		return $retour;
	}
	
	function remove_accent($str)
	{
	  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
					'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
					'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
					'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C',
					'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e',
					'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i',
					'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l',
					'?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ',
					'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 
					't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 
					'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
					'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?');

	  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
					'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
					'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
					'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
					'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
					'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
					'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
					'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
					's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
					'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
					'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	  return str_replace($a, $b, $str);
	}

	
	/* Générateur de Slug (Friendly Url) : convertit un titre en une URL conviviale.*/
	function Slug($str){
		return mb_strtolower(preg_replace(array('/[^a-zA-Z0-9 \'-]/', '/[ -\']+/', '/^-|-$/'),
		array('', '-', ''), remove_accent($str)));
	}
?>