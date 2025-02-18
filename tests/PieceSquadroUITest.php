<?php

use PHPUnit\Framework\TestCase;
use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;
use Squadro\PieceSquadroUI;

class PieceSquadroUITest extends TestCase
{
    private PlateauSquadro $plateau;

    protected function setUp(): void
    {
        // Initialiser un plateau pour les tests
        $this->plateau = new PlateauSquadro();
    }

    public function testGenerationCaseVide()
    {
        $output = PieceSquadroUI::generationCaseVide();
        $expected = '<button type="button" class="h-12 w-12 border border-gray-400 bg-gray-300 rounded-lg" disabled></button>';
        $this->assertEquals($expected, $output);
    }

    public function testGenerationCaseNeutre()
    {
        $output = PieceSquadroUI::generationCaseNeutre();
        $expected = '<button type="button" class="h-12 w-12 border border-gray-600 bg-gray-500 rounded-lg" disabled></button>';
        $this->assertEquals($expected, $output);
    }

    public function testGenerationCaseRouge()
    {
        $output = PieceSquadroUI::generationCaseRouge(3);
        $expected = '<div class="h-12 w-12 flex items-center justify-center border border-red-600 bg-red-500 text-white font-bold rounded-lg">3</div>';
        $this->assertEquals($expected, $output);
    }

    public function testGenerationPieceJouable()
    {
        $piece = PieceSquadro::initBlancEst();
        $output = PieceSquadroUI::generationPiece($piece, 1, 0, true, $this->plateau);

        // Vérifier si le bouton contient bien un formulaire permettant le déplacement
        $this->assertStringContainsString('<form action="traiteActionSquadro.php"', $output);
        $this->assertStringContainsString('<button class="h-12 w-12 border rounded-full shadow-md bg-white border-black"', $output);
    }

    public function testGenerationPieceInjouable()
    {
        $piece = PieceSquadro::initNoirNord();
        $output = PieceSquadroUI::generationPiece($piece, 6, 1, false, $this->plateau);

        // Vérifier si le bouton est désactivé (pièce adverse ou bloquée)
        $this->assertStringContainsString('<button class="h-12 w-12 border rounded-full shadow-md bg-black border-white cursor-not-allowed"', $output);
        $this->assertStringContainsString('disabled', $output);
    }
    public function testGenerationPieceBloquee()
    {
        $plateau = new PlateauSquadro();

        // Place une pièce blanche à (2,2)
        $pieceBlanche = PieceSquadro::initBlancEst();
        $plateau->setPiece(2, 2, $pieceBlanche);

        // Place une autre pièce (adverse ou alliée) à la case d’arrivée prévue pour (2,2)
        [$newX, $newY] = $plateau->getCoordDestination(2, 2);
        $plateau->setPiece($newX, $newY, PieceSquadro::initNoirNord());

        // Générer le bouton HTML
        $output = PieceSquadroUI::generationPiece($pieceBlanche, 2, 2, true, $plateau);

        // Vérifier que la pièce est bien bloquée (cursor-not-allowed)
        $this->assertStringContainsString('cursor-not-allowed', $output);
        $this->assertStringNotContainsString('<form', $output, "Le bouton ne doit pas être un formulaire activable");
    }

    public function testGeneratePlateau()
    {
        $output = PieceSquadroUI::generatePlateau($this->plateau, PieceSquadro::BLANC);

        // Vérifier si la structure du plateau est bien générée
        $this->assertStringContainsString('<table class="border-collapse border border-gray-800 mx-auto bg-gray-200">', $output);
        $this->assertStringContainsString('</table>', $output);

        // Vérifier que les cases rouges de vitesse sont bien présentes
        $this->assertStringContainsString('<div class="h-12 w-12 flex items-center justify-center border border-red-600 bg-red-500', $output);
    }
}

?>
