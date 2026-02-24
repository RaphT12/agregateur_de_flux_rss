







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="asset/style/connexion.css">
    <title>LE MONDE</title>
</head>
<body>
    <img class="image-fond" src="asset/image/Logo-le-monde.png" alt="logo le monde">
        <div id="fondform">
            <h1>Connexion</h1>
            <form action="connexion" method="post">
                <label for="name" class = "inscription">Nom d'utilisateur</label>
                <input type="text" id="name" name="name" class = "inscription">
                <label for="password" class = "inscription">Mot de passe</label>
                <input type="password" id="password" name="password" class = "inscription">
                <input type="submit" value="Se connecter" class="bouton formbouton">
            </form>
            <a href="asset/pages/inscription2.php"><div class="binscription bouton"><p>S'inscrire</p></div></a>
            <p id="pmdp"><a id="mdp" href="">Mot de passe oublié</a></p>
        </div> 
    
</body>
</html>