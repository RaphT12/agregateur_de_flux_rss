

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
                $ChoixCentreInteret = [];
                
                $nomCSV = "../baseDonne/centreInterets.csv";
                $handle = fopen($nomCSV, "w");

                if (isset($_POST['themes'])) {
                    foreach ($_POST['themes'] as $val){
                        array_push($ChoixCentreInteret, $val);
                        fwrite($monCSV, $val);
                    };
                };

        

                if ($ChoixCentreInteret == []){
                    echo 'Veuillez séléctionné un theme.';
                }else{
                   echo 'Vous avez sélectionné ' . implode(" et " , $ChoixCentreInteret) .'.'; 
                };

                
            ?>
        </div>
        <form action="./CentreInteret.php" method="POST">
            <div id="formulaire">
                <div class="checkboxs">
                    <input type="checkbox" id="International" name="themes[]" value="International" />
                    <label for="International">International</label>
                </div>
                <div class="checkboxs">
                    <input type="checkbox" id="Planète" name="themes[]" value="Planète" />
                    <label for="Planète">Planète</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Politique" name="themes[]" value="Politique" />
                    <label for="Politique">Politique</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Société" name="themes[]" value="Société" />
                    <label for="Société">Société</label>
                </div> 
                <div class="checkboxs">
                    <input type="checkbox" id="Culture" name="themes[]" value="Culture" />
                    <label for="sCulture">Culture</label>
                </div>  
            </div>
            
            <div>
                <button class="boutonsub" type="submit">S'abonner</button>
            </div>
        </form>
    </main>
</body>