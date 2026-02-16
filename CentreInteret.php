




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="asset/style/CentreInteret.css">
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
        <form>
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