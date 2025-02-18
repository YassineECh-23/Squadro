<?php

namespace Squadro;

use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;

class PieceSquadroUI
{
    public static function generationCaseVide(): string
    {
        return '<button type="button" class="h-12 w-12 border border-gray-400 bg-gray-300 rounded-lg" disabled></button>';
    }

    public static function generationCaseNeutre(): string
    {
        return '<button type="button" class="h-12 w-12 border border-gray-600 bg-gray-500 rounded-lg" disabled></button>';
    }

    public static function generationCaseRouge(int $valeur): string
    {
        return '<div class="h-12 w-12 flex items-center justify-center border border-red-600 bg-red-500 text-white font-bold rounded-lg">' . $valeur . '</div>';
    }

    public static function generationPiece(PieceSquadro $piece, int $ligne, int $colonne, bool $estActif, PlateauSquadro $plateau): string
    {
        $couleur = ($piece->getCouleur() === PieceSquadro::BLANC) ? 'bg-white border-black' : 'bg-black border-white';

        // Obtenir les coordonnées de destination
        [$newX, $newY] = $plateau->getCoordDestination($ligne, $colonne);
        $caseDestination = $plateau->getPiece($newX, $newY);

        // 🔹 **Cas 1 : La pièce appartient à l'adversaire**
        if (!$estActif) {
            return self::genererBoutonBloque($couleur, "Cette pièce appartient à l'adversaire.");
        }

        // 🔹 **Cas 2 : La case d'arrivée est occupée, donc la pièce ne peut pas bouger**
        if ($caseDestination->getCouleur() !== PieceSquadro::VIDE) {
            return self::genererBoutonBloque($couleur, "Case d'arrivée occupée, déplacement impossible.");
        }

        // 🔹 **Si la pièce est jouable, permettre le déplacement**
        return '
        <form action="traiteActionSquadro.php" method="POST">
            <input type="hidden" name="ligne" value="' . $ligne . '">
            <input type="hidden" name="colonne" value="' . $colonne . '">
            <button class="h-12 w-12 border rounded-full shadow-md ' . $couleur . '" type="submit">
            </button>
        </form>';
    }

    /**
     * Génère un bouton désactivé avec un message d'information.
     */
    private static function genererBoutonBloque(string $couleur, string $message): string
    {
        return '<button class="h-12 w-12 border rounded-full shadow-md ' . $couleur . ' cursor-not-allowed" type="button" disabled>
        </button>';
    }


    public static function generatePlateau(PlateauSquadro $plateau, int $joueurActif): string
    {
        $vitessesBlanchesRetour = [1, 3, 2, 3, 1];
        $vitessesBlanchesAller = [3, 1, 2, 1, 3];
        $vitessesNoiresAller = [3, 1, 2, 1, 3];
        $vitessesNoiresRetour = [1, 3, 2, 3, 1];

        $html = '<table class="border-collapse border border-gray-800 mx-auto bg-gray-200">';

        // 🔹 **Ligne du haut avec cases rouges (Vitesses de retour des noirs)**
        $html .= '<tr>';
        $html .= '<td class="border border-gray-800 bg-gray-200"></td>'; // Coin neutre
        $html .= '<td class="border border-gray-800 bg-gray-200"></td>'; // Décalage visuel
        foreach ($vitessesNoiresRetour as $valeur) {
            $html .= '<td class="border border-gray-800 text-center p-3">' . self::generationCaseRouge($valeur) . '</td>';
        }
        $html .= '</tr>';

        // 🔹 **Lignes du plateau avec les pièces et vitesses des blancs**
        for ($ligne = 0; $ligne < 7; $ligne++) {
            $html .= '<tr>';

            // 🔹 **Ajout des cases rouges à gauche (Vitesses de retour des blancs)**
            if ($ligne === 0) {
                $html .= '<td class="border border-gray-800 bg-gray-200"></td>'; // Décalage visuel
            } elseif ($ligne >= 1 && $ligne <= 5) {
                $html .= '<td class="border border-gray-800 text-center p-2">' . self::generationCaseRouge($vitessesBlanchesRetour[$ligne - 1]) . '</td>';
            } else {
                $html .= '<td class="border border-gray-800 bg-gray-200"></td>'; // Coin neutre
            }

            for ($colonne = 0; $colonne < 7; $colonne++) {
                $piece = $plateau->getPiece($ligne, $colonne);
                $html .= '<td class="border border-gray-800 text-center p-2">';

                if ($piece->getCouleur() === PieceSquadro::VIDE) {
                    $html .= self::generationCaseVide();
                } elseif ($piece->getCouleur() === PieceSquadro::NEUTRE) {
                    $html .= self::generationCaseNeutre();
                } else {
                    $isActive = ($piece->getCouleur() === $joueurActif);
                    $html .= self::generationPiece($piece, $ligne, $colonne, $isActive, $plateau);
                }

                $html .= '</td>';
            }

            // 🔹 **Ajout des cases rouges à droite (Vitesses d'aller des blancs)**
            if ($ligne === 0) {
                $html .= '<td class="border border-gray-800 bg-gray-200"></td>'; // Décalage visuel
            } elseif ($ligne >= 1 && $ligne <= 5) {
                $html .= '<td class="border border-gray-800 text-center p-2">' . self::generationCaseRouge($vitessesBlanchesAller[$ligne - 1]) . '</td>';
            } else {
                $html .= '<td class="border border-gray-800 bg-gray-200"></td>'; // Coin neutre
            }

            $html .= '</tr>';
        }

        // 🔹 **Ligne du bas avec cases rouges (Vitesses d'aller des noirs)**
        $html .= '<tr>';
        $html .= '<td class="border border-gray-800 bg-gray-200"></td>'; // Coin neutre
        $html .= '<td class="border border-gray-800 bg-gray-200"></td>'; // Décalage visuel
        foreach ($vitessesNoiresAller as $valeur) {
            $html .= '<td class="border border-gray-800 text-center p-3">' . self::generationCaseRouge($valeur) . '</td>';
        }
        $html .= '</tr>';

        $html .= '</table>';
        return $html;
    }
}