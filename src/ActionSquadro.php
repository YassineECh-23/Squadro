<?php

namespace Squadro;

use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;

class ActionSquadro
{

    private PlateauSquadro $plateau;
    private int $countBlancSortie = 0;
    private int $countNoirSortie = 0;
    private bool $partieTerminee = false; // Flag pour bloquer le jeu après la victoire
    private array $historiquePositions = [];

    public function __construct(PlateauSquadro $plateau)
    {
        $this->plateau = $plateau;
        if(!isset($_SESSION['countNoirSortie'])){
            $_SESSION['countNoirSortie'] = $this->countNoirSortie;
        }
        if(!isset($_SESSION['countBlancSortie'])) {
            $_SESSION['countBlancSortie'] = $this->countBlancSortie;
        }
    }

    public function estJouablePiece(int $x, int $y): bool{
        $piece = $this->plateau->getPiece($x, $y);
        return !$this->partieTerminee && $piece->getCouleur() === $_SESSION['joueurActif'];
    }

    public function jouerPiece(int $x, int $y): void
    {
        if ($this->partieTerminee) {
            return; // Empêcher les mouvements après la victoire
        }

        $piece = $this->plateau->getPiece($x, $y);
        if ($piece->getCouleur() !== $_SESSION['joueurActif']) {
            throw new \InvalidArgumentException("Cette pièce ne vous appartient pas !");
        }

        [$newX, $newY] = $this->plateau->getCoordDestination($x, $y);

        if ($newX < 0 || $newX >= 7 || $newY < 0 || $newY >= 7) {
            throw new \OutOfBoundsException(" Mouvement hors limites !");
        }

        //  Vérifier si la case d'arrivée est libre
        if ($this->plateau->getPiece($newX, $newY)->getCouleur() !== PieceSquadro::VIDE) {
            return; //  Empêcher le déplacement si la case est occupée
        }

        $this->gererCollisionsSurTrajet($x, $y, $newX, $newY, $piece);
        $this->gererCollisionsMultiples($newX, $newY, $piece);
        $this->historiquePositions["$newX-$newY"][] = ["x" => $x, "y" => $y];

        $this->plateau->setPiece($newX, $newY, $piece);
        $this->plateau->setPiece($x, $y, PieceSquadro::initVide());

        if ($this->aAtteintZoneRetournement($newX, $newY, $piece)) {
            $piece->inverseDirection();
        }

        if ($this->aTermineAllerRetour($newX, $newY)) {
            $this->sortirPiece($piece->getCouleur(), $newX, $newY);
        }

        // Vérification de victoire après chaque coup
        if ($this->remporteVictoire($piece->getCouleur())) {
            $this->afficherMessageVictoire();
        }
    }


    private function gererCollisionsSurTrajet(int $x, int $y, int $newX, int $newY, PieceSquadro $piece)
    {
        $couleur = $piece->getCouleur();

        if ($couleur === PieceSquadro::BLANC) {
            for ($col = min($y, $newY) + 1; $col <= max($y, $newY); $col++) {
                $pieceAdverse = $this->plateau->getPiece($x, $col);
                if ($pieceAdverse->getCouleur() === PieceSquadro::NOIR) {
                    $this->reculePiece($x, $col );
                }
            }
        } else {
            for ($row = min($x, $newX) + 1; $row <= max($x, $newX); $row++) {
                $pieceAdverse = $this->plateau->getPiece($row, $y);
                if ($pieceAdverse->getCouleur() === PieceSquadro::BLANC) {
                    $this->reculePiece($row, $y );
                }
            }
        }
    }

    private function reculePiece(int $x, int $y): void
{
    $pieceAdverse = $this->plateau->getPiece($x, $y);

    if ($this->aDejaEffectueAller($pieceAdverse)) {
        if ($pieceAdverse->getCouleur() === PieceSquadro::BLANC) {
            $this->plateau->setPiece($x, 6, $pieceAdverse);
        } else {
            $this->plateau->setPiece(0, $y, $pieceAdverse);
        }
    } else {
        if ($pieceAdverse->getCouleur() === PieceSquadro::BLANC) {
            $this->plateau->setPiece($x, 0, $pieceAdverse);
        } else {
            $this->plateau->setPiece(6, $y, $pieceAdverse);
        }
    }

    $this->plateau->setPiece($x, $y, PieceSquadro::initVide());
}

    private function gererCollisionsMultiples(int $x, int $y, PieceSquadro $piece)
    {
        $pieceSurCase = $this->plateau->getPiece($x, $y);

        if ($pieceSurCase->getCouleur() !== PieceSquadro::VIDE && $pieceSurCase->getCouleur() !== $piece->getCouleur()) {
            $this->reculePiece($x, $y);
            $this->plateau->setPiece($x, $y, $piece);
        }
    }

    private function aAtteintZoneRetournement(int $x, int $y, PieceSquadro $piece): bool
    {
        return ($piece->getCouleur() === PieceSquadro::BLANC && $y === 6) ||
            ($piece->getCouleur() === PieceSquadro::NOIR && $x === 0);
    }

    public function aTermineAllerRetour(int $x, int $y): bool
    {
        return ($x === 6 && $this->plateau->getPiece($x, $y)->getCouleur() === PieceSquadro::NOIR) ||
            ($y === 0 && $this->plateau->getPiece($x, $y)->getCouleur() === PieceSquadro::BLANC);
    }

    private function aDejaEffectueAller(PieceSquadro $piece): bool
    {
        return ($piece->getCouleur() === PieceSquadro::BLANC && $piece->getDirection() === PieceSquadro::OUEST) ||
            ($piece->getCouleur() === PieceSquadro::NOIR && $piece->getDirection() === PieceSquadro::SUD);
    }

    public function sortirPiece(int $couleur, int $x, int $y): void
    {
        $this->plateau->setPiece($x, $y, PieceSquadro::initVide());

        if ($couleur === PieceSquadro::BLANC) {
            if (isset($_SESSION['countBlancSortie'])) {
                $_SESSION['countBlancSortie'] = $_SESSION['countBlancSortie'] + 1;
            } else {
                $_SESSION['countBlancSortie'] = $this->countBlancSortie;
            }
        } else {
            if (isset($_SESSION['countNoirSortie'])) {
                $_SESSION['countNoirSortie'] = $_SESSION['countNoirSortie'] + 1; ;
            } else {
                $_SESSION['countNoirSortie'] = $this->countNoirSortie;
            }
        }
    }

        public function remporteVictoire(int $couleur): bool
    {
        if ($couleur === PieceSquadro::BLANC) {
            return $_SESSION['countBlancSortie'] >= 4;
        } else {
            return $_SESSION['countNoirSortie'] >= 4;
        }
    }

    private function afficherMessageVictoire(): void
    {
        $gagnant = ($_SESSION['countBlancSortie'] >= 4) ? PieceSquadro::BLANC : PieceSquadro::NOIR;
        echo SquadroUIGenerator::genererPageVictoire($this->plateau, $gagnant);
        $this->partieTerminee = true;
        exit();
    }
}
