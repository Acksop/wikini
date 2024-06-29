<?php
/* 
$Id: header.php 770 2024-05-29 15:16:45 eroy $
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003, 2004, 2006 Charles NEPOTE
Copyright 2002  Patrick PAUL
Copyright 2003  Eric DELORD
Copyright 2004, 2006, 2007  Didier LOISEAU
Copyright 2013, 2024  Emmanuel ROY
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

$charset = 'utf-8';
// Page info
$page_name = $this->GetPageTag();
$page_addr = $this->href();



//inclusion des cookies pour le fil de navigation d'ariane
include ("handlers/navigation_cookie.php");

header("Content-Type: text/html; charset=$charset");

// <head> : metas & style
if ($this->GetMethod() != 'show'
	|| !empty($_REQUEST['phrase'])
	|| !empty($_REQUEST['show_comments'])
	|| isset($_REQUEST['time']))
{
	$additionnal_metas = <<<EOD
        <meta name="robots" content="noindex, noarchive, nofollow" >
        <meta name="googlebot" content="index, noarchive, follow">
        <meta name="googlebot-news" content="noindex, noarchive, follow">
	    <meta name="the-WAYBACK-MACHINE" content="noindex, archive, follow">
EOD;
}
else
{
	$additionnal_metas = '';
}

$imported_style = isset($_COOKIE["sitestyle"]) ? htmlspecialchars($_COOKIE["sitestyle"]) : '';

$additional_style = array();
$additional_style[] = 'mobile.css';
$additional_style[] = 'wakka.css';


// Page contents
$body_attr = ($message = $this->GetMessage()) ? "onLoad=\"alert('".addslashes($message)."');\" " : "";
$wiki_name = $this->GetWakkaName();
$page_search = $this->href('', 'RechercheTexte', 'phrase=' . urlencode($page_name));
$root_page = $this->ComposeLinkToPage($this->config["root_page"]);
$navigation_links = $this->config["navigation_links"] ? $this->Format($this->config["navigation_links"]) : "";
$user_name = $this->Format($this->GetUserName());
$disconnect_link = $this->GetUser() ? '(<a href="' . $this->href('', 'ParametresUtilisateur', 'action=logout') . "\">D&eacute;connexion</a>)\n" : '';

/**
 * Addition  by Acksop on 2024 : meta_descr,title,keywords on TABLE pages
 */
$meta_keywords = $this->GetPageKeywords();
if($meta_keywords == ''){
    $meta_keywords = $this->GetConfigValue("meta_keywords");
}
$page_title = $this->GetPageTitle();
if($page_title == ''){
    $page_title = "WikiniPage «".$page_name."»";
}
$meta_description = $this->GetPageDescription();
if($meta_description == ''){
    $meta_description = $this->GetConfigValue("meta_description");
}
/**
 * End Addition
 */
?>
<!DOCTYPE html>
<html lang='fr'>
	<head>
        <meta charset="<?php echo $charset ?>">
        <title><?php echo $page_title." - ".$wiki_name; ?></title>
		
        <!--						BALISES LANG, VIEWPORT AND CHARSET				-->

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <!--						BALISES OPEN-GRAPH								-->

        <meta property="og:title" content="<?php echo $page_title." - ".$wiki_name; ?>" >
        <meta property="og:type" content="Wiki" >
        <meta property="og:url" content="<?php echo $page_addr ?>" >
        <!--<meta property="og:image" content="@OG-IMAGE" >-->
        <meta property="og:description" content="<?php echo $meta_description; ?>" >

        <!--						BALISES TWITTER								-->

        <meta name="twitter:title" content="<?php echo $page_title." - ".$wiki_name; ?>">
        <meta name="twitter:description" content="<?php echo $meta_description; ?>">
        <!-- <meta name="twitter:image" content="@X-IMAGE">-->
        <!--<meta name="twitter:site" content="">-->
        <!--<meta name="twitter:creator" content="@USERNAME">-->

        <!--						BALISES FACEBOOK							-->

        <!--<meta property="fb:admins" content="@FB-USERNAME-ID">-->

        <!--						BALISES DE META-RECHERCHES								-->
		<meta name="Category" content="Wiki">
        <meta name="Category" lang='fr' content="Wiki">
		<!--<meta name="Publisher" content="@PUBLISHER-NAME">-->
		<!--<meta name="Copyright" content="@PUBLISHER-COPYRIGHT">-->
        <!--<meta name="Date-Creation-yyyymmdd" content="@PUBLISHER-DATE-CREATION">-->
        <!--<meta name="Date-Revision-yyyymmdd" content="@PUBLISHER-DATE-REVISION">-->
        <!--<meta name="Expires"  content="@PUBLISHER-DATE-EXPIRATION">-->
		<!--<meta name="Distribution" content="@PUBLISHER-DISTRIBUTION">-->
		<meta name="Description" lang='fr' content="<?php echo $meta_description; ?>">
		<meta name='Identifier-URL' content="<?php echo $page_addr; ?>">

		<meta name="keywords" lang='fr' content="<?php echo $meta_keywords ?>" >
		
		<?php echo $additionnal_metas ?>

        
        <link rel="stylesheet" media="screen" href="/wakka.basic.css" >
		<?php foreach($additional_style as $value){
			echo <<<EOD
		<link rel="stylesheet" media="screen" href="/$value" >
EOD;
		}
		?>
        <link rel="stylesheet" media="screen" href="/assets/code-prettify/styles/desert.css" >
		<style type="text/css" media="all">
			@import url(<?php echo $imported_style ?>);
		</style>
		
		<script>

			function fKeyDown(e) {
				if (e == null) e = event;
				if (e.keyCode == 9) {
					if (typeof(document["selection"]) != "undefined") {	// ie
						e.returnValue = false;
						document.selection.createRange().text = String.fromCharCode(9);
					} else if (typeof(this["setSelectionRange"]) != "undefined") {	// other
						var start = this.selectionStart;
						this.value = this.value.substring(0, start) + String.fromCharCode(9) + this.value.substring(this.selectionEnd);
						this.setSelectionRange(start + 1, start + 1);
						return false;
					}
				}
				return true;
			}
			function doubleClickEdit(e)
			{
				if (e == null) e = event;
				source = document.all ? e.srcElement : e.target;
				if( source.nodeName == "TEXTAREA" || source.nodeName == "INPUT") return;
				document.location = '<?php echo addslashes($this->Href('edit')) ?>';
			}
			/** invert all checkboxes that are descendant of a given parent */
			function invert_selection(parent_id)
			{
				items = document.getElementById(parent_id).getElementsByTagName('input');
				for (i = 0; i < items.length; i++)
				{
					item = items[i];
					if (item && item.type == 'checkbox')
					{
						item.checked = !item.checked;
					}
				}
				return false;
			}
			
		</script>
	</head>


	<body <?php echo $body_attr ?> >

	<div>
		<span style='font-size:x-large;padding-left:1em;font-weight:bold;top:-5px;position:relative;'>
			<?php echo $wiki_name ?>
		</span>
		<br>
		<span style='display: block;margin:.5rem;'>
			<a style='display:inline;' href='<?php echo $this->config["base_url"].$this->config["root_page"]; ?>'>ð </a>
			<h1 class="page_name" style='font-size:large;display:inline;'><a href="<?php echo $page_search ?>"><?php echo $page_name ?></a></h1>
			<?php //echo $root_page ?>
			&nbsp;&nbsp;&nbsp;::&nbsp;&nbsp;&nbsp;
			<?php include "handlers/navigation.php";?> 
		</span>
	
	</div>
	
	<hr>

	<div style="line-height : 1.5;" class="header">		
		<?php echo $navigation_links ?> ::<?php echo $gtranslateDiv ?> Vous &ecirc;tes <?php echo $user_name ?> <?php echo $disconnect_link ?>
	</div>
	
