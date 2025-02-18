<?php

namespace Squadro;

class JoueurSquadro{

// Attributs de la classe
    public string $name; // Le nom du joueur
    public int $id; // L'identifiant du joueur

    // Méthodes pour accéder et modifier le nom du joueur
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    // Méthodes pour accéder et modifier l'identifiant du joueur
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // Méthode pour obtenir une représentation textuelle du joueur
    public function __toString(): string
    {
        return '('.$this->id.')'.$this->name;
    }

    // Méthode pour obtenir une représentation JSON du joueur
    public function getJson():string {
        return '{"name":"'.$this->name.'","id":'.$this->id.'}';
    }

    // Méthode statique pour initialiser un joueur à partir de sa représentation JSON
    public static function initPlayer(string $json): JoueurSquadro
    {
        $player = new JoueurSquadro(); // Création d'un nouvel objet joueur
        $object = json_decode($json); // Décodage de la chaîne JSON
        // Définition du nom et de l'identifiant du joueur à partir des données JSON
        $player->setName($object->name);
        $player->setId($object->id);
        return $player; // Retourne l'objet joueur initialisé
    }

}