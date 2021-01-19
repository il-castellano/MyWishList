<?php

require_once 'vendor/autoload.php';

use mywishlist\controleurs\ControleurEditionListe;
use mywishlist\controleurs\ControleurHome;
use mywishlist\controleurs\ControleurListes;
use mywishlist\conf\Database;
use mywishlist\controleurs\ControleurCompte;
use mywishlist\controleurs\ControleurReservation;

session_start();
Database::connect();
$app = new \Slim\Slim();

//------------------------ HOME ------------------------\\
$app->get('/', function() {
    ControleurHome::default();
})->name('default');
//------------------------ HOME ------------------------\\


//------------------------ LISTE ------------------------\\
$app->get('/listes/', function() {
    //echo "Affiche toutes les listes";
    ControleurListes::getListes();
})->name('listes');

$app->post('/listes/', function() {
    //Affiche toutes les listes, en priorité celles recherchées avec la search bar
    ControleurListes::getListesRecherchees();
})->name('recherche_listes');

$app->post('/liste/create', function() {
    ControleurEditionListe::creerListe();
    ControleurEditionListe::afficherCreerListe();
});

$app->get('/liste/create', function() {
    //echo 'création d une liste';
    ControleurEditionListe::afficherCreerListe();
})->name('creation_liste');

$app->get('/liste/:token_liste', function($token_liste) {
    //Affiche la liste à partir de son token
    ControleurListes::getAllItems($token_liste);
})->name('route_liste');

$app->get('/liste/:token_liste/item/:id_item', function($token_liste, $id_item) {
    //Affiche l'item de la liste
    ControleurListes::getItem($id_item);
})->name('route_item');

$app->post('/liste/:token_liste/item/:id_item/reserver', function($token_liste, $id_item) {
    ControleurReservation::reserverItem($id_item, $token_liste);
    ControleurListes::getItem($id_item);
})->name('reserver_item');

$app->post('/liste/:token_liste/item/:id_item/annulerReservation', function($token_liste, $id_item) {
    ControleurReservation::annulerReservation($id_item, $token_liste);
    ControleurListes::getItem($id_item);
})->name('annuler_reservation');

$app->post('/liste/:token_liste/ajouterItem', function($token_liste) {
    ControleurEditionListe::ajouterItem($token_liste);
    ControleurListes::getAllItems($token_liste);
})->name('ajouter_item');

$app->get('/liste/:token_liste/ajouterItem', function() {
    ControleurEditionListe::afficherCreerItem();
})->name('form_ajout_item');

$app->get('/liste/:token_liste/item/:id_item/supprimerItem', function($token_liste,$id_item) {
    ControleurEditionListe::supprimerItem($token_liste,$id_item);
})->name('supprimer_item');

//------------------------ LISTE ------------------------\\
$app->post('/liste/:token_liste/ajouterMessage', function($token_liste) {
    \mywishlist\controleurs\ControleurMessage::ajouterMessage($token_liste);
    ControleurListes::getAllItems($token_liste);
})->name('ajouter_message');

//------------------------ COMPTE ------------------------\\
$app->get('/login', function() {
    ControleurCompte::deconnexion();
    ControleurCompte::pageConnexion();
})->name('login');

$app->get('/inscription', function () {
    ControleurCompte::pageInscription();
})->name('form_inscription');

$app->post('/inscription/succes', function () { //TODO changer la route car /inscription/succes bof
    $app = \Slim\Slim::getInstance() ;
    $pseudo = $app->request->post('pseudo');
    $login = $app->request->post('login');
    $pass = $app->request->post('password');
    ControleurCompte::inscription($login, $pass, $pseudo);
})->name('inscription');

$app->post('/connexion', function () {
    $app = \Slim\Slim::getInstance() ;
    $login = $app->request->post('login');
    $pass = $app->request->post('password');
    ControleurCompte::connexion($login, $pass);
})->name('connexion');

$app->get('/compte', function () {
   ControleurCompte::pageGestionCompte();
});

$app->post('/compte/modification', function () {
    $app = \Slim\Slim::getInstance() ;
    $pseudo = $app->request->post('pseudo');
    $pass = $app->request->post('password');
    ControleurCompte::modifierInformations($pseudo, $pass);
})->name('modificationCompte');

$app->post('/login', function () {
    ControleurCompte::supprimer();
    ControleurCompte::pageConnexion();
})->name('supprimerCompte');
//------------------------ COMPTE ------------------------\\

$app->run();