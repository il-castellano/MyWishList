<?php


namespace mywishlist\controleurs;


use mywishlist\models\Message;
use mywishlist\vues\VueMessages;

class ControleurMessage
{
    public static function getMessages($token_liste){
        $messages = Message::all()->where('tokenListe','=',$token_liste);
        $vue = new VueMessages(['messages' => $messages, 'tokenListe' => $token_liste]);
        $vue->afficher('messages');
        $vue->afficher('form');
    }

    public static function ajouterMessage($token_liste){
        $message = new Message();
        $message->tokenListe = $token_liste;
        $message->date = date("Y-m-d H:i:s");
        if (isset($_POST['nom'])){
            $message->nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        }
        else{
            $message->nom = 'Anonyme';
        }
        if (isset($_POST['message'])){
            $message->message = filter_var($_POST['message'], FILTER_SANITIZE_SPECIAL_CHARS);
        }
        else{
            $message->message = '';
        }

        $message->save();

    }
}