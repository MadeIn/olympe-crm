<?php
/**
 * Fonctions de gestion des images et uploads
 * Modernisé pour PHP8 avec GD et gestion d'erreurs améliorée
 */

/**
 * Redimensionne une image et la sauvegarde
 */
function img_resize(string $source_file, int $size, string $save_dir, string $save_name, int $maxisheight = 0): string {
    if (!file_exists($source_file) || !is_readable($source_file)) {
        error_log("Fichier source non trouvé ou non lisible: $source_file");
        return '';
    }
    
    // S'assurer que le répertoire se termine par /
    $save_dir = rtrim($save_dir, '/') . '/';
    
    // Créer le répertoire si nécessaire
    if (!is_dir($save_dir)) {
        mkdir($save_dir, 0755, true);
    }
    
    // Obtenir les informations de l'image
    $image_info = getimagesize($source_file);
    if ($image_info === false) {
        error_log("Impossible de lire les informations de l'image: $source_file");
        return '';
    }
    
    [$width, $height, $type] = $image_info;
    $mime_type = $image_info['mime'];
    
    // Créer l'image source selon le type
    $source_img = match($type) {
        IMAGETYPE_GIF => imagecreatefromgif($source_file),
        IMAGETYPE_JPEG => imagecreatefromjpeg($source_file),
        IMAGETYPE_PNG => imagecreatefrompng($source_file),
        default => false
    };
    
    if ($source_img === false) {
        error_log("Impossible de créer l'image source depuis: $source_file");
        return '';
    }
    
    // Calculer les nouvelles dimensions
    if ($width == 0 || $height == 0) {
        imagedestroy($source_img);
        return '';
    }
    
    $ratio = min($size / $width, $size / $height);
    $new_width = (int)($width * $ratio);
    $new_height = (int)($height * $ratio);
    
    // Créer l'image de destination
    $dest_img = imagecreatetruecolor($new_width, $new_height);
    if ($dest_img === false) {
        imagedestroy($source_img);
        return '';
    }
    
    // Préserver la transparence pour PNG et GIF
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($dest_img, false);
        imagesavealpha($dest_img, true);
        $transparent = imagecolorallocatealpha($dest_img, 255, 255, 255, 127);
        imagefill($dest_img, 0, 0, $transparent);
    } elseif ($type == IMAGETYPE_GIF) {
        $transparent_index = imagecolortransparent($source_img);
        if ($transparent_index >= 0) {
            $transparent_color = imagecolorsforindex($source_img, $transparent_index);
            $transparent_new = imagecolorallocate($dest_img, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
            imagefill($dest_img, 0, 0, $transparent_new);
            imagecolortransparent($dest_img, $transparent_new);
        }
    }
    
    // Redimensionner l'image
    $success = imagecopyresampled($dest_img, $source_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    if (!$success) {
        imagedestroy($source_img);
        imagedestroy($dest_img);
        return '';
    }
    
    // Déterminer l'extension et sauvegarder
    $extension = match($type) {
        IMAGETYPE_GIF => '.gif',
        IMAGETYPE_JPEG => '.jpg',
        IMAGETYPE_PNG => '.png',
        default => '.jpg'
    };
    
    $full_save_name = $save_name . $extension;
    $full_path = $save_dir . $full_save_name;
    
    $save_success = match($type) {
        IMAGETYPE_GIF => imagegif($dest_img, $full_path),
        IMAGETYPE_JPEG => imagejpeg($dest_img, $full_path, 90),
        IMAGETYPE_PNG => imagepng($dest_img, $full_path),
        default => false
    };
    
    // Nettoyer la mémoire
    imagedestroy($source_img);
    imagedestroy($dest_img);
    
    // Supprimer le fichier source
    if (file_exists($source_file)) {
        unlink($source_file);
    }
    
    return $save_success ? $full_save_name : '';
}

/**
 * Upload et redimensionnement d'une photo de profil
 */
function uploadPhotoProfil(array $userfile_acc, string $nom_photo, int $size): string {
    if (!isset($userfile_acc['size']) || $userfile_acc['size'] <= 0) {
        return '';
    }
    
    if ($userfile_acc['error'] !== UPLOAD_ERR_OK) {
        error_log("Erreur upload: " . $userfile_acc['error']);
        return '';
    }
    
    $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
    $upload_dir = $app_config['app_root'] . "/public/photos/users/";
    
    // Créer le répertoire si nécessaire
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Vérifier le type de fichier
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $userfile_acc['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        error_log("Type de fichier non autorisé: " . $mime_type);
        return '';
    }
    
    // Générer un nom de fichier sécurisé
    $safe_name = sanitize_filename($nom_photo);
    $temp_file = $upload_dir . 'temp_' . uniqid() . '_' . $userfile_acc['name'];
    
    if (!move_uploaded_file($userfile_acc['tmp_name'], $temp_file)) {
        error_log("Impossible de déplacer le fichier uploadé");
        return '';
    }
    
    // Redimensionner l'image
    return img_resize($temp_file, $size, $upload_dir, $safe_name);
}

/**
 * Upload d'un document
 */
function uploadDoc(array $userfile_acc, string $chemin_site): string {
    if (!isset($userfile_acc['size']) || $userfile_acc['size'] <= 0) {
        return '';
    }
    
    if ($userfile_acc['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    
    $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
    $upload_dir = $app_config['app_root'] . $chemin_site . "docs/";
    
    // Créer le répertoire si nécessaire
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Générer un nom de fichier sécurisé
    $safe_name = sanitize_filename($userfile_acc['name']);
    $full_path = $upload_dir . $safe_name;
    
    if (move_uploaded_file($userfile_acc['tmp_name'], $full_path)) {
        return $safe_name;
    }
    
    return '';
}

/**
 * Upload et traitement d'une photo produit avec plusieurs tailles
 */
function uploadPhotoPdt(array $userfile_acc, string $nom_photo, string $repertoire, int $size1 = 800, int $size2 = 0, int $size3 = 0): string {
    if (!isset($userfile_acc['size']) || $userfile_acc['size'] <= 0) {
        return '';
    }
    
    if ($userfile_acc['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    
    $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
    $base_dir = $app_config['app_root'] . "/public/photos/" . $repertoire . "/";
    
    // Créer les répertoires nécessaires
    $dirs = ['zoom/', 'norm/', 'min/'];
    foreach ($dirs as $dir) {
        $full_dir = $base_dir . $dir;
        if (!is_dir($full_dir)) {
            mkdir($full_dir, 0755, true);
        }
    }
    
    // Vérifier le type de fichier
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $userfile_acc['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return '';
    }
    
    $temp_file = $base_dir . 'temp_' . uniqid() . '_' . $userfile_acc['name'];
    
    if (!move_uploaded_file($userfile_acc['tmp_name'], $temp_file)) {
        return '';
    }
    
    $safe_name = Slug($nom_photo);
    $result = '';
    
    // Traitement pour la grande taille (zoom)
    if ($size1 > 0) {
        $temp_zoom = $base_dir . 'zoom/' . basename($temp_file);
        copy($temp_file, $temp_zoom);
        $result = img_resize($temp_zoom, $size1, $base_dir . 'zoom/', $safe_name);
    }
    
    // Traitement pour la taille normale
    if ($size2 > 0) {
        $temp_norm = $base_dir . 'norm/' . basename($temp_file);
        copy($temp_file, $temp_norm);
        img_resize($temp_norm, $size2, $base_dir . 'norm/', $safe_name);
    }
    
    // Traitement pour la miniature
    if ($size3 > 0) {
        $temp_min = $base_dir . 'min/' . basename($temp_file);
        copy($temp_file, $temp_min);
        img_resize($temp_min, $size3, $base_dir . 'min/', $safe_name);
    }
    
    // Nettoyer le fichier temporaire
    if (file_exists($temp_file)) {
        unlink($temp_file);
    }
    
    return $result;
}

/**
 * Upload de photo avec traitement multiple (fonction legacy modernisée)
 */
function uploadPhoto(array $userfile_acc, string $nom_photo, string $chemin_site): string {
    if (!isset($userfile_acc['size']) || $userfile_acc['size'] <= 0) {
        return '';
    }
    
    if ($userfile_acc['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    
    $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
    $upload_dir = $app_config['app_root'] . $chemin_site . "produits/photos/UPLOAD/";
    $save_dir = $app_config['app_root'] . $chemin_site . "produits/photos/";
    
    // Créer les répertoires nécessaires
    $dirs = [$upload_dir, $save_dir . 'zoom/', $save_dir . 'norm/', $save_dir . 'min/'];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    $temp_file = $upload_dir . $userfile_acc['name'];
    
    if (!move_uploaded_file($userfile_acc['tmp_name'], $temp_file)) {
        return '';
    }
    
    // Générer un nom unique
    $safe_name = xtTraiter($nom_photo) . "-" . date("YmdHis");
    
    try {
        // Obtenir les dimensions
        $image_info = getimagesize($temp_file);
        if ($image_info === false) {
            unlink($temp_file);
            return '';
        }
        
        [$width, $height] = $image_info;
        $portrait = ($height > $width) ? 1 : 0;
        $ratio = $width / $height;
        
        // Dimensions pour les différentes tailles
        $large_width = 1000;
        $large_height = (int)round($large_width / $ratio);
        
        $norm_width = 600;
        $norm_height = (int)round($norm_width / $ratio);
        
        $min_width = 200;
        $min_height = (int)round($min_width / $ratio);
        
        // Traiter chaque taille
        $final_name = $safe_name . '.jpg';
        
        // Grande image (zoom)
        $zoom_file = $save_dir . 'zoom/' . $final_name;
        copy($temp_file, $zoom_file);
        if (!resize_image_to_dimensions($zoom_file, $large_width, $large_height)) {
            cleanup_files([$temp_file, $zoom_file]);
            return '';
        }
        
        // Image normale
        $norm_file = $save_dir . 'norm/' . $final_name;
        copy($temp_file, $norm_file);
        if (!resize_image_to_dimensions($norm_file, $norm_width, $norm_height)) {
            cleanup_files([$temp_file, $zoom_file, $norm_file]);
            return '';
        }
        
        // Miniature
        $min_file = $save_dir . 'min/' . $final_name;
        copy($temp_file, $min_file);
        if (!resize_image_to_dimensions($min_file, $min_width, $min_height)) {
            cleanup_files([$temp_file, $zoom_file, $norm_file, $min_file]);
            return '';
        }
        
        // Nettoyer le fichier source
        unlink($temp_file);
        
        return $final_name;
        
    } catch (Exception $e) {
        error_log("Erreur uploadPhoto: " . $e->getMessage());
        if (file_exists($temp_file)) {
            unlink($temp_file);
        }
        return '';
    }
}

/**
 * Redimensionne une image aux dimensions exactes
 */
function resize_image_to_dimensions(string $file_path, int $new_width, int $new_height): bool {
    if (!file_exists($file_path)) {
        return false;
    }
    
    $image_info = getimagesize($file_path);
    if ($image_info === false) {
        return false;
    }
    
    [$width, $height, $type] = $image_info;
    
    // Créer l'image source
    $source = match($type) {
        IMAGETYPE_JPEG => imagecreatefromjpeg($file_path),
        IMAGETYPE_PNG => imagecreatefrompng($file_path),
        IMAGETYPE_GIF => imagecreatefromgif($file_path),
        default => false
    };
    
    if ($source === false) {
        return false;
    }
    
    // Créer l'image destination
    $dest = imagecreatetruecolor($new_width, $new_height);
    if ($dest === false) {
        imagedestroy($source);
        return false;
    }
    
    // Préserver la transparence pour PNG
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
    }
    
    // Redimensionner
    $success = imagecopyresampled($dest, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    if ($success) {
        // Sauvegarder (toujours en JPEG pour cette fonction)
        $success = imagejpeg($dest, $file_path, 90);
    }
    
    imagedestroy($source);
    imagedestroy($dest);
    
    return $success;
}

/**
 * Nettoie une liste de fichiers
 */
function cleanup_files(array $files): void {
    foreach ($files as $file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }
}

/**
 * Valide un fichier uploadé
 */
function validate_upload(array $file, array $allowed_types = [], int $max_size = 10485760): array {
    $result = ['valid' => false, 'error' => ''];
    
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'Erreur lors du téléchargement du fichier';
        return $result;
    }
    
    if ($file['size'] > $max_size) {
        $result['error'] = 'Le fichier est trop volumineux';
        return $result;
    }
    
    if (!empty($allowed_types)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
            $result['error'] = 'Type de fichier non autorisé';
            return $result;
        }
    }
    
    $result['valid'] = true;
    return $result;
}
?>