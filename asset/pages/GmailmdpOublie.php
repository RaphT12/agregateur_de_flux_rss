<?php
require __DIR__ . '/../../vendor/autoload.php'; // Charge l'autoloader de Composer (nécessaire pour PHPMailer)

// Importe les classes PHPMailer dans le script
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$messageok = "";                    // Contiendra le message de succès
$erreur = "";                       // Contiendra le message d'erreur

// Chargement du fichier .env
$lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); 

// Lit le fichier .env ligne par ligne en ignorant les lignes vides
foreach ($lines as $line) {         
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {             // Traite uniquement les lignes contenant '=' et ne commençant pas par '#' (commentaires)
        [$key, $value] = explode('=', $line, 2);                                // Sépare la clé et la valeur au premier '='
        $_ENV[trim($key)] = trim($value);                                       // Stocke la variable dans $_ENV en supprimant les espaces
    }
}

// Traitement du formulaire 
if ($_SERVER["REQUEST_METHOD"] == "POST") {                     // Vérifie que le formulaire a été soumis en POST
    $email = htmlspecialchars($_POST['mail']);                  // Récupère l'email et le sécurise contre les failles XSS
    $csvPath = __DIR__ . '/../baseDonne/Utilisateur.csv';       // Vérification de l'email dans le CSV 
    $existe = false;                                            // Par défaut, l'email n'existe pas
    if (($handle = fopen($csvPath, 'r')) !== false) {           // Ouvre le CSV en lecture
        fgetcsv($handle);                                       // Saute la ligne d'en-têtes
        while (($row = fgetcsv($handle)) !== false) {           // Lit ligne par ligne
            if (isset($row[1]) && $row[1] === $email) {         // Compare l'email (colonne 1) avec celui saisi
                $existe = true;                                 // Email trouvé
                break;                                          // Inutile de continuer la lecture
            }
        }
        fclose($handle);                                        // Ferme le fichier
    }

    if (!$existe) {
        // Email introuvable dans le CSV -> affiche une erreur
        $erreur = "<p style='color:red; text-align:center;'>Aucun compte trouvé avec cet email.</p>";
    } else {
        // Email trouvé -> on prépare l'envoi du mail avec PHPMailer
        $mail = new PHPMailer(true); // true active les exceptions en cas d'erreur

        try {
            // Configuration SMTP 
            $mail->isSMTP();                                    // Utilise le protocole SMTP
            $mail->Host     = 'smtp.gmail.com';                 // Serveur SMTP de Gmail
            $mail->SMTPAuth = true;                             // Active l'authentification SMTP
            $mail->Port     = 587;                              // Port SMTP avec chiffrement TLS
            $mail->Username = $_ENV['GMAIL_USER'];              // Email expéditeur (depuis .env)
            $mail->Password = $_ENV['GMAIL_PASSWORD'];          // Mot de passe (depuis .env)
            $mail->CharSet  = 'UTF-8';                          // Encodage pour les accents

            // Expéditeur et destinataire 
            $mail->setFrom($_ENV['GMAIL_USER'], 'Le Monde');    // Adresse et nom de l'expéditeur
            $mail->addAddress($email);                          // Destinataire = email saisi

            // Contenu du mail 
            $mail->isHTML(true);                                // Le contenu sera en HTML
            $mail->Subject = 'Réinitialisation de votre mot de passe';

            // Génère le lien de réinitialisation avec l'email encodé en paramètre GET
            $url = "http://localhost:8888/PHP/PHP-projet/asset/pages/mdp.php?email=" . urlencode($email);

            // Corps du mail avec un bouton lien vers la page de réinitialisation
            $mail->Body = "
                    <div style='font-family: Arial, sans-serif; text-align: center; max-width: 500px; margin: auto; border: 1px solid #eee; padding: 20px;'>
                        <div style='background-color: black; color: white; padding: 20px;'>
                            <h1 style='margin: 0;'>Le Monde</h1>
                        </div>
                        <h2 style='margin-top: 20px;'>Vous y êtes presque !</h2>
                        <p style='color: #444;'>Pour changer votre mot de passe, il ne vous reste plus qu'une étape : créer votre mot de passe.</p>
                        <p style='color: #444; font-size: 14px;'>Pour ce faire, veuillez cliquer sur le bouton ci-dessous. Vous pourrez le rechanger à tout moment ou le récupérer en cas de perte.</p>
                        <div style='margin-top: 30px;'>
                            <a href='$url' style='background-color: black; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Créer un mot de passe
                            </a>
                        </div>
                    </div>";

            $mail->send(); // Envoie le mail
            $messageok = "<p style='color:green; text-align:center;'>Un email de réinitialisation a été envoyé.</p>";

        } catch (Exception $e) {
            // En cas d'erreur SMTP, affiche le détail de l'erreur PHPMailer
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