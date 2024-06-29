<?php

/**
 * ImageCompliant pour WikiNi - GNU General Public License
 * 
 * @author MickaÃ«l Menu <mickael.menu@gmail.com>
 * @coauthor Emmanuel ROY <acksop@gmail.com>
 * @copyright Copyright (c) 2005 Mickal Menu
 * @copyright Copyright (c) 2024 Emmanuel ROY
 * @category actions
 * @version 0.0.1
 * @since 2005-03-21
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @link http://www.wikini.net/
 */

if (!defined("WIKINI_VERSION"))
{
  exit();
}

// RÃ©cupÃ©ration des paramÃ¨tres.
$img_src    = $this->GetParameter("src");
$img_desc   = $this->GetParameter("desc");
$img_align  = $this->GetParameter("align");
$img_link   = $this->GetParameter("link");
$img_target = $this->GetParameter("target");
$img_hidden = $this->GetParameter("hidden");
$img_style_def = $this->GetParameter("style");
$img_border = $this->GetParameter("border");
$img_width  = $this->GetParameter("width");
$img_height = $this->GetParameter("height");

// VÃ©rification du paramÃªtre 'src`.
if (empty($img_src)) 
{
  exit();
}

// Gestion du paramÃªtre 'hidden`.
if ($img_hidden)
{
  $img_div_style = "display: none";
}
else
{
  $img_div_style = "display: inline;";
}

// Gestion du paramÃªtre 'border`.
if (!$img_border) 
{
  $img_style = "border: 0";
}

//Gestion du parametre `width`
if ($img_width)
{
  $tag_width = 'width="'.$img_width.'"';
}
else
{
  $tag_width = '';
}

//Gestion du parametre `height`
if ($img_height)
{
  $tag_height = 'height="'.$img_height.'"';
}
else
{
  $tag_height = '';
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

if (!empty($img_style_def))
{
  $img_style .= $img_style_def;
}

// Gestion des paramÃ¨tres 'link` et 'target`.
if (!empty($img_link))
{
  echo '<a href="'.$img_link.'"';
  if (!empty($img_target) && ($img_target == '_blank' || $img_target == '_top')) {
    echo 'target="'.$img_target.'"';
  }
  echo '>';
}

// Affichage de l'image.
echo '<img class="image"';
if (!$img_border)
{
  echo ' style="'.$img_style.'"';
}
echo ' src="'.$img_src.'" '.$tag_height.' '.$tag_width.' alt="'.$img_desc.'" title="'.$img_desc.'" />';

if (!empty($img_link))
{
  echo '</a>';
}

echo '</div>';

?> 
