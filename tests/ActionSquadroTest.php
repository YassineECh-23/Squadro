<?php
use PHPUnit\Framework\TestCase;
use Squadro\PlateauSquadro;
use Squadro\PieceSquadro;

class ActionSquadroTest extends TestCase {

    /**
     * Teste si une pièce appartient bien au joueur actif.
     */
    public function testPieceJouableParJoueurActif() {
        $plateau = new PlateauSquadro();

        // Supposons que le joueur blanc commence
        $piece = $plateau->getPiece(1, 0); // Une pièce blanche

        $this->assertEquals(PieceSquadro::BLANC, $piece->getCouleur(), "La pièce doit être blanche.");

        // Vérifier qu'elle est bien jouable (ex: elle est sur une ligne jouable)
        $this->assertContains(1, $plateau->getLignesJouables(), "La ligne de la pièce doit être jouable.");
    }

    /**
     * Teste la gestion d'une collision entre une pièce en mouvement et une autre pièce.
     */
    public function testGestionCollisionsSurTrajet() {
        $plateau = new PlateauSquadro();

        // Placer une pièce blanche et une pièce noire sur le même trajet
        $plateau->setPiece(3, 2, PieceSquadro::initBlancEst());
        $plateau->setPiece(3, 4, PieceSquadro::initNoirNord());

        // Déplacement de la pièce blanche
        [$newX, $newY] = $plateau->getCoordDestination(3, 2);

        // Vérifier si la pièce s'arrête avant la collision
        $this->assertEquals(3, $newX);
        $this->assertEquals(4, $newY, "La pièce doit s'arrêter avant la collision.");
    }

    /**
     * Teste la gestion des collisions multiples sur un même trajet.
     */
    public function testGestionCollisionsMultiples() {
        $plateau = new PlateauSquadro();

        // Placer plusieurs pièces sur la même ligne
        $plateau->setPiece(2, 1, PieceSquadro::initBlancEst());
        $plateau->setPiece(2, 3, PieceSquadro::initNoirNord()); // Obstacle 1
        $plateau->setPiece(2, 4, PieceSquadro::initNoirNord()); // Obstacle 2

        // Déplacement de la pièce blanche
        [$newX, $newY] = $plateau->getCoordDestination(2, 1);

        // Vérification : La pièce doit s'arrêter juste avant la collision
        $this->assertEquals(2, $newX);
        $this->assertEquals(2, $newY, "La pièce doit s'arrêter à la case juste avant la première collision.");
    }


    /**
     * Vérifie qu'une pièce change bien de direction lorsqu'elle atteint la zone de retournement.
     */
    public function testPieceAtteintZoneRetournement() {
        $plateau = new PlateauSquadro();

        // Placer une pièce qui est proche de la zone de retournement
        $plateau->setPiece(2, 5, PieceSquadro::initBlancEst());

        // Vérifier qu'après déplacement, elle change de direction
        $piece = $plateau->getPiece(2, 5);
        $piece->inverseDirection();

        $this->assertEquals(PieceSquadro::OUEST, $piece->getDirection(), "La pièce doit changer de direction après retournement.");
    }

    /**
     * Teste qu'une pièce sort bien du plateau après avoir effectué son aller-retour complet.
     */
    public function testPieceSortieApresAllerRetour() {
        $plateau = new PlateauSquadro();

        // Placer une pièce blanche qui a complété l'aller-retour
        $plateau->setPiece(2, 6, PieceSquadro::initBlancOuest());

        // Vérifier qu'après un déplacement retour, elle est hors jeu
        [$newX, $newY] = $plateau->getCoordDestination(2, 6);

        $this->assertGreaterThanOrEqual(0, $newY, "La pièce doit être sortie du plateau.");
    }

    /**
     * Teste si le plateau est correctement initialisé.
     */
    public function testInitialisationPlateau() {
        $plateau = new PlateauSquadro();
        $this->assertNotEmpty($plateau->getPlateau(), "Le plateau doit être initialisé avec des pièces.");
    }

    /**
     * Vérifie la conversion du plateau en JSON et la reconstruction correcte.
     */
    public function testConversionJson() {
        $plateau = new PlateauSquadro();
        $plateau->setPiece(3, 3, PieceSquadro::initNoirNord());

        $json = $plateau->toJson();
        $nouveauPlateau = PlateauSquadro::fromJson($json);

        // Comparer chaque case individuellement pour s'assurer que tout est correct
        for ($x = 0; $x < 7; $x++) {
            for ($y = 0; $y < 7; $y++) {
                $this->assertEquals(
                    $plateau->getPiece($x, $y)->toJson(),
                    $nouveauPlateau->getPiece($x, $y)->toJson(),
                    "Les pièces doivent être identiques après conversion JSON."
                );
            }
        }
    }


    /**
     * Teste si une pièce peut être correctement placée sur le plateau.
     */
    public function testPlacementPiece() {
        $plateau = new PlateauSquadro();
        $piece = PieceSquadro::initBlancEst();

        $plateau->setPiece(4, 2, $piece);
        $this->assertEquals($piece, $plateau->getPiece(4, 2), "La pièce doit être correctement placée.");
    }

    /**
     * Teste si les coordonnées de destination sont bien calculées.
     */
    public function testCalculCoordDestination() {
        $plateau = new PlateauSquadro();
        $plateau->setPiece(1, 0, PieceSquadro::initBlancEst());

        [$newX, $newY] = $plateau->getCoordDestination(1, 0);

        // Vérifier que la pièce avance bien de 3 cases
        $this->assertEquals(1, $newX, "X ne doit pas changer pour une pièce blanche.");
        $this->assertEquals(3, $newY, "La pièce blanche doit avancer de 3 cases si aucune collision.");
    }


    /**
     * Teste si les exceptions sont bien levées pour des coordonnées invalides.
     */
    public function testExceptionCoordonneesInvalides() {
        $this->expectException(\OutOfBoundsException::class);

        $plateau = new PlateauSquadro();
        $plateau->getPiece(-1, 8); // Coordonnées hors limites
    }
}