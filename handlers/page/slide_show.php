<?php
	
/*
Handler "slide_show" pour WikiNi version WikiNi 0.4.1 et supï¿½rieurs.
Dï¿½veloppï¿½ par Charles Nï¿½pote.
Version 0.1 du 09/01/2005.
Licence GPL.

Par dï¿½faut il utilise les classes de style suivantes :
.slide { font-size: 160%; margin: 5%; background-color: #FFFFFF; padding: 30px; border: 1px inset; line-height: 1.5; }
.slide UL, LI { font-size: 100%; }
.slide LI LI { font-size: 90% }
.sl_nav p { text-decoration: none; text-align: right; font-size: 80%; line-height: 0.4; }
.sl_nav A { text-decoration: none; }
.sl_nav a:hover { color: #CF8888 }
.sum { font-size: 8px; }

Pour modifier ces styles il faut crï¿½er un fichier "slideshow.css" contenant les styles modifiï¿½s.
Le fichier "slideshow.css" sera reconnu automatiquement.

*/

// Vï¿½rification de sï¿½curitï¿½
if (!defined("WIKINI_VERSION"))
{
	die ("acc&egrave;s direct interdit");
}

// On teste si l'utilisateur peut lire la page
if (!$this->HasAccess("read"))
{
	return;
}
else
{
	// On teste si la page existe
	if (!$this->page)
	{
		return;
	}
	else
	{
		/*
		Exemple de page :
		
		(1) Prï¿½sentation xxxxxxxxxxxxxx
		
		===== (2) Titre =====
		Diapo 2.
		
		===== (3) Titre =====
		Diapo 3.
		
		===== (4) Titre =====
		Diapo 4.
		
		===== (5) Titre =====
		Diapo 5.
		
		===== (6) Titre =====
		Diapo 6.
		
		===== (7) Titre =====
		Diapo 7.
		
		Autre exemple :
		
		===== (1) Titre =====
		Diapo 1.
		
		===== (2) Titre =====
		Diapo 2.
		
		===== (3) Titre =====
		Diapo 3.
		
		===== (4) Titre =====
		Diapo 4.
		
		===== (5) Titre =====
		Diapo 5.
		
		===== (6) Titre =====
		Diapo 6.
		
		===== (7) Titre =====
		Diapo 7.
		
		*/

		//
		// dï¿½coupe la page
		$this->RegisterInclusion($this->GetPageTag());
		$body_f = $this->format($this->page["body"]);
		$this->UnregisterLastInclusion();
		$body = preg_split('/(.*<h2>.*<\/h2>)/',$body_f,-1,PREG_SPLIT_DELIM_CAPTURE);
        
		// Informations de dï¿½bogage
		if (isset($_REQUEST["debug"]) and $_REQUEST["debug"] == "1")
		{
			echo "<div style=\"display: none\">\n";
			print_r($body);
			echo "</div>\n\n";
		}

		// Si la premiï¿½re diapositive commence par un titre de niveau 1
		if (preg_match('/^<h2>.*<\/h2>/', $body_f)) $major = "0";
		else $major = "1";
		
		$user = $this->GetUser(); // echo $this->GetUser();

		// On teste toutes les paramï¿½tres du handler "slide_show" ; s'il n'y en a pas, c'est le paramï¿½tre "slide=1" qui est invoquï¿½ par dï¿½faut

		/*
		switch ($_REQUEST["method"])
		{
			case "export":
				export();
				break;
			case "show":
				showSlide();
				break;
			default:
				showSlide();
				break;
		}
		*/

		//if ($_REQUEST["export"]) { return; }
			

		if (!$body)
		{
			return;
		}
		else
		{
			// Si on ne prï¿½cise pas de paramï¿½tre, on affiche par dï¿½faut la premiï¿½re diapo
			if (!isset($_REQUEST["slide"]) or $_REQUEST["slide"] == "1") $slide = "1";
			else $slide = $_REQUEST["slide"];

			// En-tï¿½te du fichier HTML
			echo
			"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
			echo
			"<html>\n\n\n",
			"<head>\n",
			"<title>", $this->GetWakkaName(), ":", $this->GetPageTag(), "</title>\n",
			"<meta name=\"robots\" content=\"noindex, nofollow\" />\n",
			"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
			echo
			"<style type=\"text/css\" media=\"all\"> @import \"wakka.css\";</style>\n";
			// Teste s'il existe une feuille de style externe, sinon utilise des styles par dï¿½faut
			if (!file_exists("slideshow.css"))
			{
				echo "<style type=\"text/css\">\n",
				".slide { font-size: 160%; margin: 5%; background-color: #FFFFFF; padding: 30px; border: 1px inset; line-height: 1.5; }\n",
				".slide UL, LI { font-size: 100%; }\n",
				".slide LI LI { font-size: 90% }\n",
				".sl_nav p { text-decoration: none; text-align: right; font-size: 80%; line-height: 0.4; }\n",
				".sl_nav A { text-decoration: none; }\n",
				".sl_nav a:hover { color: #CF8888 }\n",
				".sum { font-size: 8px; }\n",
				"</style>\n";
			}
			else
			{
				echo "<style type=\"text/css\" media=\"all\"> @import \"slideshow.css\";</style>\n";
			}

			echo
			"</head>\n\n\n";
			
			// Affiche le corps de la page
			echo
			"<body ";
			echo (!$user || ($user["doubleclickedit"] == 'Y')) ? "ondblclick=\"document.location='".$this->href("edit")."';\" " : "", ">\n";

			// -- Affichage du sommaire [ï¿½ complï¿½ter] ----------
			/*
			if ($_REQUEST["sum"] == "on")
			{
				echo "<ul class=\"sum\">\n";
				if ($major = "1") echo "<li>", $this->format($body[0]), "</li>\n";
				foreach ($body as $title_sum)
				{
					$i = $i + 1;
					$type = gettype($i/2);
					// Ne retourne que les 50 premiers caractï¿½res du titre
					$title_sum = substr($title_sum, 0, 50);
					if ($type == "integer")
					{
						echo "<li>",$this->format($title_sum),"</li>\n";
					}
				}
				echo "</ul>\n\n";
			}
			*/

			// -- Affichage du menu de navigation --------------
			echo
			"<div class=\"sl_nav\">\n",
			"<p>";
			// Si ce n'est pas la premiï¿½re diapositive, on affiche les liens "<< prï¿½cï¿½dent"
			// et "[Dï¿½but]"
			if ($slide !== "1")
			echo
			"<a href=\"",$this->href(),"/slide_show&slide=",$_REQUEST['slide']-1,"\"><< prï¿½cï¿½dent</a>",
			" :: <a href=\"",$this->href(),"/slide_show&slide=1\">[dï¿½but]</a>\n";
			echo " :: ";
			// Si ce n'est pas la derniï¿½re diapositive, on affiche le lien "suivant >>"
			if (isset($body[($slide)*2-($major*2)+2]) or $slide == "1")
			echo "<a href=\"",$this->href(),"/slide_show&slide=",$slide+1,"\">suivant >></a>\n";
			echo
			"</p>\n";
			// Quelquesoit la diapositive, on affiche les liens "ï¿½diter" et "[]->" (pour quitter)
			echo "<p><a href=\"",$this->href(),"/edit\">ï¿½diter </a> :: <a href=\"",$this->href(),"\">[]-></a></p>\n";
			echo
			"</div>\n\n";

			echo
			"<div class=\"slide\">\n";

			// -- Affichage du contenu -------------------------

			// Si c'est la premiï¿½re diapositive
			if ($slide == "1" and $major == "1")
			{
				echo $body[0], "<br><br>";
			}

			// A partir de la deuxiï¿½me diapositive
			else
			{
				echo
				$body[($slide*2)-($major*2)-1].$body[($slide*2)-($major*2)],"\n";
				echo "\n";
			}
			echo
			"</div>\n\n";

			echo
			"</body>\n",
			"</html>";
		}
	}
}
?>