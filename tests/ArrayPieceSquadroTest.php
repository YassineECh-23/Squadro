<?php

namespace Squadro\Tests;

use PHPUnit\Framework\TestCase;
use Squadro\ArrayPieceSquadro;
use Squadro\PieceSquadro;

class ArrayPieceSquadroTest extends TestCase
{
    private ArrayPieceSquadro $arrayPieces;

    protected function setUp(): void
    {
        // Initialise un tableau de pièces avant chaque test
        $this->arrayPieces = new ArrayPieceSquadro();
    }

    /**
     * Teste la méthode count.
     */
    public function testCount(): void
    {
        // Vérifie que le tableau est vide au départ
        $this->assertEquals(0, $this->arrayPieces->count());

        // Ajoute une pièce et vérifie que le compteur est mis à jour
        $piece = PieceSquadro::initBlancEst();
        $this->arrayPieces->add($piece);
        $this->assertEquals(1, $this->arrayPieces->count());
    }

    /**
     * Teste la méthode offsetExists.
     */
    public function testOffsetExists(): void
    {
        // Vérifie qu'une pièce n'existe pas à un index donné
        $this->assertFalse($this->arrayPieces->offsetExists(0));

        // Ajoute une pièce et vérifie qu'elle existe à l'index 0
        $piece = PieceSquadro::initNoirNord();
        $this->arrayPieces->add($piece);
        $this->assertTrue($this->arrayPieces->offsetExists(0));
    }

    /**
     * Teste la méthode offsetGet.
     */
    public function testOffsetGet(): void
    {
        // Ajoute une pièce et vérifie qu'elle peut être récupérée
        $piece = PieceSquadro::initBlancOuest();
        $this->arrayPieces->add($piece);
        $this->assertEquals($piece, $this->arrayPieces->offsetGet(0));

        // Vérifie que null est retourné pour un index inexistant
        $this->assertNull($this->arrayPieces->offsetGet(1));
    }

    /**
     * Teste la méthode offsetSet.
     */
    public function testOffsetSet(): void
    {
        // Ajoute une pièce à un index spécifique
        $piece = PieceSquadro::initNoirSud();
        $this->arrayPieces->offsetSet(0, $piece);
        $this->assertEquals($piece, $this->arrayPieces->offsetGet(0));

        // Ajoute une pièce sans spécifier d'index
        $piece2 = PieceSquadro::initBlancEst();
        $this->arrayPieces->offsetSet(null, $piece2);
        $this->assertEquals($piece2, $this->arrayPieces->offsetGet(1));
    }

    /**
     * Teste la méthode offsetUnset.
     */
    public function testOffsetUnset(): void
    {
        // Ajoute une pièce et vérifie qu'elle peut être supprimée
        $piece = PieceSquadro::initNoirNord();
        $this->arrayPieces->add($piece);
        $this->arrayPieces->offsetUnset(0);
        $this->assertFalse($this->arrayPieces->offsetExists(0));
    }

    /**
     * Teste la méthode add.
     */
    public function testAdd(): void
    {
        // Ajoute une pièce et vérifie qu'elle est bien ajoutée
        $piece = PieceSquadro::initBlancEst();
        $this->arrayPieces->add($piece);
        $this->assertEquals($piece, $this->arrayPieces->offsetGet(0));
    }

    /**
     * Teste la méthode remove.
     */
    public function testRemove(): void
    {
        // Ajoute une pièce et vérifie qu'elle peut être supprimée
        $piece = PieceSquadro::initNoirSud();
        $this->arrayPieces->add($piece);
        $this->arrayPieces->remove(0);
        $this->assertFalse($this->arrayPieces->offsetExists(0));
    }

    /**
     * Teste la méthode __toString.
     */
    public function testToString(): void
    {
        // Ajoute des pièces et vérifie la représentation en chaîne de caractères
        $piece1 = PieceSquadro::initBlancEst();
        $piece2 = PieceSquadro::initNoirNord();
        $this->arrayPieces->add($piece1);
        $this->arrayPieces->add($piece2);

        $expectedString = "ArrayPieceSquadro{" . $piece1->__toString() . ", " . $piece2->__toString() . "}";
        $this->assertEquals($expectedString, $this->arrayPieces->__toString());
    }

    /**
     * Teste la méthode toJson.
     */
    public function testToJson(): void
    {
        // Ajoute des pièces au tableau
        $piece1 = PieceSquadro::initBlancEst();
        $piece2 = PieceSquadro::initNoirNord();
        $this->arrayPieces->add($piece1);
        $this->arrayPieces->add($piece2);

        // Convertit le tableau en JSON
        $json = $this->arrayPieces->toJson();

        // Vérifie que le JSON correspond à la sortie attendue
        $expectedJson = '[{"couleur":0,"direction":1},{"couleur":1,"direction":0}]';
        $this->assertEquals($expectedJson, $json);
    }

    /**
     * Teste la méthode fromJson.
     */
    public function testFromJson(): void
    {
        // Crée un tableau de pièces
        $piece1 = PieceSquadro::initBlancEst();
        $piece2 = PieceSquadro::initNoirNord();
        $this->arrayPieces->add($piece1);
        $this->arrayPieces->add($piece2);

        // Convertit le tableau en JSON
        $json = $this->arrayPieces->toJson();

        // Convertit le JSON en tableau de pièces
        $newArrayPieces = ArrayPieceSquadro::fromJson($json);

        // Vérifie que les pièces sont correctement restaurées
        $this->assertEquals($piece1, $newArrayPieces->offsetGet(0));
        $this->assertEquals($piece2, $newArrayPieces->offsetGet(1));
    }
}