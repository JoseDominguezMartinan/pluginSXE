<?php

/*
Plugin Name: pluginPrueba
Plugin URI:  http://link to your plugin homepage
Description: Si introducimos un titulo que ya existe, nos lo reemplaza por otro con un numero al final
Version:     1.0
Author:      Jose Dominguez
Author URI:  http://link to your website
License:     GPL2 etc
License URI: https://link to your plugin license

Copyright YEAR PLUGIN_AUTHOR_NAME (email : your email address)
(Plugin Name) is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
(Plugin Name) is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with (Plugin Name). If not, see (http://link to your plugin license).
*/

// funcion para renombrar los titutlos repetidos
function renym_content_replace( $title ) {
	
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	
	$postid = get_the_ID();

	
	// creamos la sentencia sql
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$sql = "SELECT `post_title` FROM `wp5_posts` WHERE `post_author`= 1 AND `post_status`='publish' AND `ID`!= $postid AND post_title LIKE '$title%' ORDER BY `ID`";

	 $consulta=$wpdb->get_results($sql,OBJECT);
	
	$search=Array();
	$replace=Array();
	
	
  if(count($consulta)!=null){
		for($i=0;$i<count($consulta);$i++){
			$search[$i]=$consulta[$i]->post_title;
			if($search[$i]==$title)
				$coincide=true;
			else
			$coincide=false;
			if($coincide==false){
				$replace[$i]=$search[$i];
				$titulo=$replace[$i];
			}
			else{
				$replace[$i]=$search[$i].$i;
				
				$titulo=$replace[$i];
				
			}
	}
  }
	
	if($coincide==true){
	
		$sql2 ="UPDATE `wp5_posts` SET `post_title`='$titulo' WHERE `ID` =$postid";
		$wpdb->query($sql2,OBJECT);
		return str_replace( $search, $replace, $title );
	}
 else{
		return $title;
 }
	
}
add_filter( 'the_title', 'renym_content_replace' );


// funcion para ver la estructura del array de lo que devuelve la sentencia sql, para asi poder comparar con el texto que nosotros introducimos

function dam18_shortcodes_init(){
  function dam18_shortcode($atts = [], $content = null){
    // do something to $content
    global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	
	// creamos la sentencia sql
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$sql = "SELECT `post_title` FROM `wp5_posts` WHERE `post_author`= 1 AND `post_status`='publish'  ";
	
	 $search=$wpdb->get_results($sql,OBJECT);
	 
    $content = "<div id='algo'>".print_r($search[0]->post_title)."</div>";
    // always return
    return $content;
  }
  add_shortcode('dam18', 'dam18_shortcode');
}

add_action('init', 'dam18_shortcodes_init');



?>