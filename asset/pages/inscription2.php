<?php
require __DIR__ . '/../../vendor/autoload.php';                 // Charge l'autoloader Composer (PHPMailer)

use PHPMailer\PHPMailer\PHPMailer;                              // Importe la classe principale PHPMailer
use PHPMailer\PHPMailer\Exception;                              // Importe la classe pour gérer les erreurs PHPMailer

$messageok = "";                                                // Initialise la variable du message affiché à l'utilisateur

$lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);      // Lit le .env et retourne un tableau de lignes
foreach ($lines as $line) {                                                                // Parcourt chaque ligne du .env
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {                        // Ignore les commentaires (#) et les lignes sans '='
        [$key, $value] = explode('=', $line, 2);                                           // Sépare la clé et la valeur au niveau du '='
        $_ENV[trim($key)] = trim($value);                                                  // Stocke la variable dans $_ENV en supprimant les espaces
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {                // Vérifie que le formulaire a bien été soumis en POST
    $nom   = htmlspecialchars($_POST['name']);             // Récupère le nom et le protège contre les failles XSS
    $email = htmlspecialchars($_POST['mail']);             // Récupère l'email et le protège contre les failles XSS
    
    $dejainscrit = false;                                                               // Par défaut, on suppose que l'email n'existe pas encore
    if (($handle = fopen(__DIR__ . '/../baseDonne/Utilisateur.csv', 'r')) !== false) {  // Ouvre le CSV en lecture
        fgetcsv($handle);                                                               // Saute la ligne d'en-têtes
        while (($row = fgetcsv($handle)) !== false) {                                   // Lit le CSV ligne par ligne
            if (isset($row[1]) && $row[1] === $email) {                                 // Vérifie si l'email (colonne 1) correspond
                $dejainscrit = true;                                                    // Email trouvé, l'utilisateur est déjà inscrit
                break;                                                                  // Arrête la lecture, inutile de continuer
            }
        }
        fclose($handle);        // Ferme le fichier CSV
    }

    if ($dejainscrit) {                                                                                                         // Si l'email est déjà utilisé
        $messageok = "<p style='color: red; text-align: center; margin-top: 10px;'>Cette adresse email est déjà utilisée.</p>"; // Affiche une erreur en rouge
    } else {                                                                                                                    // Sinon, on procède à l'inscription
        $token = bin2hex(random_bytes(16));                                                                                     // Génère un token aléatoire sécurisé de 32 caractères hexadécimaux
        $mail = new PHPMailer(true);                                                                                            // Crée une instance PHPMailer avec les exceptions activées

        try {
            $mail->isSMTP();                                        // Utilise le protocole SMTP pour l'envoi
            $mail->Host     = 'smtp.gmail.com';                     // Définit le serveur SMTP de Gmail
            $mail->SMTPAuth = true;                                 // Active l'authentification SMTP
            $mail->Port     = 587;                                  // Port TLS de Gmail
            $mail->Username = $_ENV['GMAIL_USER'];                  // Email expéditeur depuis le .env
            $mail->Password = $_ENV['GMAIL_PASSWORD'];              // Mot de passe depuis le .env
            $mail->CharSet  = 'UTF-8';                              // Encodage pour supporter les accents

            $mail->setFrom($_ENV['GMAIL_USER'], 'Le Monde');        // Définit l'expéditeur avec le nom "Le Monde"
            $mail->addAddress($email, $nom);                        // Ajoute le destinataire avec son nom

            $mail->isHTML(true);                                    // Indique que le corps du mail est en HTML
            $mail->Subject = 'Finalisez votre inscription';         // Définit le sujet du mail
            
            $url = "http://localhost:8888/PHP/PHP-projet/asset/pages/mdp.php";      // URL vers la page de création de mot de passe
            
            $mail->Body = "
                    <div style='font-family: Arial, sans-serif; text-align: center; max-width: 500px; margin: auto; border: 1px solid #eee; padding: 20px;'>
                        <div style='background-color: black; color: white; padding: 20px;'>
                            <h1 style='margin: 0;'>Le Monde</h1>
                        </div>
                        
                        <h2 style='margin-top: 20px;'>Vous y êtes presque, $nom !</h2>
                        
                        <p style='color: #444;'>Pour finaliser votre inscription, il ne vous reste plus qu'une étape : créer votre mot de passe.</p>
                        
                        <p style='color: #444; font-size: 14px;'>Pour ce faire, veuillez cliquer sur le bouton ci-dessous. Vous pourrez le changer à tout moment ou le récupérer en cas de perte.</p>
                        
                        <div style='margin-top: 30px;'>
                            <a href='$url' style='background-color: black; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Créer un mot de passe
                            </a>
                        </div>
                    </div>";

            $mail->send();          // Envoie le mail

            $dejadans = false;                                                                      // Double vérification : par défaut l'email n'est pas encore dans le CSV
            if (($check = fopen(__DIR__ . '/../baseDonne/Utilisateur.csv', 'r')) !== false) {       // Réouvre le CSV en lecture
                fgetcsv($check);                                                                    // Saute la ligne d'en-têtes
                while (($row = fgetcsv($check)) !== false) {                                        // Relit le CSV ligne par ligne
                    if (isset($row[1]) && $row[1] === $email) {                                     // Vérifie une dernière fois si l'email existe
                        $dejadans = true;                                                           // Email trouvé, on n'écrira pas
                        break;                                                                      // Arrête la lecture
                    }
                }
                fclose($check);         // Ferme le fichier
            }

            if (!$dejadans) {                                                       // Si l'email n'est toujours pas dans le CSV
                $f = fopen(__DIR__ . '/../baseDonne/Utilisateur.csv', 'a');         // Ouvre le CSV en mode ajout (sans écraser)
                fputcsv($f, [$nom, $email, $token]);                                // Écrit une nouvelle ligne avec nom, email et token
                fclose($f);                                                         // Ferme le fichier
            }

            $messageok = "<p style='color: green; text-align: center; margin-top: 10px;'>Vous venez de recevoir un mail de confirmation.</p>"; // Affiche un message de succès en vert

        } catch (Exception $e) {
            echo "Erreur d'envoi : {$mail->ErrorInfo}"; // Affiche le détail de l'erreur SMTP en cas d'échec
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