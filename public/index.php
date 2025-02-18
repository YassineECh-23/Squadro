<?php
session_start();

use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;
use Squadro\SquadroUIGenerator;
use Squadro\ActionSquadro;

require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/ActionSquadro.php';
require_once '../src/SquadroUIGenerator.php';

// Initialisation des compteurs
$compteurBlanc = $_SESSION['countBlancSortie'] ?? 0;
$compteurNoir = $_SESSION['countNoirSortie'] ?? 0;

// Vérification de l'existence du plateau, sinon initialisation
if (!isset($_SESSION['plateau'])) {
    $plateau = new PlateauSquadro();
    $_SESSION['plateau'] = $plateau->toJson(); // Sauvegarde en JSON
    $_SESSION['joueurActif'] = PieceSquadro::BLANC; // Les Blancs commencent
} else {
    // Charger le plateau depuis JSON
    try {
        $plateau = PlateauSquadro::fromJson($_SESSION['plateau']);
    } catch (Exception $e) {
        echo SquadroUIGenerator::genererPageErreur("Erreur de chargement du plateau : " . $e->getMessage());
        session_destroy();
        exit;
    }
}

// Vérification de l'état du jeu
$actionSquadro = new ActionSquadro($plateau);
$joueurActif = $_SESSION['joueurActif'];

if ($actionSquadro->remporteVictoire($joueurActif)) {
    $_SESSION['partieTerminee'] = true;
    $_SESSION['joueurGagnant'] = $joueurActif;
    echo SquadroUIGenerator::genererPageVictoire($plateau, $joueurActif);
    session_destroy();
    exit;
}

// Vérification si une pièce a été sélectionnée mais pas encore confirmée
if (isset($_SESSION['pieceSelectionnee'])) {
    $pieceSelectionnee = $_SESSION['pieceSelectionnee'];
    echo SquadroUIGenerator::genererPageConfirmerDeplacement(
        $pieceSelectionnee['ligne'],
        $pieceSelectionnee['colonne'],
        $plateau,
        $joueurActif
    );
    exit;
}

// Sauvegarde du plateau en JSON après toute modification
$_SESSION['plateau'] = $plateau->toJson();

// Générer la page du jeu
echo SquadroUIGenerator::genererPageJeu($plateau, $joueurActif, $compteurBlanc, $compteurNoir);
