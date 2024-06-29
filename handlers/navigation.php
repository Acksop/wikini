 <?php

/*
    navigation.php -  2004 jmg, contrib 2024 acksop

    ne pas sÃ©parer de navigation_cookie.php
    
    mettre le fichier << navigation.php >> dans le rÃ©pertoire << /handlers/ >>

    mettre << include ("navigation.php"); >> afin d'ajouter le fil de navigation d'ariane
    Ã  l'endroit ou vous le voulez, c'est a dire dans le fichier
    << /action/header.php >> avant [ <div class="page"> ].

*/


    $nom = "historique" ;
	if(isset($_COOKIE[$nom])) {
        $histo_tab = explode("|", $_COOKIE[$nom]);
		$histo_tab_unique = array_unique($histo_tab);
    
        echo "<<<";

		$nb_pages_visit = count($histo_tab_unique);
		
		//pour ne laisser que les 7 derniÃ¨res pages visitÃ©es
		$debut_histo = $nb_pages_visit - 7;
		
		($debut_histo < 0)?$debut_histo=0:$debut_histo=$debut_histo;
		$i = 0;
		$histo='';
		
		foreach ($histo_tab_unique as $element) {
			if($i < $debut_histo){
				$i = $i + 1;
				continue;
			}
			$nom_page_wiki = urldecode($element);
			$lien_html_wiki =  $this->config["base_url"].$nom_page_wiki;
			
			if($i == $debut_histo){
				$histo = "<a href='$lien_html_wiki'>".$nom_page_wiki."</a>";	
			}else if($i > $debut_histo){
				$histo = " <a href='$lien_html_wiki'>".$nom_page_wiki."</a> < ". $histo;
			}
		 
			$i = $i + 1;
		}
		
		echo $histo;
	}else{
		echo "<strong>Pas d'historique</strong>";
	}
	
	
	