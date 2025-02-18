<?php
session_start();

use Squadro\PlateauSquadro;
use Squadro\ActionSquadro;
use Squadro\PieceSquadro;
use Squadro\SquadroUIGenerator;

require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/ActionSquadro.php';
require_once '../src/SquadroUIGenerator.php';

// Vérification des données POST
if (!isset($_POST['ligne']) || !isset($_POST['colonne'])) {
    echo SquadroUIGenerator::genererPageErreur("Erreur : Aucune pièce sélectionnée !");
    exit;
}

$ligne = (int)$_POST['ligne'];
$colonne = (int)$_POST['colonne'];

// Vérification du plateau dans la session
if (!isset($_SESSION['plateau'])) {
    echo SquadroUIGenerator::genererPageErreur("Erreur : PlateauSquadro absent !");
    exit;
}

// Charger le plateau depuis JSON
try {
    $plateau = PlateauSquadro::fromJson($_SESSION['plateau']);
} catch (Exception $e) {
    echo SquadroUIGenerator::genererPageErreur("Erreur critique : PlateauSquadro corrompu ! " . $e->getMessage());
    exit;
}

$joueurActif = $_SESSION['joueurActif'];
$actionSquadro = new ActionSquadro($plateau);

// Si l'utilisateur n'a pas encore confirmé, sauvegarder l'état et afficher la page de confirmation
if (!isset($_POST['confirmer']) && !isset($_POST['annuler'])) {
    $_SESSION['pieceSelectionnee'] = ['ligne' => $ligne, 'colonne' => $colonne];
    echo SquadroUIGenerator::genererPageConfirmerDeplacement($ligne, $colonne, $plateau, $joueurActif);
    exit;
}

// Suppression de la sélection après confirmation ou annulation
unset($_SESSION['pieceSelectionnee']);

// Si l'utilisateur a annulé, rediriger vers la page principale du jeu
if (isset($_POST['annuler'])) {
    header("Location: index.php");
    exit;
}

// Vérification si la pièce est jouable après confirmation
if (!$actionSquadro->estJouablePiece($ligne, $colonne, $joueurActif)) {
    echo SquadroUIGenerator::genererPageErreur("Cette pièce ne peut pas être déplacée !");
    exit;
}

// Jouer la pièce après confirmation
$actionSquadro->jouerPiece($ligne, $colonne, $joueurActif);

// Vérification de la victoire
if ($actionSquadro->remporteVictoire($joueurActif)) {
    session_destroy();
    exit;
}

// Changer de joueur
$_SESSION['joueurActif'] = ($joueurActif === PieceSquadro::BLANC) ? PieceSquadro::NOIR : PieceSquadro::BLANC;

// Sauvegarde de l'état du plateau en JSON
$_SESSION['plateau'] = $plateau->toJson();

// Redirection vers l'index pour afficher le nouveau plateau
header("Location: index.php");
exit;
