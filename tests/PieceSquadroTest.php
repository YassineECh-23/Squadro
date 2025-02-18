<?php

use PHPUnit\Framework\TestCase;
use Squadro\PieceSquadro;

class PieceSquadroTest extends TestCase
{
    /** @test */
    public function testCreationPieceBlancheEst()
    {
        $piece = PieceSquadro::initBlancEst();
        $this->assertEquals(PieceSquadro::BLANC, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::EST, $piece->getDirection());
    }

    /** @test */
    public function testCreationPieceBlancheOuest()
    {
        $piece = PieceSquadro::initBlancOuest();
        $this->assertEquals(PieceSquadro::BLANC, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::OUEST, $piece->getDirection());
    }

    /** @test */
    public function testCreationPieceNoireNord()
    {
        $piece = PieceSquadro::initNoirNord();
        $this->assertEquals(PieceSquadro::NOIR, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::NORD, $piece->getDirection());
    }

    /** @test */
    public function testCreationPieceNoireSud()
    {
        $piece = PieceSquadro::initNoirSud();
        $this->assertEquals(PieceSquadro::NOIR, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::SUD, $piece->getDirection());
    }

    /** @test */
    public function testInversionDirection()
    {
        $piece = PieceSquadro::initBlancEst();
        $piece->inverseDirection();
        $this->assertEquals(PieceSquadro::OUEST, $piece->getDirection());

        $piece->inverseDirection();
        $this->assertEquals(PieceSquadro::EST, $piece->getDirection());

        $pieceNoire = PieceSquadro::initNoirNord();
        $pieceNoire->inverseDirection();
        $this->assertEquals(PieceSquadro::SUD, $pieceNoire->getDirection());

        $pieceNoire->inverseDirection();
        $this->assertEquals(PieceSquadro::NORD, $pieceNoire->getDirection());
    }

    /** @test */
    public function testCreationCaseVide()
    {
        $piece = PieceSquadro::initVide();
        $this->assertEquals(PieceSquadro::VIDE, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::VIDE, $piece->getDirection());
    }

    /** @test */
    public function testCreationCaseNeutre()
    {
        $piece = PieceSquadro::initNeutre();
        $this->assertEquals(PieceSquadro::NEUTRE, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::NEUTRE, $piece->getDirection());
    }

    /** @test */
    public function testToString()
    {
        $piece = PieceSquadro::initBlancEst();
        $this->assertStringContainsString("Blanc", (string) $piece);
        $this->assertStringContainsString("Est", (string) $piece);
    }

    /** @test */
    public function testToJson()
    {
        $piece = PieceSquadro::initBlancEst();
        $json = $piece->toJson();
        $this->assertJson($json);

        $decoded = json_decode($json, true);
        $this->assertEquals(PieceSquadro::BLANC, $decoded['couleur']);
        $this->assertEquals(PieceSquadro::EST, $decoded['direction']);
    }

    /** @test */
    public function testFromJson()
    {
        $json = '{"couleur":0,"direction":1}';
        $piece = PieceSquadro::fromJson($json);
        $this->assertEquals(PieceSquadro::BLANC, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::EST, $piece->getDirection());
    }

    /** @test */
    public function testFromJsonInvalidDataThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        PieceSquadro::fromJson('{"invalid_key":0}');
    }

}
