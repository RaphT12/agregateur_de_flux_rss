<?php
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$messageok = "";
$erreur = "";

// Charge le .env
$lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['mail']);

    // Vérifie que l'email existe dans le CSV
    $csvPath = __DIR__ . '/../baseDonne/Utilisateur.csv';
    $existe = false;

    if (($handle = fopen($csvPath, 'r')) !== false) {
        fgetcsv($handle); // saute l'en-tête
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[1]) && $row[1] === $email) {
                $existe = true;
                break;
            }
        }
        fclose($handle);
    }

    if (!$existe) {
        $erreur = "<p style='color:red; text-align:center;'>Aucun compte trouvé avec cet email.</p>";
    } else {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host     = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Port     = 587;
            $mail->Username = $_ENV['GMAIL_USER'];
            $mail->Password = $_ENV['GMAIL_PASSWORD'];
            $mail->CharSet  = 'UTF-8';

            $mail->setFrom($_ENV['GMAIL_USER'], 'Le Monde');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';

            $url = "http://localhost:8888/PHP/PHP-projet/asset/pages/mdp.php?email=" . urlencode($email);

            $mail->Body = "<h1>Réinitialisation du mot de passe</h1>
                           <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                           <p><a href='$url' style='background:black; color:white; padding:10px; text-decoration:none;'>Réinitialiser mon mot de passe</a></p>";

            $mail->send();
            $messageok = "<p style='color:green; text-align:center;'>Un email de réinitialisation a été envoyé.</p>";

        } catch (Exception $e) {
            $erreur = "<p style='color:red; text-align:center;'>Erreur d'envoi : {$mail->ErrorInfo}</p>";
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
            <h1>Mot de passe oublié</h1>
            <?php echo $messageok; echo $erreur; ?>
            <form action="GmailmdpOublie.php" method="post">
                <label for="mail" class="inscription">Email</label>
                <input type="email" id="mail" name="mail" class="inscription">
                <input type="submit" value="Envoyer" class="bouton formbouton">
            </form>
            <p id="pmdp"><a id="mdp" href="../../index.php">Se connecter</a></p>
        </div> 
</body>
</html>