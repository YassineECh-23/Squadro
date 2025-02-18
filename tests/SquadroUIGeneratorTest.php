<?php

use PHPUnit\Framework\TestCase;
use Squadro\SquadroUIGenerator;
use Squadro\PlateauSquadro;
use Squadro\PieceSquadro;

class SquadroUIGeneratorTest extends TestCase
{
    private PlateauSquadro $plateau;

    protected function setUp(): void
    {
        $this->plateau = new PlateauSquadro();
    }

    public function testGenererEntete()
    {
        $html = SquadroUIGenerator::genererEntete("Test Title");
        $this->assertStringContainsString("<title>Test Title</title>", $html);
        $this->assertStringContainsString("<html lang=\"fr\">", $html);
    }

    public function testGenererPiedDePage()
    {
        $html = SquadroUIGenerator::genererPiedDePage();
        $this->assertStringContainsString("</body></html>", $html);
    }

    public function testGenererPageJouerPiece()
    {
        $html = SquadroUIGenerator::genererPageJouerPiece($this->plateau, PieceSquadro::BLANC);
        $this->assertStringContainsString("Jouer une pièce", $html);
        $this->assertStringContainsString("C'est au tour des <span class=\"font-semibold\">Blancs</span>", $html);
    }

    public function testGenererPageConfirmerDeplacement()
    {
        $html = SquadroUIGenerator::genererPageConfirmerDeplacement(2, 3, $this->plateau, PieceSquadro::NOIR);
        $this->assertStringContainsString("Confirmer le déplacement", $html);
        $this->assertStringContainsString("(2, 3)", $html);
    }

    public function testGenererPageVictoire()
    {
        $html = SquadroUIGenerator::genererPageVictoire($this->plateau, PieceSquadro::BLANC);
        $this->assertStringContainsString("Victoire !", $html);
        $this->assertStringContainsString("Les <span class=\"font-semibold\">Blancs</span> ont gagné !", $html);
    }

    public function testGenererPageErreur()
    {
        $html = SquadroUIGenerator::genererPageErreur("Erreur de test");
        $this->assertStringContainsString("Erreur !", $html);
        $this->assertStringContainsString("Erreur de test", $html);
    }

    public function testGenererCardBlanche()
    {
        $html = SquadroUIGenerator::genererCardBlanche(3);
        $this->assertStringContainsString("Pièces blanches sorties", $html);
        $this->assertStringContainsString("<p class=\"text-3xl\">3</p>", $html);
    }

    public function testGenererCardNoir()
    {
        $html = SquadroUIGenerator::genererCardNoir(5);
        $this->assertStringContainsString("Pièces noires sorties", $html);
        $this->assertStringContainsString("<p class=\"text-3xl\">5</p>", $html);
    }
}
