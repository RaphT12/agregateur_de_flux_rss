<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/inscription.css">
</head>
<body>
    <div id = "container">
        <img src="../image/cabinet_notaire_allobroges_salle_dattente (1).webp" alt="image de la salle dattente du cabinet notaire">
        <div id = "inscription">
            <h1>Inscription</h1>
            <form action="connexion.php" method="post">
                <label for="name" class = "inscription">Nom d'utilisateur:</label>
                <input type="text" id="name" name="name" class = "inscription">
                <label for="password" class = "inscription">Mot de passe:</label>
                <input type="password" id="password" name="password" class = "inscription">
                <label for="email" class = "inscription">Email:</label>
                <input type="email" id="email" name="email" class = "inscription">
                <div id = "buttons">
                    <input type="submit" value="S'inscrire" class="bouton">
                    <input type="submit" value="Récupération" class="bouton">
                </div>
            </form>
        </div>
    </div>
</body>
</html>