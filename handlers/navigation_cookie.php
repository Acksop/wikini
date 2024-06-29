 <?php

/*
    navigation_cookie.php - 2004 jmg, contrib 2024 acksop

    ne pas sÃ©parer de navigation.php
    
    mettre le fichier << cookie.php >> dans le rÃ©pertoire << /handlers/ >>

    mettre << include ("cookie.php"); >> 
    dans le fichier << /actions/header.php >> tout au dï¿½but du code.

*/

 function setANewCookie($name,$value,$options){
     if (version_compare(phpversion(), '6.0.0', '<')) {
         //if ((float)phpversion() < 6.0) {
         setcookie($name,$value,$options['expires'], $options['path'].'; samesite='.$options['samesite'],$options['domain'], $options['secure'], $options['httponly']);
     }else{
         setcookie($name,$value,$options);
     }
 }
 function getLastParamCookie($set = true,$domain = ''){
     if($domain !== ''){
         if ($set) {
             $arr_cookie_options = array(
                 'expires' => time() + 3600 * 24 * 365,
                 'path' => '/',
                 'domain' => $domain, // leading dot for compatibility or use subdomain
                 'secure' => true,     // or false
                 'httponly' => true,    // or false
                 'samesite' => 'Lax' // None || Lax  || Strict
             );
         } else {
             $arr_cookie_options = array(
                 'expires' => time() - 4200,
                 'path' => '/',
                 'domain' => $domain, // leading dot for compatibility or use subdomain
                 'secure' => true,     // or false
                 'httponly' => true,    // or false
                 'samesite' => 'Lax' // None || Lax  || Strict
             );
         }
     }else {
         if ($set) {
             $arr_cookie_options = array(
                 'expires' => time() + 3600 * 24 * 365,
                 'path' => '/',
                 'domain' => $_SERVER['HTTP_HOST'], // leading dot for compatibility or use subdomain
                 'secure' => true,     // or false
                 'httponly' => true,    // or false
                 'samesite' => 'Lax' // None || Lax  || Strict
             );
         } else {
             $arr_cookie_options = array(
                 'expires' => time() - 4200,
                 'path' => '/',
                 'domain' => $_SERVER['HTTP_HOST'], // leading dot for compatibility or use subdomain
                 'secure' => true,     // or false
                 'httponly' => true,    // or false
                 'samesite' => 'Lax' // None || Lax  || Strict
             );
         }
     }
     return $arr_cookie_options;
 }

 $arr_cookie_options = getLastParamCookie();

 $nom = "historique" ;


 if(isset($_COOKIE[$nom])) {
     $histo_tab = explode("|", $_COOKIE[$nom]);
     $histo_tab_unique = array_unique($histo_tab);

     $nb_pages_visit = count($histo_tab_unique);

     //pour ne laisser que les 10 derniÃ¨res pages visitÃ©es
     $debut_histo = $nb_pages_visit - 10;

     ($debut_histo < 0)?$debut_histo=0:$debut_histo=$debut_histo;
     $i = 0;
     $histo='';

     foreach ($histo_tab_unique as $element) {
         if($i < $debut_histo){
             $i = $i + 1;
             continue;
         }

         if($i == $debut_histo){
             if($element !== urlencode($this->GetPageTag())){
                 $histo = $element;
             }
         }else if($i > $debut_histo){
             if($element !== urlencode($this->GetPageTag())){
                $histo .= "|". $element;
             }
         }

         $i = $i + 1;
     }
     setANewCookie( $nom, $histo."|".urlencode($this->GetPageTag()), $arr_cookie_options );
 }else{
     setANewCookie( $nom, urlencode($this->GetPageTag()), $arr_cookie_options );
 }