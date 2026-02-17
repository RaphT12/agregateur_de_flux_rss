

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
                $TopCSV = ["id_user", "name", "url"];
                $donne = [];
                $ChoixCentreInteret = [];
                $index = 0;
                $listChoix = [];
                
                $nomCSV = "../baseDonne/centreInterets.csv";
                $handle = fopen($nomCSV, "w");
                fputcsv($handle, $TopCSV, ',', '"', '');

                if (isset($_POST['themes'])) {
                    foreach ($_POST['themes'] as $val){
                        array_push($ChoixCentreInteret, ["1", "$val", "https://www.lemonde.fr/".$val."/rss_full.xml"]);
                    };
                };

                foreach($ChoixCentreInteret as $choix){
                    array_push($listChoix, $ChoixCentreInteret[$index][1]);
                    $index++;
                }

                if ($ChoixCentreInteret == []){
                    echo 'Veuillez séléctionné un theme.';
                }else{
                    echo 'Vous avez sélectionné ' . implode(" et " , $listChoix) .'.';
                    foreach ($ChoixCentreInteret as $ch){
                        fputcsv($handle, $ch, ',', '"', '');
                    };
                };


                fclose($handle);
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
                    <label for="sCulture">Culture</label>
                </div>  
            </div>
            
            <div>
                <button class="boutonsub" type="submit">S'abonner</button>
            </div>
        </form>
    </main>
</body>