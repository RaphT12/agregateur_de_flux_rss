<?php
require __DIR__ . '/../../vendor/autoload.php';         // Charge l'autoloader Composer

$erreur = "";                                                   // Initialise la variable du message d'erreur
$emailParam = isset($_GET['email']) ? $_GET['email'] : "";      // Récupère l'email depuis l'URL (?email=...), vide si absent

if ($_SERVER["REQUEST_METHOD"] == "POST") {             // Vérifie que le formulaire a été soumis en POST
    $password = $_POST['password'];                     // Récupère le mot de passe saisi
    $confirm  = $_POST['confirm'];                        // Récupère la confirmation du mot de passe
    $email    = $_POST['email'];                        // Récupère l'email depuis le champ caché du formulaire

    if (empty($password) || empty($confirm)) {              // Vérifie que les deux champs sont remplis
        $erreur = "<p style='color:red; text-align:center;'>Veuillez remplir les deux champs.</p>";
    } elseif ($password !== $confirm) {                     // Vérifie que les deux mots de passe sont identiques
        $erreur = "<p style='color:red; text-align:center;'>Les mots de passe ne correspondent pas.</p>";
    } elseif (strlen($password) < 8) {                     // Vérifie que le mot de passe fait au moins 8 caractères
        $erreur = "<p style='color:red; text-align:center;'>Le mot de passe doit faire au moins 8 caractères.</p>";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);         // Chiffre le mot de passe avec bcrypt
        $csvPath = __DIR__ . '/../baseDonne/Utilisateur.csv';       // Chemin vers le CSV des utilisateurs
        $rows = [];                                                 // Tableau qui contiendra toutes les lignes du CSV
        $header = null;                                             // Contiendra la ligne d'en-têtes
        $updated = false;                                           // Indique si une ligne a bien été mise à jour

        if (($handle = fopen($csvPath, 'r')) !== false) {           // Ouvre le CSV en lecture
            $header = fgetcsv($handle);                             // Récupère et stocke la ligne d'en-têtes
            while (($row = fgetcsv($handle)) !== false) {           // Lit toutes les lignes
                $rows[] = $row;                                     // Ajoute chaque ligne au tableau
            }
            fclose($handle);                                        // Ferme le fichier
        }

        foreach ($rows as $key => $row) {                           // Parcourt toutes les lignes du CSV
            if (empty($email)) {                                    // Cas 1 : vient de l'inscription (pas d'email en GET)
                if (isset($row[2]) && !isset($row[3])) {            // Cherche la ligne qui a un token mais pas encore de mot de passe
                    $rows[$key][3] = $hash;                         // Ajoute le mot de passe hashé en colonne 3
                    $updated = true;                                // Marque la mise à jour comme effectuée
                    break;                                          // Arrête la boucle
                }
            } else {                                                // Cas 2 : vient du mot de passe oublié (email passé en GET)
                if (isset($row[1]) && $row[1] === $email) {         // Cherche la ligne correspondant à l'email
                    $rows[$key][3] = $hash;                         // Met à jour le mot de passe hashé en colonne 3
                    $updated = true;                                // Marque la mise à jour comme effectuée
                    break;                                          // Arrête la boucle
                }
            }
        }

        if ($updated) {                             // Si une ligne a bien été modifiée
            $handle = fopen($csvPath, 'w');         // Réouvre le CSV en écriture (efface le contenu)
            fputcsv($handle, $header);              // Réécrit la ligne d'en-têtes
            foreach ($rows as $row) {               // Réécrit toutes les lignes (avec le mot de passe mis à jour)
                fputcsv($handle, $row);
            }
            fclose($handle);                                                                                                    // Ferme le fichier
            echo "<script>window.location.href = 'http://localhost:8888/PHP/PHP-projet/asset/pages/dashboard.php';</script>";   // Redirige vers le dashboard en JavaScript
            exit();                                                                                                             // Arrête l'exécution du script
        } else {
            $erreur = "<p style='color:red; text-align:center;'>Compte introuvable.</p>";                                       // Aucune ligne trouvée à mettre à jour
        }
    }
}
?>

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
            <h1>Mot de passe</h1>
            <?php echo $erreur; ?>
            <form action="mdp.php" method="post">
                <!-- Email caché transmis au POST -->
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($emailParam); ?>">
                <label for="password" class="inscription">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" class="inscription">
                <label for="confirm" class="inscription">Confirmer le mot de passe</label>
                <input type="password" id="confirm" name="confirm" class="inscription">
                <input type="submit" value="Accepter" class="bouton formbouton">
            </form>
            <p id="pmdp"><a id="mdp" href="../../index.php">Se connecter</a></p>
        </div> 
</body>
</html>