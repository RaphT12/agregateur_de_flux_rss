<?php
session_start();                         // Démarre (ou reprend) la session en cours pour pouvoir y accéder
session_destroy();                       // Détruit complètement la session et toutes ses variables ($_SESSION)
header('Location: ../../index.php');     // Redirige l'utilisateur vers la page de connexion
exit();                                  // Arrête l'exécution du script après la redirection
?>