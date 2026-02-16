

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
                <div class="deconnexion boutonH">
                    <p>Déconnexion</p>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div id="abonnements">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // On crée un tableau pour stocker les choix faits
                $choix_utilisateurs = [];

                // On vérifie chaque checkbox une par une
                if (isset($_POST['checkboxInternational'])) {
                    $choix_utilisateurs[] = "International";
                }
                if (isset($_POST['chekboxPlanète'])) {
                    $choix_utilisateurs[] = "Planète";
                }
                if (isset($_POST['chekboxPolitique'])) {
                    $choix_utilisateurs[] = "Politique";
                }
                if (isset($_POST['chekboxSociété'])) {
                    $choix_utilisateurs[] = "Société";
                }
                if (isset($_POST['chekboxCulture'])) {
                    $choix_utilisateurs[] = "Culture";
                }

                // Test d'affichage
                if (!empty($choix_utilisateurs)) {
                    echo "<p>Vous vous êtes abonné à : " . implode(", ", $choix_utilisateurs) . "</p>";
                } else {
                    echo "Veuillez sélectionner au moins un centre d'intérêt.";
                }
            }
            ?>
        </div>
        <form action="./CentreInteret.php" method="POST">
            <div id="formulaire">
                <div class="checkboxs">
                    <input type="checkbox" id="International" name="checkboxInternational" value="checkInternational" />
                    <label for="International">International</label>
                </div>
                <div class="checkboxs">
                    <input type="checkbox" id="Planète" name="chekboxPlanète" value="chekPlanète" />
                    <label for="Planète">Planète</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Politique" name="chekboxPolitique" value="chekPolitique" />
                    <label for="Politique">Politique</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Société" name="chekboxSociété" value="chekSociété" />
                    <label for="Société">Société</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Culture" name="chekboxCulture" value="chekCulture" />
                    <label for="sCulture">Culture</label>
                </div>  
            </div>
            
            <div>
                <button class="boutonsub" type="submit">S'abonner</button>
            </div>
        </form>
    </main>
</body>