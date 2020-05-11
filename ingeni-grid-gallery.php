<?php
/*
Plugin Name: Ingeni Grid Gallery
Version: 2020.02
Plugin URI: http://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: http://ingeni.net
Description: Replaces standard Wordpress post galleries with Foundation Grid galleries.
*/

/*
Copyright (c) 2019 Ingeni Web Solutions
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

Disclaimer: 
	Use at your own risk. No warranty expressed or implied is provided.
	This program is free software; you can redistribute it and/or modify 
	it under the terms of the GNU General Public License as published by 
	the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 	See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


Requires : Wordpress 3.x or newer ,PHP 5 +

v2020.01 - Initial version, based on Ingeni grid Gallery v2019.04

v2020.02 - Support for the 'columns' parameter.

*/


//Init hook
add_action('init', 'grid_override_wp_gallery');
 
//Override function
function grid_override_wp_gallery()
{
    remove_shortcode('gallery');
    add_shortcode('gallery', 'ingeni_grid_gallery_shortcode');
}
 
//Custom gallery shortcode
function ingeni_grid_gallery_shortcode($atts, $content) {
	$retHtml = '';

	$params = shortcode_atts( array(
		'ids' => '',
		'orderby' => 'post__in',
		'columns' => '2',
		'container_class' => '',
		'link' => 'file', //file | link | <empty string> (for linking to attachment page)
		'close_grid_container' => 1
	), $atts );

	$large_cell_class = "medium-6"; // Two medium/large columns
	if ( $params['columns'] == "3" ) {
		$large_cell_class = "medium-6 large-4";
	}
	if ( $params['columns'] == "4" ) {
		$large_cell_class = "medium-4 large-3";
	}

	if ( $params['ids'] ) {
		if ($params['close_grid_container'] > 0) {
			$retHtml = '</div></div></div>';
		}
		$retHtml .= '<div class="grid-container full '.$params['container_class'].' photo-grid-wrap"><div class="grid-x grid-margin-x">';

		$imgIds = explode(",",$params['ids']);

		foreach( $imgIds as $imgId ) {
			$img_url = wp_get_attachment_image_src( $imgId, "large" );
			if ($img_url !== false) {
				$retHtml .= '<div class="cell small-12 '.$large_cell_class.'">';
				$retHtml .= '<div class="photo_grid_item" style="background-image: url('.$img_url[0].');"></div>';
				$retHtml .= '</div>';
			}
		}
		
		$retHtml .= '</div></div></div>';

		
		if ($params['close_grid_container'] > 0) {
			$retHtml .= '</div></div></div>';
		}
	}

	return $retHtml;
}



function ingeni_load_grid() {
	$dir = plugins_url( '', __FILE__ );

	//grid gallery
	if ( is_singular( 'post' ) || is_singular( 'page' ) ) {
		wp_enqueue_style( 'ingeni-grid-css', $dir . '/ingeni-grid-gallery.css' );
	}
		
		
	// Init auto-update from GitHub repo
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/BruceMcKinnon/ingeni-grid-gallery',
		__FILE__,
		'ingeni-grid-gallery'
	);
}
add_action( 'wp_enqueue_scripts', 'ingeni_load_grid' );


// Plugin activation/deactivation hooks
function ingeni_grid_activation() {
	flush_rewrite_rules( false );
}
register_activation_hook(__FILE__, 'ingeni_grid_activation');

function ingeni_grid_deactivation() {
  flush_rewrite_rules( false );
}
register_deactivation_hook( __FILE__, 'ingeni_grid_deactivation' );

?>