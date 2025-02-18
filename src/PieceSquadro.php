<?php
namespace Squadro;

use InvalidArgumentException;

class PieceSquadro {
    // Constantes pour les couleurs
    public const BLANC = 0;
    public const NOIR = 1;
    public const VIDE = -1;
    public const NEUTRE = -2;

    // Constantes pour les directions
    public const NORD = 0;
    public const EST = 1;
    public const SUD = 2;
    public const OUEST = 3;

    // Attributs protégés
    protected int $couleur;
    protected int $direction;

    /**
     * Constructeur privé pour éviter une instanciation directe incorrecte.
     */
    private function __construct(int $couleur, int $direction) {
        // Vérification des valeurs pour éviter des erreurs
        if (!in_array($couleur, [self::BLANC, self::NOIR, self::VIDE, self::NEUTRE], true)) {
            throw new InvalidArgumentException("Couleur invalide : $couleur");
        }
        if (!in_array($direction, [self::NORD, self::EST, self::SUD, self::OUEST, self::VIDE, self::NEUTRE], true)) {
            throw new InvalidArgumentException("Direction invalide : $direction");
        }

        $this->couleur = $couleur;
        $this->direction = $direction;
    }

    /**
     * Retourne la couleur de la pièce.
     */
    public function getCouleur(): int {
        return $this->couleur;
    }

    /**
     * Retourne la direction actuelle de la pièce.
     */
    public function getDirection(): int {
        return $this->direction;
    }

    /**
     * Inverse la direction de la pièce lorsqu'elle atteint un point de retournement.
     */
    public function inverseDirection(): void {
        if ($this->direction === self::NORD) {
            $this->direction = self::SUD;
        } elseif ($this->direction === self::SUD) {
            $this->direction = self::NORD;
        } elseif ($this->direction === self::EST) {
            $this->direction = self::OUEST;
        } elseif ($this->direction === self::OUEST) {
            $this->direction = self::EST;
        } else {
            throw new InvalidArgumentException("Impossible d'inverser la direction : valeur invalide.");
        }
    }

    /**
     * Retourne une représentation sous forme de texte de la pièce.
     */
    public function __toString(): string {
        $couleurs = [self::BLANC => "Blanc", self::NOIR => "Noir", self::VIDE => "Vide", self::NEUTRE => "Neutre"];
        $directions = [self::NORD => "Nord", self::EST => "Est", self::SUD => "Sud", self::OUEST => "Ouest"];

        $couleurStr = $couleurs[$this->couleur] ?? "Inconnu";
        $directionStr = $directions[$this->direction] ?? "Aucune";

        return "PieceSquadro [Couleur: $couleurStr, Direction: $directionStr]";
    }

    // --- Méthodes statiques pour créer différentes pièces du jeu ---

    /**
     * Crée une case vide.
     */
    public static function initVide(): PieceSquadro {
        return new self(self::VIDE, self::VIDE);
    }

    /**
     * Crée une case neutre.
     */
    public static function initNeutre(): PieceSquadro {
        return new self(self::NEUTRE, self::NEUTRE);
    }

    /**
     * Crée une pièce noire qui commence en bas et monte (vers le nord).
     */
    public static function initNoirNord(): PieceSquadro {
        return new self(self::NOIR, self::NORD);
    }

    /**
     * Crée une pièce noire qui commence en haut et descend (vers le sud).
     */
    public static function initNoirSud(): PieceSquadro {
        return new self(self::NOIR, self::SUD);
    }

    /**
     * Crée une pièce blanche qui commence à gauche et va vers la droite (est).
     */
    public static function initBlancEst(): PieceSquadro {
        return new self(self::BLANC, self::EST);
    }

    /**
     * Crée une pièce blanche qui commence à droite et va vers la gauche (ouest).
     */
    public static function initBlancOuest(): PieceSquadro {
        return new self(self::BLANC, self::OUEST);
    }

    // --- Gestion JSON ---

    /**
     * Convertit l'objet en JSON.
     */
    public function toJson(): string {
        return json_encode([
            'couleur' => $this->couleur,
            'direction' => $this->direction
        ]);
    }

    /**
     * Crée une pièce à partir d'une représentation JSON.
     */
    public static function fromJson(string $json): PieceSquadro {
        $data = json_decode($json, true);

        // Vérification des valeurs
        if (!isset($data['couleur'], $data['direction'])) {
            throw new InvalidArgumentException("Données JSON invalides");
        }

        return new self((int) $data['couleur'], (int) $data['direction']);
    }
}
