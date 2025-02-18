<?php

namespace Squadro;

class PartieSquadro {
    const PLAYER_ONE = 0;
    const PLAYER_TWO = 1;

    private int $partieId;
    private array $joueurs;
    private int $joueurActif;
    private string $gameStatus;
    private $plateau; // Placeholder pour PlateasuSquadro

    public function __construct(JoueurSquadro $playerOne) {
        $this->partieId = 0;
        $this->joueurs = [$playerOne];
        $this->joueurActif = self::PLAYER_ONE;
        $this->gameStatus = 'initialized';
        $this->plateau = null; // À implémenter si nécessaire
    }

    public function addJoueur(JoueurSquadro $player): void {
        $this->joueurs[] = $player;
    }

    public function getJoueurActif(): JoueurSquadro {
        return $this->joueurs[$this->joueurActif];
    }

    public function setJoueurActif(int $nom): void {
        $this->joueurActif = $nom;
    }

    public function getPartieID(): int {
        return $this->partieId;
    }

    public function setPartieID(int $id): void {
        $this->partieId = $id;
    }

    public function getJoueurs(): array {
        return $this->joueurs;
    }

    public function toJson(): string {
        return json_encode([
            'partieId' => $this->partieId,
            'joueurs' => array_map(fn($joueur) => json_decode($joueur->toJson(), true), $this->joueurs),
            'joueurActif' => $this->joueurActif,
            'gameStatus' => $this->gameStatus
        ]);
    }

    public static function fromJson(string $jsonString): PartieSquadro {
        $data = json_decode($jsonString, true);
        $partie = new PartieSquadro(JoueurSquadro::fromJson(json_encode($data['joueurs'][0])));
        foreach (array_slice($data['joueurs'], 1) as $joueur) {
            $partie->addJoueur(JoueurSquadro::fromJson(json_encode($joueur)));
        }
        $partie->setPartieID($data['partieId']);
        $partie->setJoueurActif($data['joueurActif']);
        return $partie;
    }
}

