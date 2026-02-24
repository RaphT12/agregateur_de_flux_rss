<?php
// On charge PHPMailer (Chemin : remonter de pages -> asset -> racine)
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$messageok = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des infos du formulaire
    $nom = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['mail']);
    
    // Génération du token unique pour le lien
    $token = bin2hex(random_bytes(16));

    $mail = new PHPMailer(true);

    try {
        // --- CONFIGURATION SMTP MAILTRAP ---
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Port       = 2525;
        $mail->Username   = 'c2c4f77217c6d9'; 
        $mail->Password   = '7503331a8b3f26'; // <-- Ton mot de passe complet de l'image
        $mail->CharSet    = 'UTF-8';

        // --- DESTINATAIRE ---
        $mail->setFrom('inscription@lemonde.fr', 'Le Monde');
        $mail->addAddress($email, $nom); 

        // --- CONTENU DU MAIL ---
        $mail->isHTML(true);
        $mail->Subject = 'Finalisez votre inscription';
        
        // On crée le lien vers la page de création de mot de passe
        $url = "http://localhost:8888/PHP/PHP-projet/asset/pages/mdpOublie.php";
        
        $mail->Body = "<h1>Bienvenue $nom !</h1>
                       <p>Cliquez sur le bouton ci-dessous pour créer votre mot de passe :</p>
                       <p><a href='$url' style='background:black; color:white; padding:10px; text-decoration:none;'>Créer mon mot de passe</a></p>";

        $mail->send();
        
        // --- OPTIONNEL : SAUVEGARDE DANS LE CSV ---
        // On enregistre nom, email et token (mot de passe vide pour l'instant)
        $f = fopen(__DIR__ . '/../baseDonne/Utilisateur.csv', 'a');
        fputcsv($f, [$nom, $email, $token]);
        fclose($f);

        $messageok = "<p style='color: green; text-align: center; margin-top: 10px;' >Vous venez de reçevoir un mail de confirmation.</p>";

    } catch (Exception $e) {
        echo "Erreur d'envoi : {$mail->ErrorInfo}";
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
            <?php
                echo $messageok
            ?>
            <form action="inscription2.php" method="post">
                <label for="name" class = "inscription">Nom d'utilisateur</label>
                <input type="text" id="name" name="name" class = "inscription">
                <label for="mail" class = "inscription">Email</label>
                <input type="mail" id="mail" name="mail" class = "inscription">
                <input type="submit" value="S'inscrire" class="bouton formbouton">
            </form>
            <p id="pmdp"><a id="mdp" href="../../index.php">Se connecter</a></p>
        </div> 
    
</body>
</html>