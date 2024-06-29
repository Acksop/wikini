<?php

// Vï¿½rification de sï¿½curitï¿½
if (!defined('WIKINI_VERSION')) {
    die ('acc&egrave;s direct interdit');
}

/*
Description: Handlers pour affichages SVG 
auteurs: Yann Le Guennec - Charles Nepote
version 0.2
22.02.2005
*/
header("Content-type: image/svg+xml");
$a['reseaupagecourante'] = 1;
$a['touteslespages'] = 1;
$b = $_GET['svg'];
if(isset($a[$b])) {
    $url = $this->config["handler_path"]."/page/svg/".$b.".php";
    if(is_file($url)) {
        include($url);
    }
}
?> 