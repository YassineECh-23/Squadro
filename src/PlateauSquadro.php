<?php

namespace Squadro;

use Squadro\PieceSquadro;
require_once '../src/PieceSquadro.php';

class PlateauSquadro {
    // Constantes pour les vitesses de déplacement
    public const BLANC_V_ALLER = [0, 1, 3, 2, 3, 1, 0];
    public const BLANC_V_RETOUR = [0, 3, 1, 2, 1, 3, 0];
    public const NOIR_V_ALLER = [0, 3, 1, 2, 1, 3, 0];
    public const NOIR_V_RETOUR = [0, 1, 3, 2, 3, 1, 0];

    // Attributs
    private array $plateau = []; // Plateau 7x7
    private array $lignesJouables = [1, 2, 3, 4, 5]; // Lignes jouables (blanc)
    private array $colonnesJouables = [1, 2, 3, 4, 5]; // Colonnes jouables (noir)

    // Constructeur
    public function __construct() {
        $this->initPlateau();
    }

    /**
     * Initialise le plateau de jeu avec les pièces et les cases vides.
     */
    private function initPlateau(): void {
        $this->initCasesVides();
        $this->initCasesNeutres();
        $this->initCasesBlanches();
        $this->initCasesNoires();
    }

    /**
     * Initialise les cases vides du plateau.
     */
    private function initCasesVides(): void {
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->plateau[$i][$j] = PieceSquadro::initVide();
            }
        }
    }

    /**
     * Initialise les cases neutres du plateau.
     */
    private function initCasesNeutres(): void {
        $this->plateau[0][0] = PieceSquadro::initNeutre();
        $this->plateau[0][6] = PieceSquadro::initNeutre();
        $this->plateau[6][0] = PieceSquadro::initNeutre();
        $this->plateau[6][6] = PieceSquadro::initNeutre();
    }

    /**
     * Initialise les cases contenant les pièces blanches.
     */
    private function initCasesBlanches(): void {
        for ($j = 1; $j <= 5; $j++) {
            $this->plateau[$j][0] = PieceSquadro::initBlancEst();
        }
    }

    /**
     * Initialise les cases contenant les pièces noires.
     */
    private function initCasesNoires(): void {
        for ($i = 1; $i <= 5; $i++) {
            $this->plateau[6][$i] = PieceSquadro::initNoirNord();
        }
    }

    /**
     * Retourne le plateau de jeu sous forme de tableau.
     */
    public function getPlateau(): array {
        return $this->plateau;
    }

    /**
     * Récupère une pièce aux coordonnées données.
     */
    public function getPiece(int $x, int $y): PieceSquadro {
        if ($x < 0 || $x >= 7 || $y < 0 || $y >= 7) {
            throw new \OutOfBoundsException("Coordonnées invalides ($x, $y)");
        }
        return $this->plateau[$x][$y];
    }

    /**
     * Place une pièce à une position spécifique.
     */
    public function setPiece(int $x, int $y, PieceSquadro $piece): void {
        if ($x < 0 || $x >= 7 || $y < 0 || $y >= 7) {
            throw new \OutOfBoundsException("Coordonnées invalides ($x, $y)");
        }
        $this->plateau[$x][$y] = $piece;
    }

    /**
     * Retourne les indices des lignes jouables.
     */
    public function getLignesJouables(): array {
        return $this->lignesJouables;
    }

    /**
     * Retourne les indices des colonnes jouables.
     */
    public function getColonnesJouables(): array {
        return $this->colonnesJouables;
    }

    /**
     * Supprime une ligne jouable.
     */
    public function retireLigneJouable(int $index): void {
        $this->lignesJouables = array_values(array_diff($this->lignesJouables, [$index]));
    }

    /**
     * Supprime une colonne jouable.
     */
    public function retireColonneJouable(int $index): void {
        $this->colonnesJouables = array_values(array_diff($this->colonnesJouables, [$index]));
    }

    /**
     * Calcule les coordonnées de destination d'une pièce.
     */
    public function getCoordDestination(int $x, int $y): array {
        $piece = $this->getPiece($x, $y);
        if ($piece->getCouleur() === PieceSquadro::BLANC) {
            // Pièce blanche (horizontal)
            if ($piece->getDirection() === PieceSquadro::EST) {
                $vitesse = self::BLANC_V_ALLER[$x];
                $newX = $x;
                $newY = $y + $vitesse;
            } else {
                $vitesse = self::BLANC_V_RETOUR[$x];
                $newX = $x;
                $newY = $y - $vitesse;
            }
        } else {
            // Pièce noire (vertical)
            if ($piece->getDirection() === PieceSquadro::NORD) {
                $vitesse = self::NOIR_V_ALLER[$y];
                $newX = $x - $vitesse;
                $newY = $y;
            } else {
                $vitesse = self::NOIR_V_RETOUR[$y];
                $newX = $x + $vitesse;
                $newY = $y;
            }
        }

        // Vérification des limites
        if ($newX < 0 || $newX >= 7 || $newY < 0 || $newY >= 7) {
            throw new \OutOfBoundsException("Mouvement hors limites ($newX, $newY)");
        }

        return [$newX, $newY];
    }

    /**
     * Retourne une représentation JSON du plateau.
     */
    public function toJson(): string
    {
        $plateauArray = [];

        foreach ($this->plateau as $x => $row) {
            foreach ($row as $y => $piece) {
                $plateauArray[$x][$y] = json_decode($piece->toJson(), true);
            }
        }

        return json_encode($plateauArray);
    }

    /**
     * Crée un plateau à partir d'un JSON.
     */
    public static function fromJson(string $json): PlateauSquadro
    {
        $data = json_decode($json, true);
        $instance = new self();

        foreach ($data as $x => $row) {
            foreach ($row as $y => $cell) {
                if (is_array($cell)) {
                    $instance->plateau[$x][$y] = PieceSquadro::fromJson(json_encode($cell));
                } else {
                    $instance->plateau[$x][$y] = PieceSquadro::initVide();
                }
            }
        }

        return $instance;
    }

    /**
     * Retourne une représentation textuelle du plateau.
     */
    public function __toString(): string {
        $result = "PlateauSquadro :\n";
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $piece = $this->plateau[$i][$j];
                $result .= match ($piece->getCouleur()) {
                    PieceSquadro::BLANC => " B ",
                    PieceSquadro::NOIR => " N ",
                    PieceSquadro::NEUTRE => " X ",
                    default => " . ",
                };
            }
            $result .= "\n";
        }
        return $result;
    }
}