<?php

// Démarre la session PHP pour accéder aux variables de session
session_start();

if (!isset($_SESSION['email'])) {           // Si l'utilisateur n'est pas connecté (pas d'email en session), 
    header('Location: ../../index.php');    // on le redirige vers la page d'accueil
    exit();                                 // Arrête l'exécution du script après la redirection
}

$emailConnecte = $_SESSION['email'];        // Récupère l'email de l'utilisateur connecté depuis la session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/CentreInteret.css">
    <title>Vos centres d'interets</title>
</head>
<body>
    <header>
        <div id="header">
            <h1>Bienvenue</h1>
            <div id="boutonHead">
                <div class="TabBord boutonH">
                    <p><a href="dashboard.php">Tableau de bord</a></p>
                </div>
                <a href="deconnexion.php">
                    <div class="deconnexion boutonH">
                        <p>Déconnexion</p>
                    </div>
                </a>
            </div>
        </div>
    </header>
    <main>
        <div id="abonnements">
            <?php
                $TopCSV = ["id_user", "name", "url"];                       // Définit les en-têtes du fichier CSV
                $listChoix = [];                                            // Initialise un tableau vide pour stocker les thèmes choisis

                if (isset($_POST['themes'])) {                              // Vérifie si le formulaire a été soumis avec des thèmes cochés
                    $nomCSV = "../baseDonne/centreInterets.csv";            // Chemin vers le fichier CSV
                    $rows = [];                                             // Tableau pour stocker les lignes des autres utilisateurs
                    if (file_exists($nomCSV)) {                              // Vérifie si le fichier CSV existe déjà
                        $handle = fopen($nomCSV, 'r');                      // Ouvre le fichier en lecture
                        fgetcsv($handle);                                   // Saute la première ligne (les en-têtes)
                        while (($row = fgetcsv($handle)) !== false) {       // Lit le fichier ligne par ligne
                            if ($row[0] !== $emailConnecte) {               // Si la ligne n'appartient pas à l'utilisateur connecté
                                $rows[] = $row;                             // On la conserve dans le tableau
                            }
                        }
                        fclose($handle);                                    // Ferme le fichier après lecture
                    }
                    $handle = fopen($nomCSV, 'w');                          // Réouvre le fichier en écriture (efface le contenu existant)
                    if ($handle) {                                          // Vérifie que l'ouverture a réussi
                        fputcsv($handle, $TopCSV, ',', '"', '');            // Réécrit la ligne d'en-têtes

                        foreach ($rows as $row) {                           // Réécrit les lignes des autres utilisateurs
                            fputcsv($handle, $row, ',', '"', '');
                        }

                        foreach ($_POST['themes'] as $val) {                                                                            // Pour chaque thème coché par l'utilisateur
                            fputcsv($handle, [$emailConnecte, $val, "https://www.lemonde.fr/".$val."/rss_full.xml"], ',', '"', '');     // Ajoute une ligne avec l'email, le nom du thème et l'URL du flux RSS correspondant
                            $listChoix[] = $val;                                                                                        // Ajoute le thème à la liste des choix
                        }
                        fclose($handle);                                                                                                // Ferme le fichier après écriture

                        if (empty($listChoix)) {                                                        // Si aucun thème n'a été sélectionné
                            echo "Veuillez sélectionner un thème.";
                        } else {                                                                        // Sinon, affiche les thèmes sélectionnés
                            echo "Vous venez de sélectionner : " . implode(", ", $listChoix) . ".";
                        }
                    } else {
                        echo "Impossible de créer le fichier.";                                         // Erreur si le fichier ne peut pas être ouvert
                    }
                }
            ?>
        </div>
        <form action="./CentreInteret.php" method="POST">
            <div id="formulaire">
                <div class="checkboxs">
                    <input type="checkbox" id="International" name="themes[]" value="international" />
                    <label for="International">International</label>
                </div>
                <div class="checkboxs">
                    <input type="checkbox" id="Planète" name="themes[]" value="planete" />
                    <label for="Planète">Planète</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Politique" name="themes[]" value="politique" />
                    <label for="Politique">Politique</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Société" name="themes[]" value="societe" />
                    <label for="Société">Société</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Culture" name="themes[]" value="culture" />
                    <label for="Culture">Culture</label>
                </div>
                <div class="checkboxs">
                    <input type="checkbox" id="Economie" name="themes[]" value="economie" />
                    <label for="Economie">Economie</label>
                </div>
                <div class="checkboxs">
                    <input type="checkbox" id="Idées" name="themes[]" value="idees" />
                    <label for="Idées">Idées</label>
                </div>
                <div class="checkboxs">
                    <input type="checkbox" id="Sciences" name="themes[]" value="sciences" />
                    <label for="Sciences">Sciences</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Sport" name="themes[]" value="sport" />
                    <label for="Sport">Sport</label>
                </div>
                <div class="checkboxs">
                    <input type="checkbox" id="Pixels" name="themes[]" value="pixels" />
                    <label for="Pixels">Pixels</label>
                </div>        
            </div>
            <div>
                <button class="boutonsub" type="submit">S'abonner</button>
            </div>
        </form>
    </main>
</body>
</html>