<?php

// Démarre la session PHP
session_start();

// Redirige vers l'accueil si l'utilisateur n'est pas connecté
if (!isset($_SESSION['email'])) {
    header('Location: ../../index.php');
    exit();
}

$emailConnecte = $_SESSION['email'];                                        // Récupère l'email depuis la session
$nomConnecte   = $_SESSION['nom'];                                          // Récupère le nom depuis la session

//////////////
// FONCTION //
//////////////

// Fonction qui convertit une date RSS en texte lisible ("Il y a X min", etc.)
function formatTempsEcoule($dateRss) {
    $dateArticle = strtotime($dateRss);                                 // Convertit la date RSS en timestamp Unix
    $maintenant = time();                                               // Récupère le timestamp actuel
    $secondes = $maintenant - $dateArticle;                             // Calcule la différence en secondes

    if ($secondes < 60) return "À l'instant";                           // Moins d'1 minute
    
    $minutes = round($secondes / 60);                                   // Convertit en minutes
    if ($minutes < 60) return "Il y a " . $minutes . " min";            // Moins d'1 heure
    
    $heures = round($secondes / 3600);                                  // Convertit en heures
    if ($heures < 24) return "Il y a " . $heures . " h";                // Moins d'1 jour
    
    $jours = round($secondes / 86400);                                  // Convertit en jours
    return "Il y a " . $jours . " jour" . ($jours > 1 ? "s" : "");      // Pluriel si besoin
}

$nomCSV = "../baseDonne/centreInterets.csv";                            // Chemin vers le CSV
$csvlist = array_map('str_getcsv', file("$nomCSV"));                     // Lit le CSV et parse chaque ligne en tableau
$deps_Interet = array_slice($csvlist, 1);                               // Supprime la première ligne (en-têtes)
$lesUrl = [];                                                           // Tableau des URLs RSS de l'utilisateur
$lesnom = [];                                                           // Tableau des noms de thèmes de l'utilisateur
$filteredInteret = [];                                                   // Tableau des lignes CSV filtrées pour l'utilisateur
$index = 0;                                                             // Index pour suivre le thème courant lors de l'affichage

foreach($deps_Interet as $info){                    // Parcourt toutes les lignes du CSV et ne garde que celles de l'utilisateur connecté
    if ($info[0] === $emailConnecte) {              // Compare l'email de la ligne avec celui de la session
        array_push($lesUrl, $info[2]);              // Ajoute l'URL RSS au tableau
        array_push($lesnom, $info[1]);              // Ajoute le nom du thème au tableau
        array_push($filteredInteret, $info);         // Ajoute la ligne complète au tableau filtré
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/dashboard.css">
    <title>Vos centres d'interets</title>
</head>
<body>
    <header>
        <div id="header">
            <h1>Bienvenue <?php echo htmlspecialchars($nomConnecte); // Affiche le nom de l'utilisateur connecté en sécurisant contre les failles XSS?> !</h1>
            <div id="boutonHead">
                <div class="TabBord boutonH">
                    <p>Tableau de bord</p>
                </div>
                <a href="deconnexion.php">
                    <div class="deconnexion boutonH">
                        <p>Déconnexion</p>
                    </div>
                </a>
            </div>
        </div>
    </header>
    <main>
        <section id="interet">
            <article id="vosCentreInteret">
                <div id="choixC">
                    <div id="Interets">
                        <p>Vos centres d'interets :</p>
                        <div id="cardinteret">
                            <?php  foreach($lesnom as $nomcentre){ echo "<span class='text'>" . $nomcentre . "</span>"; } // Affiche les noms des centres d'intérêt sous forme de spans ?>
                        </div> 
                    </div>
                    <div id="Changer" class="boutonH">
                        <p><a href="CentreInteret.php">Changer vos centres d'interets</a></p>
                    </div>
                </div>
            </article>
            <article id="actualité">
                <?php
                foreach ($lesUrl as $urls){                                                     // Boucle sur chaque URL RSS de l'utilisateur
                    $rss = simplexml_load_file($urls);                                           // Charge et parse le flux XML RSS distant
                    if ($rss) {                                                                 // Vérifie que le chargement a réussi
                        foreach ($rss->channel->item as $item) {                                // Boucle sur chaque article du flux RSS
                            $media = $item->children('http://search.yahoo.com/mrss/');          // Accède aux balises du namespace Yahoo Media (pour les images)
                            $image_url = "";                                                    // Initialise l'URL de l'image à vide
                            $temps = formatTempsEcoule((string)$item->pubDate);                 // Formate la date de publication
                            if (isset($media->content)) {                                       // Vérifie si une image est disponible
                                $image_url = (string)$media->content->attributes()->url;        // Récupère l'URL de l'image
                            }
                            // Génère la carte HTML de l'article avec image, titre, description, date et thème
                            echo "
                                <div class='cardactu'>
                                    <img class='imgactu' src='{$image_url}' alt=''>
                                    <div class='infos'>
                                        <h2>{$item->title}</h2>
                                        <p class='description'>{$item->description}</p>
                                        <div class='plusinfos'>
                                            <p class='heur'>{$temps}</p>
                                            <p class='heur'>{$filteredInteret[$index][1]}</p>
                                            <p><a href='{$item->link}'>Plus d'infos ></a></p>
                                        </div>
                                    </div>
                                </div>
                            ";
                        }
                        $index++;                                                               // Passe au thème suivant une fois tous ses articles affichés
                    } else {
                        echo "Impossible de charger le flux XML.";                               // Erreur si le flux RSS est inaccessible
                    }
                }
                ?>
            </article>
        </section>
    </main>
</body>
</html>