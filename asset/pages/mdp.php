<?php
require __DIR__ . '/../../vendor/autoload.php';

$erreur = "";
$emailParam = isset($_GET['email']) ? $_GET['email'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];
    $email    = $_POST['email']; // récupéré depuis le formulaire caché

    if (empty($password) || empty($confirm)) {
        $erreur = "<p style='color:red; text-align:center;'>Veuillez remplir les deux champs.</p>";
    } elseif ($password !== $confirm) {
        $erreur = "<p style='color:red; text-align:center;'>Les mots de passe ne correspondent pas.</p>";
    } elseif (strlen($password) < 8) {
        $erreur = "<p style='color:red; text-align:center;'>Le mot de passe doit faire au moins 8 caractères.</p>";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $csvPath = __DIR__ . '/../baseDonne/Utilisateur.csv';
        $rows = [];
        $header = null;
        $updated = false;

        if (($handle = fopen($csvPath, 'r')) !== false) {
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        foreach ($rows as $key => $row) {
            if (empty($email)) {
                // Vient de l'inscription : cherche la ligne sans mot de passe
                if (isset($row[2]) && !isset($row[3])) {
                    $rows[$key][3] = $hash; // ← modifie via la clé, pas la référence
                    $updated = true;
                    break;
                }
            } else {
                // Vient du mot de passe oublié : cherche par email
                if (isset($row[1]) && $row[1] === $email) {
                    $rows[$key][3] = $hash; // ← modifie via la clé, pas la référence
                    $updated = true;
                    break;
                }
            }
        }

        if ($updated) {
            $handle = fopen($csvPath, 'w');
            fputcsv($handle, $header);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
            echo "<script>window.location.href = 'http://localhost:8888/PHP/PHP-projet/asset/pages/dashboard.php';</script>";
            exit();
        } else {
            $erreur = "<p style='color:red; text-align:center;'>Compte introuvable.</p>";
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