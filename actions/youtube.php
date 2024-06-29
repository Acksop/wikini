<?php

/**
 * YouTubeCompliant pour WikiNi - GNU General Public License
 * 
 * @author Emmanuel ROY <acksop@gmail.com>
 * @copyright Copyright (c) 2018 Emmanuel ROY
 * @category actions
 * @version 0.0.1
 * @since 2018-07-16
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @link http://www.wikini.net/
 */

if (!defined("WIKINI_VERSION"))
{
  exit();
}
// RÃ©cupÃ©ration des paramÃ¨tres.
$img_src    = $this->GetParameter("src");
$img_align  = $this->GetParameter("align");
$img_div_style = $this->GetParameter("style");
$img_width  = $this->GetParameter("width");
$img_height = $this->GetParameter("height");

// VÃ©rification du paramÃªtre 'src`.
if (empty($img_src)) 
{
  exit();
}
// VÃ©rification du paramÃªtre 'src`.
if (empty($img_width)) 
{
$img_width  = "583";
}
// VÃ©rification du paramÃªtre 'src`.
if (empty($img_height)) 
{
$img_height = "360";
}

// Gestion du paramÃªtre 'align` et crÃ©ation de la "div" contenant l'image.
if (!empty($img_align) && ($img_align == 'left' || $img_align == 'center' || $img_align == 'right'))
{
  echo '<div align="'.$img_align.'">';
}
else
{
  echo '<div style="'.$img_div_style.'">';
}

// Affichage de l'image.
echo '<iframe src="https://www.youtube.com/embed/'.$img_src.'" width="'.$img_width.'" height="'.$img_height.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';

echo '</div>';

?> 
