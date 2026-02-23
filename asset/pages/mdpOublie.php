







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/connexion.css">
    <title>LE MONDE</title>
</head>
<body>
    <img class="image-fond" src="../image/Logo-le-monde.png" alt="logo le monde">
        <div id="fondform">
            <h1>Connexion</h1>
            <form action="connexion" method="post">
                <label for="password" class = "inscription">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" class = "inscription">
                <label for="password" class = "inscription">Confirmer le mot de passe</label>
                <input type="password" id="password" name="password" class = "inscription">
                <input type="submit" value="Accepter" class="bouton formbouton">
            </form>
            <p id="pmdp"><a id="mdp" href="../../index.php">Se connecter</a></p>
        </div> 
    
</body>
</html>