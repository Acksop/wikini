<?php
/**
 * Action permettant d'effacer facilement les spams de commentaires
 * (pour WikiNi 0.5 et supï¿½rieurs)
 * 
 * Cette action accepte les paramï¿½tres :
 * -- "max" permettant de limiter le nombre de commentaires affichï¿½s
 * -- "logpage" permettant de spï¿½cifier la page oï¿½ sont enregistrï¿½es
 *    les suppressions effectuï¿½es
 * Exemple d'utilisation : {{erasespamedcomments max="50"}}
 * 
 * @version $Id: erasespamedcomments.class.php 859 2007-11-22 01:07:26Z nepote $
 * @author Charles Nï¿½pote <charles@nepote.org>
 * @author Didierï¿½Loiseau <l.farquaad@gmail.com>
 * @copyright Copyright &copy; 2006, 2007 Charles Nï¿½pote
 * @license License GPL.
 * 
 * @todo
 * -- pour garantir une certaine transparence, option d'envoi par mail des contenus effacï¿½s (?)
 *    (via une mï¿½thode appelï¿½e NotifyAdmin())
 * -- idï¿½alement la derniï¿½re page affiche les rï¿½sultats mais ne renettoie
 *    pas les commentaires si elle est rechargï¿½e
 * -- test pour savoir si quelque chose a bien ï¿½tï¿½ effacï¿½
 * -- la prï¿½sentation (style, paramï¿½trage de limite du nombre de commentaires affichï¿½s,
 *    paramï¿½trage de la longueur des contenus affichï¿½s, etc.)
 * 
 * 
*/


// Vï¿½rification de sï¿½curitï¿½
if (!defined('WIKINI_VERSION'))
{
	die ('acc&egrave;s direct interdit');
}

class ActionErasespamedcomments extends WikiniAdminAction
{
	function PerformAction($args)
	{
		$wiki = &$this->wiki;
		ob_start();
		echo	"\n<!-- == Action erasespamedcomments v 0.7 ============================= -->\n";

		// -- 2. Affichage du formulaire ---
		if(!isset($_POST['clean']))
		{
			$limit = isset($args['max']) && $args["max"] > 0 ? (int) $args["max"] : 0;
			if ($comments = $wiki->LoadRecentComments($limit))
			{
				// Formulaire listant les commentaires
				echo "<form method=\"post\" action=\"". $wiki->Href() . "\" name=\"selection\">\n";
				$curday = '';
				foreach ($comments as $comment)
				{
					// day header
					list($day, $time) = explode(" ", $comment["time"]);
					if ($day != $curday)
					{
						if ($curday)
						{
							echo "</ul>\n" ;
						}
						$erase_id = 'erasecommday_' . str_replace('-', '', $day);
						echo "<b>$day:</b> <a href=\"#\" onclick=\"return invert_selection('" . $erase_id . "')\">inverser</a> <br>\n" ;
						echo "<ul id=\"" . $erase_id . "\">\n";
						$curday = $day;
					}

					// echo entry
					echo
						"<li><input name=\"suppr[]\" value=\"" . $comment["tag"] . "\" type=\"checkbox\" /> [Suppr.!] ".
						$comment["tag"].
						" (",$comment["time"],") <code>".
						htmlspecialchars(substr($comment['body'], 0, 25))."</code> ".
						"<a href=\"",$wiki->href("", $comment["comment_on"], "show_comments=1")."#".$comment["tag"]."\">".
						$comment["comment_on"],"</a> . . . . ".
						$wiki->Format($comment["user"]),"</li>\n" ;
				}
				echo "</ul>\n<input type=\"hidden\" name=\"clean\" value=\"yes\" />\n";
				echo "<button value=\"Valider\">Nettoyer >></button>\n";
				echo "</form>";
			}
			else
			{
				echo "<i>Pas de commentaires r&eacute;cents.</em>" ;
			}
		}


		// -- 3. Traitement du formulaire ---
		else if(isset($_POST['clean']))
		{
			$deletedPages = "";


			// -- 3.1 Si des pages ont ï¿½tï¿½ sï¿½lectionnï¿½es : effacement ---
			// On efface chaque ï¿½lï¿½ment du tableau suppr[]
			// Pour chaque page sï¿½lectionnï¿½e
			if (!empty($_POST['suppr']))
			{
				foreach ($_POST['suppr'] as $page)
				{
					// Effacement de la page en utilisant la mï¿½thode adï¿½quate
					// (si DeleteOrphanedPage ne convient pas, soit on crï¿½ï¿½
					// une autre, soit on la modifie
					echo "Effacement de : " . $page . "<br>\n";
					$wiki->DeleteOrphanedPage($page);
					$deletedPages .= $page . ", ";
				}
				$deletedPages = trim($deletedPages, ", ");
				echo "<p><a href=\"".$wiki->Href()."\">Retour au formulaire.</a></p>";
			}

			// -- 3.2 Si aucune page n'a ï¿½tï¿½ sï¿½lectionnï¿½ : message
			else
			{
				echo "<p>Aucun commentaire n'a ï¿½tï¿½ sï¿½lectionnï¿½ pour ï¿½tre effacï¿½.</p>";
				echo "<p><a href=\"".$wiki->Href()."\">Retour au formulaire.</a></p>";
			}

			// -- 3.3 ï¿½criture du journal des actions ---
			//        S'il y a eu des pages nettoyï¿½es,
			//        on enregistre dans une page choisie qui a fait quoi
			if ($deletedPages)
			{
				// -- Dï¿½termine quelle est la page de log :
				//    -- passï¿½e en paramï¿½tre
				//    -- ou la page de log par dï¿½faut
				$reportingPage = isset($args["logpage"]) ? $args["logpage"] : "";

				// -- Ajout de la ligne de log
				$wiki->LogAdministrativeAction($wiki->GetUserName(),
					"Commentaire(s) effacï¿½(s)" .
					/*" [" .*/ /*$_POST['comment'] .*/ /* "]".*/
					"&nbsp;: " .
					"\"\"".
					$deletedPages .
					"\"\"".
					"\n", $reportingPage);
			}
		}
		return ob_get_clean();
	}
}

?>
