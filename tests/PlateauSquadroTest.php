<?php

use PHPUnit\Framework\TestCase;
use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;

class PlateauSquadroTest extends TestCase {



    public function testInitialisationPlateau() {
        $plateau = new PlateauSquadro();

        // Vérifie que les coins sont neutres
        $this->assertEquals(PieceSquadro::initNeutre(), $plateau->getPiece(0, 0));
        $this->assertEquals(PieceSquadro::initNeutre(), $plateau->getPiece(0, 6));
        $this->assertEquals(PieceSquadro::initNeutre(), $plateau->getPiece(6, 0));
        $this->assertEquals(PieceSquadro::initNeutre(), $plateau->getPiece(6, 6));

        // Vérifie que les cases de départ des pièces blanches sont correctement initialisées
        for ($j = 1; $j <= 5; $j++) {
            $this->assertEquals(PieceSquadro::initBlancEst(), $plateau->getPiece($j, 0));
        }

        // Vérifie que les cases de départ des pièces noires sont correctement initialisées
        for ($i = 1; $i <= 5; $i++) {
            $this->assertEquals(PieceSquadro::initNoirNord(), $plateau->getPiece(6, $i));
        }

        // Vérifie que les autres cases sont vides
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 5; $j++) {
                $this->assertEquals(PieceSquadro::initVide(), $plateau->getPiece($i, $j));
            }
        }
    }

    public function testSetPiece() {
        $plateau = new PlateauSquadro();
        $piece = PieceSquadro::initBlancEst();

        // Place une pièce blanche à une position
        $plateau->setPiece(2, 3, $piece);

        // Vérifie que la pièce a été placée correctement
        $this->assertSame($piece, $plateau->getPiece(2, 3));
    }

    public function testRetirerLigneJouable() {
        $plateau = new PlateauSquadro();

        // Retire une ligne jouable
        $plateau->retireLigneJouable(3);

        // Vérifie que la ligne a été retirée
        $this->assertNotContains(3, $plateau->getLignesJouables());
    }

    public function testRetirerColonneJouable() {
        $plateau = new PlateauSquadro();

        // Retire une colonne jouable
        $plateau->retireColonneJouable(4);

        // Vérifie que la colonne a été retirée
        $this->assertNotContains(4, $plateau->getColonnesJouables());
    }

    public function testGetCoordDestinationBlanc() {
        $plateau = new PlateauSquadro();

        // Pièce blanche, direction EST (aller)
        $plateau->setPiece(3, 0, PieceSquadro::initBlancEst());
        [$newX, $newY] = $plateau->getCoordDestination(3, 0);

        // Vérifie la destination
        $this->assertEquals(3, $newX);
        $this->assertEquals(2, $newY); // Vitesse d'aller = 2 pour x = 3
    }

    public function testGetCoordDestinationNoir() {
        $plateau = new PlateauSquadro();

        // Pièce noire, direction NORD (aller)
        $plateau->setPiece(6, 3, PieceSquadro::initNoirNord());
        [$newX, $newY] = $plateau->getCoordDestination(6, 3);

        // Vérifie la destination
        $this->assertEquals(4, $newX); // Vitesse d'aller = 2 pour y = 3
        $this->assertEquals(3, $newY);
    }
    public function testToJson() {
        $plateau = new PlateauSquadro();
        $json = $plateau->toJson();

        // Vérifie que le JSON n'est pas vide
        $this->assertNotEmpty($json);

        // Vérifie que le JSON peut être décodé en tableau
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);

        // Vérifie que le plateau décodé a la bonne taille
        $this->assertCount(7, $decoded);
        foreach ($decoded as $row) {
            $this->assertCount(7, $row);
        }
    }

    public function testFromJson() {
        $plateau = new PlateauSquadro();
        $json = $plateau->toJson();
        $newPlateau = PlateauSquadro::fromJson($json);

        // Vérifie que les plateaux sont identiques
        $this->assertEquals($plateau->getPlateau(), $newPlateau->getPlateau());
    }

    public function testToString() {
        $plateau = new PlateauSquadro();
        $output = (string) $plateau;

        $this->assertStringContainsString("PlateauSquadro", $output);
        $this->assertStringContainsString(" B ", $output); // Vérifie si des pièces blanches sont affichées
        $this->assertStringContainsString(" N ", $output); // Vérifie si des pièces noires sont affichées
    }
}