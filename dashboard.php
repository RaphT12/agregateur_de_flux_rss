



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="asset/style/dashboard.css">
    <title>Vos centres d'interets</title>
</head>
<body>
    <header>
        <div id="header">
            <h1>Bienvenu Raphaël</h1>
            <div id="boutonHead">
                <div class="TabBord">
                    <p>Tableau de bord</p>
                </div>
                <div id="deconnexion">
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
                        <p>Vos centres d'interets</p>
                    </div>
                    <div id="Changer">
                        <p>Changer vos centres d'interets</p>
                    </div>
                </div>
            </article>
            <article id="actualité">
                <?php

                $url = "https://www.lemonde.fr/culture/rss_full.xml";
                $rss = simplexml_load_file($url);

                if ($rss) {
                    foreach ($rss->channel->item as $item) {
                        $media = $item->children('http://search.yahoo.com/mrss/');
                        $image_url = "";
                        if (isset($media->content)) {
                            $image_url = (string)$media->content->attributes()->url;
                        }
                        echo"
                            <div class='cardactu'>
                                <img class='imgactu' src='{$image_url}' alt=''>
                                <div class='infos'>
                                    <h2>{$item->title}</h2>
                                    <p>{$item->description}</p>
                                    <div class='plusinfos'>
                                        <p class='heur'>{$item->pubDate}</p>
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