<?php
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$messageok = "";

$lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['mail']);
    
    $dejainscrit = false;
    if (($handle = fopen(__DIR__ . '/../baseDonne/Utilisateur.csv', 'r')) !== false) {
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[1]) && $row[1] === $email) {
                $dejainscrit = true;
                break;
            }
        }
        fclose($handle);
    }

    if ($dejainscrit) {
        $messageok = "<p style='color: red; text-align: center; margin-top: 10px;'>Cette adresse email est déjà utilisée.</p>";
    } else {
        $token = bin2hex(random_bytes(16));
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
            $mail->addAddress($email, $nom); 

            $mail->isHTML(true);
            $mail->Subject = 'Finalisez votre inscription';
            
            $url = "http://localhost:8888/PHP/PHP-projet/asset/pages/mdp.php";
            
            $mail->Body = "<h1>Bienvenue $nom !</h1>
                           <p>Cliquez sur le bouton ci-dessous pour créer votre mot de passe :</p>
                           <p><a href='$url' style='background:black; color:white; padding:10px; text-decoration:none;'>Créer mon mot de passe</a></p>";

            $mail->send();

            // Vérifie une dernière fois avant d'écrire
            $dejadans = false;
            if (($check = fopen(__DIR__ . '/../baseDonne/Utilisateur.csv', 'r')) !== false) {
                fgetcsv($check);
                while (($row = fgetcsv($check)) !== false) {
                    if (isset($row[1]) && $row[1] === $email) {
                        $dejadans = true;
                        break;
                    }
                }
                fclose($check);
            }

            if (!$dejadans) {
                $f = fopen(__DIR__ . '/../baseDonne/Utilisateur.csv', 'a');
                fputcsv($f, [$nom, $email, $token]);
                fclose($f);
            }

            $messageok = "<p style='color: green; text-align: center; margin-top: 10px;'>Vous venez de recevoir un mail de confirmation.</p>";

        } catch (Exception $e) {
            echo "Erreur d'envoi : {$mail->ErrorInfo}";
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
            <h1>Inscription</h1>
            <?php echo $messageok; ?>
            <form action="inscription2.php" method="post">
                <label for="name" class="inscription">Nom d'utilisateur</label>
                <input type="text" id="name" name="name" class="inscription">
                <label for="mail" class="inscription">Email</label>
                <input type="email" id="mail" name="mail" class="inscription">
                <input type="submit" value="S'inscrire" class="bouton formbouton">
            </form>
            <p id="pmdp"><a id="mdp" href="../../index.php">Se connecter</a></p>
        </div> 
</body>
</html>