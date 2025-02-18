<?php

namespace Squadro;

use ArrayAccess;
use Countable;
use InvalidArgumentException;

class ArrayPieceSquadro implements Countable, ArrayAccess
{
    private array $pieces;

    public function __construct(array $pieces = [])
    {
        $this->pieces = $pieces;
    }

    public function count(): int
    {
        return count($this->pieces);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->pieces[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->pieces[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->pieces[] = $value;
        } else {
            $this->pieces[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->pieces[$offset]);
    }

    public function add(PieceSquadro $piece): void
    {
        $this->pieces[] = $piece;
    }

    public function remove(int $index): void
    {
        if (isset($this->pieces[$index])) {
            unset($this->pieces[$index]);
            $this->pieces = array_values($this->pieces); // Réindexer le tableau
        }
    }

    public function __toString(): string
    {
        return "ArrayPieceSquadro{" . implode(", ", array_map(fn($piece) => $piece->__toString(), $this->pieces)) . "}";
    }

    public function toJson(): string
    {
        $piecesData = array_map(function ($piece) {
            return [
                'couleur' => $piece->getCouleur(),
                'direction' => $piece->getDirection()
            ];
        }, $this->pieces);

        return json_encode($piecesData);
    }

    public static function fromJson(string $json): ArrayPieceSquadro
    {
        $piecesData = json_decode($json, true); // Décoder en tableau associatif

        if (!is_array($piecesData)) {
            throw new InvalidArgumentException("Données JSON invalides");
        }

        $pieces = [];
        foreach ($piecesData as $pieceData) {
            if (!isset($pieceData['couleur'], $pieceData['direction'])) {
                throw new InvalidArgumentException("Données JSON invalides");
            }
            $pieces[] = PieceSquadro::fromJson(json_encode($pieceData)); // Encoder en JSON avant de passer à fromJson
        }

        return new ArrayPieceSquadro($pieces);
    }
}