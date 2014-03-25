<?php
/*
Plugin Name: Ziofix SVG
Plugin URI: http://www.ziofix.fr
Description: Distributeur de SVG de toutes les couleurs, de toutes les formes
Author: Ziofix
Version: 1
Author URI: http://www.ziofix.fr
*/

/*  Copyright 2013  Ziofix  (email : jrlarcelet@ziofix.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('init', 'zfxSvg');
function zfxSvg()
{
	// Si on appelle la fonction
	if(isset($_GET['zfxSvg']))
	{
		// On vérifie que toutes les données sont bien présentent et que l'image envoyée est locale
		if(isset($_GET['img']) && isset($_GET['color']) && filter_var($_GET['img'], FILTER_VALIDATE_URL) === FALSE)
		{
			$img = mysql_escape_string($_GET['img']);
			$color = mysql_escape_string($_GET['color']);
			// On retrouve le chemin des fichiers svg ici : /wp-content/themes/letheme/img/svg/
			$url = str_replace('/wp-content/themes', '', get_theme_root()).str_replace(get_bloginfo('wpurl'), '', get_bloginfo('template_url').'/img/svg/'.$img.'.svg');
			// On vérifie que l'image existe
			if(file_exists($url))
			{
				ob_clean();
				$age = 3600*24*30*12;
				// On envoi l'en-tête
				header('Content-Type: image/svg+xml');
				header('Expires: '.gmdate ("D, d M Y H:i:s", time() + $age) . " GMT");
				header('Cache-Control: max-age='.$age.', must-revalidate');
				header('ETag: "'.md5(file_get_contents($url)).'"');
				// Chargement du contenu de l'image au format XML
				$svg = file_get_contents($url);
				$doc = new DOMDocument();
				$doc->preserveWhiteSpace = false;
				$doc->loadXML($svg);
				// Si on se situe dans un path
				$tags = $doc->getElementsByTagName("path");
				foreach ($tags as $tag) // On change l'attribu fill de chaque path
				{
					$svg_color = $tag->getAttribute('fill');
					$tag->setAttribute('fill', '#' . $color);
					$svg = $doc->saveXML($doc);
				}
				// Si on se situe dans un polygon
				$tags = $doc->getElementsByTagName("polygon");
				foreach ($tags as $tag)
				{
					$svg_color = $tag->getAttribute('fill');
					$tag->setAttribute('fill', '#' . $color);
					$svg = $doc->saveXML($doc);
				}
				// Si on se situe dans un line
				$tags = $doc->getElementsByTagName("line");
				foreach ($tags as $tag)
				{
					$svg_color = $tag->getAttribute('stroke');
					$tag->setAttribute('stroke', '#' . $color);
					$svg = $doc->saveXML($doc);
				}
				// Si on se situe dans un polyline
				$tags = $doc->getElementsByTagName("polyline");
				foreach ($tags as $tag)
				{
					$svg_color = $tag->getAttribute('fill');
					$tag->setAttribute('fill', '#' . $color);
					$svg = $doc->saveXML($doc);
				}
				// Si on se situe dans un rect
				$tags = $doc->getElementsByTagName("rect");
				foreach ($tags as $tag)
				{
					$svg_color = $tag->getAttribute('fill');
					$tag->setAttribute('fill', '#' . $color);
					$svg = $doc->saveXML($doc);
				}
				echo $svg;
				die();
			}
		}	
	}
	if(isset($_GET['zfxSvgPreloader']))
	{
		$css = file_get_contents(get_template_directory().'/main.css');
	
		$regex = "/img=(.*)&color=(.*)['|)]/";
		if(preg_match_all($regex, $css, $svg))
		{	
			ob_clean();
			$age = 3600;
			header('Expires: '.gmdate('r', ($age .'&gt;'. time() ? $age : time() + $age)).' GMT');
			header('Cache-Control: max-age=3600, must-revalidate');
			foreach($svg[0] as $zfxSvg)
			{
				echo '<img src="'.home_url().'?zfxSvg=1&'.str_replace(')', '', $zfxSvg).'" alt=""><br>';
			}
		}	
		die();
	}
}