<?php


namespace mywishlist\controleurs;


use mywishlist\models\Liste;
use mywishlist\models\Reservation;

class ControleurReservation
{

    public static function reserverItem($id_item, $token_liste){
        $liste = Liste::all()->find($token_liste);
        $token = $liste['token'];

        //vérifie si la personne qui reserve n'est pas le créateur de la liste
        if (isset($_COOKIE['created'])) {
            $created = unserialize($_COOKIE['created']);
            if (in_array($token, $created)) { //créateur de la liste
                return null;
            }
        }
        //pas créateur
        //vérifie que pas dejà reservé
        $reserv = Reservation::all()->where('idItem','=',$id_item)->first();

        if(!isset($reserv)){ //pas deja reservé
            $token_reserv = "";
            try {
                $token_reserv = bin2hex(random_bytes(5));
            } catch (\Exception $e) {}

            if (isset($_COOKIE['reserves'])){
                $cookie = unserialize($_COOKIE['reserves']);
                array_push($cookie,$token_reserv);
                $cookie = serialize($cookie);
            }
            else{
                $cookie = serialize([$token_reserv]);
            }

            setcookie('reserves',$cookie,time()+60*60*24*365);

            $reserv = new Reservation();
            $reserv->idItem = $id_item;
            $reserv->tokenListe = $token_liste;
            $reserv->tokenReserv = $token_reserv;
            if (isset($_POST['nom'])){
                $reserv->nomParticipant = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
            }
            else{
                $reserv->nomParticipant = 'Anonymous';
            }
            if (isset($_POST['message'])){
                $reserv->message = filter_var($_POST['message'], FILTER_SANITIZE_SPECIAL_CHARS);
            }
            else{
                $reserv->message = '';
            }

            $reserv->save([$id_item, $token]);
        }
        else{ //déja reservé
            //TODO AFFICHER ERREUR DEJA RESERVE
        }
    }

    public static function annulerReservation($id_item,$token_liste){
        $reservation = Reservation::all()->where('idItem','=',$id_item)->where('tokenListe','=',$token_liste)->first();
        $token = $reservation['tokenReserv'];
        if (isset($_COOKIE['reserves'])) {
            $reserves = unserialize($_COOKIE['reserves']);
            if (in_array($token, $reserves)) {
                $reservation->delete();
            }
        }
    }
}