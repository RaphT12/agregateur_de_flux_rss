<?php

// Démarre la session pour stocker les infos des utilisateurs sur tout le site 
session_start();

// Initialise la variable d'erreur pour éviter les "Undefined variable"
$erreur = "";

////////////////
// FORMULAIRE //
////////////////

if ($_SERVER["REQUEST_METHOD"] == "POST") {                                     // Vérifie si le formulaire a etait soumis via la methode post
    $email = htmlspecialchars($_POST['mail']);                                  // protection de l'email contre les potentielle scripts
    $password = $_POST['password'];                                             // Récupération du mot de passe brut
    $csvPath = __DIR__ . '/asset/baseDonne/Utilisateur.csv';                    // initialisation du chemin vers le csv
    $connecte = false;                                                          // initialisation de la variable connecte a FALSE
    if (($handle = fopen($csvPath, 'r')) !== false) {                           // ouverture du csv en lecture seul ('r')
        fgetcsv($handle);                                                       // Saute la première ligne (les entêtes : nom, email, etc.)
        while (($row = fgetcsv($handle)) !== false) {                           // Parcourt le fichier ligne par ligne
            if (isset($row[1]) && $row[1] === $email) {                         // Vérifie si l'email (colonne 1) correspond à celui saisi
                if (isset($row[3]) && password_verify($password, $row[3])) {    // Vérifie si le mot de passe saisi correspond au hash (colonne 3)
                    $connecte = true;                                           // On affecte true a $connecte
                    break;                                                      // On arrete la boucle des qu'on a trouver l'utilisateur
                }
            }
        }
        fclose($handle);                                                        // Ferme le flux du fichier pour libérer de la mémoire
    }

    if ($connecte) {                                                                                                        // Si l'utilisateur est trouvé et authentifié
        $_SESSION['email'] = $row[1];                                                                                       // Stocke l'email' en session pour les réutiliser ailleurs
        $_SESSION['nom']   = $row[0];                                                                                       // Stocke le nom en session pour les réutiliser ailleurs
        echo "<script>window.location.href = 'http://localhost:8888/PHP/PHP-projet/asset/pages/dashboard.php';</script>";   // Redirection via JavaScript vers le tableau de bord
        exit();                                                                                                             // Interrompt le script après la redirection
    } else {                                                                                                                // Sinon
        $erreur = "<p style='color:red; text-align:center;'>Email ou mot de passe incorrect.</p>";                          // Prépare le message d'erreur en cas d'échec
    }
}
?>

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
            <form action="index.php" method="post">
                <?php echo $erreur; ?>  <!--  On sort le messge d'erreure en cas d'erreure  -->
                <label for="mail" class="inscription">Adresse Gmail</label>
                <input type="email" id="mail" name="mail" class="inscription">
                <label for="password" class = "inscription">Mot de passe</label>
                <input type="password" id="password" name="password" class = "inscription">
                <input type="submit" value="Se connecter" class="bouton formbouton">
            </form>
            <a href="asset/pages/inscription2.php"><div class="binscription bouton"><p>S'inscrire</p></div></a>
            <p id="pmdp"><a id="mdp" href="asset/pages/GmailmdpOublie.php">Mot de passe oublié</a></p>
        </div> 
    
</body>
</html>