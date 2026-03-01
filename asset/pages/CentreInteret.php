<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: ../../index.php');
    exit();
}

$emailConnecte = $_SESSION['email'];
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
                $TopCSV = ["id_user", "name", "url"];
                $listChoix = [];

                if (isset($_POST['themes'])) {
                    $nomCSV = "../baseDonne/centreInterets.csv";
                    $rows = [];

                    // Lit les lignes existantes et garde les autres utilisateurs
                    if (file_exists($nomCSV)) {
                        $handle = fopen($nomCSV, 'r');
                        fgetcsv($handle); // saute l'en-tête
                        while (($row = fgetcsv($handle)) !== false) {
                            if ($row[0] !== $emailConnecte) {
                                $rows[] = $row;
                            }
                        }
                        fclose($handle);
                    }

                    // Réécrit tout
                    $handle = fopen($nomCSV, 'w');
                    if ($handle) {
                        fputcsv($handle, $TopCSV, ',', '"', '');

                        // Remet les autres utilisateurs
                        foreach ($rows as $row) {
                            fputcsv($handle, $row, ',', '"', '');
                        }

                        // Ajoute les choix de l'utilisateur connecté
                        foreach ($_POST['themes'] as $val) {
                            fputcsv($handle, [$emailConnecte, $val, "https://www.lemonde.fr/".$val."/rss_full.xml"], ',', '"', '');
                            $listChoix[] = $val;
                        }
                        fclose($handle);

                        if (empty($listChoix)) {
                            echo "Veuillez sélectionner un thème.";
                        } else {
                            echo "Vous venez de sélectionner : " . implode(", ", $listChoix) . ".";
                        }
                    } else {
                        echo "Impossible de créer le fichier.";
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