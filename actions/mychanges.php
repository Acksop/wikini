<?php
/*
$Id: mychanges.php 294 2005-01-09 20:05:09Z nepote $
Copyright (c) 2003, Carlo Zottmann
Copyright  2003 David DELON
Copyright 2003 Charles NEPOTE
Copyright 2004 Jean Christophe ANDRÃ
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
if ($user = $this->GetUser())
{
	$my_edits_count = 0;
	$curChar = '';
	$curday = '';
	$last_tag = '';

	if (($bydate = $this->GetParameter("bydate")))
	{
		echo "<b>Liste des pages que vous avez modifi&eacute;es, tri&eacute;e par date de modification.</b><br><br>\n";	

		if ($pages = $this->LoadAll("SELECT tag, time FROM ".$this->config["table_prefix"]."pages WHERE user = '".mysql_escape_string($this->GetUserName())."' AND tag NOT LIKE 'Comment%' ORDER BY time ASC, tag ASC"))
		{
			foreach ($pages as $page)
			{
				$edited_pages[$page["tag"]] = $page["time"];
			}

			 arsort($edited_pages);

			foreach ($edited_pages as $page["tag"] => $page["time"])
			{
				// day header
				list($day, $time) = explode(" ", $page["time"]);
				if ($day != $curday)
				{
					if ($curday) echo "<br>\n";
					echo "<b>$day:</b><br>\n";
					$curday = $day;
				}

				// echo entry
				echo "&nbsp;&nbsp;&nbsp;($time) (",$this->ComposeLinkToPage($page["tag"], "revisions", "history", 0),") ",$this->ComposeLinkToPage($page["tag"], "", "", 0),"<br>\n";

				$my_edits_count++;
			}
			
			if ($my_edits_count == 0)
			{
				echo "<i>Vous n'avez pas modifi&eacute; de page.</em>";
			}
		}
		else
		{
			echo "<i>Aucune page trouv&eacute;e.</em>";
		}
	}
	else
	{
		echo "<b>Liste des pages que vous avez modifi&eacute;es, tri&eacute;e par ordre alphab&eacute;tique.</b><br><br>\n";	

		if ($pages = $this->LoadAll("SELECT tag, time FROM ".$this->config["table_prefix"]."pages WHERE user = '".mysql_escape_string($this->GetUserName())."' AND tag NOT LIKE 'Comment%' ORDER BY tag ASC, time DESC"))
		{
			foreach ($pages as $page)
			{
				if ($last_tag != $page["tag"]) {
					$last_tag = $page["tag"];
					// XXX: strtoupper is locale dependent
					$firstChar = strtoupper($page["tag"][0]);
					if (!preg_match("/".WN_UPPER."/", $firstChar)) {
						$firstChar = "#";
					}
		
					if ($firstChar != $curChar) {
						if ($curChar) echo "<br>\n";
						echo "<b>$firstChar</b><br>\n";
						$curChar = $firstChar;
					}
	
					// echo entry
					echo "&nbsp;&nbsp;&nbsp;(",$page["time"],") (",$this->ComposeLinkToPage($page["tag"], "revisions", "history", 0),") ",$this->ComposeLinkToPage($page["tag"], "", "", 0),"<br>\n";
	
					$my_edits_count++;
				}
			}
			
			if ($my_edits_count == 0)
			{
				echo "<i>Vous n'avez pas modifi&eacute; de page.</em>";
			}
		}
		else
		{
			echo "<i>Aucune page trouv&eacute;e.</em>";
		}
	}
}
else
{
	echo "<i>Vous n'&ecirc;tes pas identifi&eacute; : impossible d'afficher la liste des pages que vous avez modifi&eacute;es.</em>";
}

?>
