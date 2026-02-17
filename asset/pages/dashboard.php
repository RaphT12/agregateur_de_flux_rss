<?php
function formatTempsEcoule($dateRss) {
    // Convertit la date du flux en secondes
    $dateArticle = strtotime($dateRss);
    $maintenant = time();
    $secondes = $maintenant - $dateArticle;

    // Calcul des différentes unités
    if ($secondes < 60) {
        return "À l'instant";
    }
    
    $minutes = round($secondes / 60);
    if ($minutes < 60) {
        return "Il y a " . $minutes . " min";
    }
    
    $heures = round($secondes / 3600);
    if ($heures < 24) {
        return "Il y a " . $heures . " h";
    }
    
    $jours = round($secondes / 86400);
    return "Il y a " . $jours . " jour" . ($jours > 1 ? "s" : "");
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
            <h1>Bienvenue</h1>
            <div id="boutonHead">
                <div class="TabBord boutonH">
                    <p>Tableau de bord</p>
                </div>
                <div class="deconnexion boutonH">
                    <p>Déconnexion</p>
                </div>
            </div>
        </div>
    </header>
    <main>
        <section id="interet">
            <article id="vosCentreInteret">
                <div id="choixC">
                    <div id="Interets">
                        <p>Vos centres d'interets :</p>
                    </div>
                    <div id="Changer" class="boutonH">
                        <p><a href="CentreInteret.php">Changer vos centres d'interets</a></p>
                    </div>
                </div>
            </article>
            <article id="actualité">
                <?php

                $url = "https://www.lemonde.fr/pixels/rss_full.xml";
                $rss = simplexml_load_file($url);

                if ($rss) {
                    foreach ($rss->channel->item as $item) {
                        $media = $item->children('http://search.yahoo.com/mrss/');
                        $image_url = "";
                        $temps = formatTempsEcoule((string)$item->pubDate);
                        if (isset($media->content)) {
                            $image_url = (string)$media->content->attributes()->url;
                        }
                        echo"
                            <div class='cardactu'>
                                <img class='imgactu' src='{$image_url}' alt=''>
                                <div class='infos'>
                                    <h2>{$item->title}</h2>
                                    <p class='description'>{$item->description}</p>
                                    <div class='plusinfos'>
                                        <p class='heur'>{$temps}</p>
                                        <p><a href='{$item->link}'>Plus d'infos ></a></p>
                                    </div>
                                </div>
                            </div>
                        ";
                    };
                } else {
                    echo "Impossible de charger le flux XML.";
                }
                ?>
            </article>
        </section>
    </main>
</body>
</html>