<?php


namespace wishlist\fonction;

use wishlist\modele\Item;


class GestionImage {

    public static function imageUpload($item)
    {
        if (SELF::imageVerify($_FILES)) {
            if ($item->img) unlink($item->img);
            if (!is_dir('img/')) mkdir('img/');

            $nom = "img/$item->id-icone.png";
            $resultat = move_uploaded_file($_FILES['icone']['tmp_name'],$nom);
            if ($resultat) echo "Transfert réussi";

            $item->img = $nom;
            $item->save();
        }
    }

    public static function imageDelete($item)
    {
        if ($item->img) unlink($item->img);
        $item->img = NULL;
        $item->save();
    }

    public static function imageVerify($file)
    {
        if ($_FILES['icone']['error'] > 0) {
            Alerte::set('transfer_error');
            return false;
        }

        if ($_FILES['icone']['size'] > 10000000) {
            Alerte::set('max_file_size');
            return false;
        }

        $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
        $extension_upload = strtolower(  substr(  strrchr($_FILES['icone']['name'], '.')  ,1)  );
        if ( !in_array($extension_upload, $extensions_valides) ) {
            Alerte::set('invalide_extension');
            return false;
        }

        return true;
    }

}