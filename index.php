<?php
$id = 0;
// Si l'id est bien défini en GET (dans l'URL)
if (isset($_GET['id'])) {
   $id = intval($_GET['id']);
   // Autoriser n'importe quel site web à récupérer en Javascript des données de cette API :
   header("Access-Control-Allow-Origin: *");

   // Fournir un résultat en JSON :
   header('content-type:application/json');

   $foundError = 0;
   if (($page = @file_get_contents('https://www.allocine.fr/film/fichefilm_gen_cfilm=' . $id . '.html')) === false) {
      $error = error_get_last();
      $foundError = 1;
} else {
      // On récupère le code HTML de la page Allociné correspondant à l'id,
      // qu'on stocke dans la variable $page
      

      // Récupération de l'affiche du film
      // On identifie la portion de code HTML qui contient l'URL de l'affiche du film
      $pos = strpos($page, 'class="thumbnail-img" src="https://');
      $pos2 = strpos($page, '"', $pos + 28);

      // On récupère uniquement l'URL de l'affiche
      $urlAffiche = "";
      $urlAffiche = substr($page, $pos + 27, $pos2 - $pos - 27);

      // Récupération de la bande annonce du film
      // On identifie la portion de code HTML qui contient l'URL de la bande annonce du film
      $pos = strpos($page, 'href="/video/player_gen_cmedia=');
      $pos2 = strpos($page, '&', $pos + 31);

      // On récupère uniquement l'URL de la bande annonce
      $urlBandeAnnonce = "";
      $urlBandeAnnonce = "https://player.allocine.fr/" . substr($page, $pos + 31, $pos2 - $pos - 31) . ".html";

      if($pos === false){
         $urlBandeAnnonce = "";
      }

      // On crée un tableau de résultats
      $result = array("id" => $id, "urlAffiche" => $urlAffiche, "urlBandeAnnonce" => $urlBandeAnnonce);
   }
   if ($foundError > 0){
      $result = array("error" => "Identifiant du film non trouvé sur Allociné");
   }
   echo (json_encode($result));
} else {
   echo "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//FR\" \"http://www.w3.org/TR/html4/loose.dtd\">
<head>
  <title>API de récupération d'informations sur Allociné</title>
  <meta name=\"KeyWords\" content=\"Allociné,API,affiche,bande-annonce,poster,trailer\">  
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
</head>
<body>
<div id=\"container\">
<h1>API de récupération d'informations sur Allociné</h1>
<p>
Les données sont fournies au format JSON et les paramètres sont transmis en GET. Le code de cette API est disponible sous licence libre MIT <a href=\"https://github.com/PhilippeGambette/AllocineAPI\">sur GitHub</a>
</p>
<br/>
<p>
Paramètres possibles :
<ul>
<li>id (indispensable) : pour préciser l'identifiant du film sur Allociné. Exemple : <a href=\"index.php?id=271687\">id=271687</a> pour le film <em>Nomadland</em></li>
</ul>
</p>
</div>
</div>
</body>
</html>";
}
