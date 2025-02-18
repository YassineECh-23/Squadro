<?php

namespace Squadro;

require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadro.php';
require_once '../src/PieceSquadroUI.php';

class SquadroUIGenerator
{
    /**
     * Génère un composant HTML récurrent (en-tête de page).
     */
    public static function genererEntete(string $title): string
    {
        return '<!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>' . htmlspecialchars($title) . '</title>
                    <script src="https://cdn.tailwindcss.com"></script>
                </head>
                <body class="bg-gray-100">';
    }

    /**
     * Génère un composant HTML récurrent (pied de page).
     */
    public static function genererPiedDePage(): string
    {
        return '</body></html>';
    }

    /**
     * Génère la page pour jouer une pièce du joueur actif.
     */
    public static function genererPageJouerPiece(PlateauSquadro $plateau, int $joueurActif): string
    {
        return self::genererEntete("Squadro - Jouer une pièce") . '
                <div class="text-center">
                    <h1 class="text-2xl font-bold">Jouer une pièce</h1>
                    <p class="text-lg">C\'est au tour des <span class="font-semibold">' .
            ($joueurActif === PieceSquadro::BLANC ? 'Blancs' : 'Noirs') . '</span> de jouer.</p>
                </div>
                ' . PieceSquadroUI::generatePlateau($plateau, $joueurActif) . '
                ' . self::genererPiedDePage();
    }

    /**
     * Génère la page pour confirmer le déplacement de la pièce choisie.
     */
    public static function genererPageConfirmerDeplacement(int $ligne, int $colonne, PlateauSquadro $plateau, int $joueurActif): string {
        return self::genererEntete("Confirmer Déplacement") . '
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="animate-fade-in-up bg-white p-8 rounded-lg shadow-lg w-full max-w-md transform transition-all">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Confirmer le déplacement</h1>
            <p class="text-lg text-gray-600 mb-6 text-center">
                Voulez-vous déplacer la pièce en position 
                <span class="font-semibold text-blue-600">(' . $ligne . ', ' . $colonne . ')</span> ?
            </p>

            <form method="POST" action="traiteActionSquadro.php" class="flex flex-col space-y-4">
                <input type="hidden" name="ligne" value="' . $ligne . '">
                <input type="hidden" name="colonne" value="' . $colonne . '">
                <button type="submit" name="confirmer" class="w-full px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors duration-200">
                    Continuer
                </button>
                <button type="submit" name="annuler" class="w-full px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors duration-200">
                    Annuler
                </button>
            </form>
        </div>
    </div>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
    
    ' . self::genererPiedDePage();
    }


    /**
     * Génère la page affichant le plateau final et le message de victoire.
     */
    public static function genererPageVictoire(PlateauSquadro $plateau, int $joueurGagnant): string
    {
        $gagnantTexte = ($joueurGagnant === PieceSquadro::BLANC) ? 'Blancs' : 'Noirs';

        return self::genererEntete("Squadro - Victoire") . '
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                <h1 class="text-5xl font-bold text-indigo-600 mb-4">🎉 Victoire ! 🎉</h1>
                <p class="text-3xl text-gray-800">Les <span class="font-semibold text-green-500">' . $gagnantTexte . '</span> ont gagné la partie !</p>
                <div class="mt-6">
                    <a href="index.php" class="px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600">
                        🔄 Rejouer
                    </a>
                </div>
            </div>
        </div>
    ' . self::genererPiedDePage();
    }

    /**
     * Génère la page d'erreur.
     */
    public static function genererPageErreur(string $message): string{
        return self::genererEntete("Squadro - Erreur") . '
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-red-600">Erreur !</h1>
                    <p class="text-lg">' . htmlspecialchars($message) . '</p>   
                </div>
                <a href="index.php" class="mt-4 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600">Revenir à l\'accueil</a>
                ' . self::genererPiedDePage();

    }
    /**
     * Génère la carte d'affichage des pièces noires sorties.
     */
    public static function genererCardBlanche(int $compteurBlanc): string
    {
        return '<div class="flex items-center bg-white border rounded-sm overflow-hidden shadow">
                <div class="p-4 bg-green-400">
                     <img src="../icons/white_ball.png" alt="Icône noir" class="h-12 w-12">
                </div>
                <div class="px-4 text-gray-700">
                    <h3 class="text-sm tracking-wider">Pièces blanches sorties :</h3>
                    <p class="text-3xl">' . $compteurBlanc . '</p>
                </div>
            </div>';
    }

    /**
     * Génère la carte d'affichage des pièces noires sorties.
     */
    public static function genererCardNoir(int $compteurNoir): string
    {
        return '<div class="flex items-center bg-white border rounded-sm overflow-hidden shadow">
                <div class="p-4 bg-green-400">
                      <img src="../icons/black_ball.png" alt="Icône noir" class="h-12 w-12">
                </div>
                <div class="px-4 text-gray-700">
                    <h3 class="text-sm tracking-wider">Pièces noires sorties :</h3>
                    <p class="text-3xl">' . $compteurNoir . '</p>
                </div>
            </div>';
    }
    /**
     * Génère la page principale du jeu.
     */
    public static function genererPageJeu(PlateauSquadro $plateau, int $joueurActif, int $compteurBlanc, int $compteurNoir): string
    {
        return self::genererEntete("Squadro - Jeu") . '
        <div class="flex flex-row justify-center items-center">
            <!-- Colonne des compteurs -->
            <div class="w-[30%] flex flex-col gap-10">
                ' . self::genererCardNoir($compteurNoir) . '
                ' . self::genererCardBlanche($compteurBlanc) . '
            </div>

            <!-- Zone principale du jeu -->
            <div class="w-[70%] flex flex-col items-center">
                <h1 class="text-2xl font-bold mb-4">Squadro</h1>

                <!-- Bouton de réinitialisation -->
                <form method="POST" action="reset.php" class="mb-4">
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white font-bold rounded-lg"> Réinitialiser le jeu</button>
                </form>

                <!-- Indication du tour -->
                <p class="text-lg">
                    C\'est au tour des
                    <span class="font-semibold">' . ($joueurActif === PieceSquadro::BLANC ? "Blancs" : "Noirs") . '</span> de jouer.
                </p>

                <!-- Affichage du plateau -->
                <div class="mt-4">
                    ' . PieceSquadroUI::generatePlateau($plateau, $joueurActif) . '
                </div>
            </div>
        </div>
        ' . self::genererPiedDePage();
    }


}
