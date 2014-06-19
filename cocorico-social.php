<?php
/*
Plugin Name: Cocorico Social
Plugin URI: https://www.themesdefrance.fr/plugins/coco-social
Description: The share plugin from Themes de France
Version: 1.0.0
Author: Alex from Themes de France
Author URI: https://www.themesdefrance.fr
Text Domain: cocosocial
Domain Path: /lang/
License: GPL v3

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'admin/Cocorico/Cocorico.php';

// Load Styles
function coco_social_load_style() {
	wp_enqueue_style( 'coco-social', plugins_url( '/style.css', __FILE__ ), false, '1.0.0', 'screen' );
}

add_action( 'wp_enqueue_scripts', 'coco_social_load_style' );

// Plugin Admin

function coco_social_menu_item(){
    	add_options_page('Cocorico Social', 'Cocorico Social', 'manage_options', 'coco-social', 'coco_social_options');
}

add_action('admin_menu','coco_social_menu_item');


function coco_social_load_textdomain() {
	$domain = 'cocosocial';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	
	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'init', 'coco_social_load_textdomain' );

function coco_social_options(){
    	include('admin/cocorico-social-admin.php');
}

// Plugin Functions

function coco_social_share($content) {
		
		$location = get_option('cocosocial_location', false);
		$networks =  get_option('cocosocial_networks');
		
		if(is_single() && $location && $networks!='') { 
			
			if(in_array('top', $location))
				$content = coco_social_buttons($networks,'top').$content;
			if(in_array('bottom', $location))
				$content = $content.coco_social_buttons($networks,'bottom');
            
        }
        return $content;
}
add_filter ('the_content', 'coco_social_share');

function coco_social_buttons($networks,$location){

			$nb_networks = count($networks);
			
			// Format
			$format = get_option('cocosocial_format');
			$share_message = get_option('cocosocial_bottom_message');
			
			// Apply the right class 
    		$buttons_class = coco_social_get_class($nb_networks);
			
            $buttons = "<div class='coco-social'>";
            
            if($location=='bottom')
            	$buttons.= ( $share_message ? "<h4>".sanitize_text_field($share_message)."</h4>" : '');
            
            $buttons.= "<ul class='coco-social-buttons $format $buttons_class'>";
            
            for($i=0;$i<$nb_networks;$i++){
                $buttons.= "<li>".coco_social_button($networks[$i],$format)."</li>";
            }

            $buttons.= "</ul></div>";
            
            return $buttons;
}

function coco_social_button($coco_network, $coco_format){
	
	global $post;
	$share_url = '';
	$name = $coco_network;
	
	switch($coco_network){
		case 'facebook' :
			$share_url = 'https://www.facebook.com/sharer/sharer.php?u='.urlencode(get_permalink($post->ID));
		break;
		case 'twitter' :
			$twitter = get_option('cocosocial_twitter_username');
			$share_url = 'http://twitter.com/share?url='.urlencode(get_permalink($post->ID)).'&text='.urlencode(get_the_title($post->ID)).( $twitter ? '&via='.$twitter : '');
		break;
		case 'googleplus' :
			$share_url = 'https://plus.google.com/share?url='.urlencode(get_permalink($post->ID));
			$name = 'Google+';
		break;
		case 'linkedin' :
			$share_url = 'http://www.linkedin.com/shareArticle?mini=true&url='.urlencode(get_permalink($post->ID)).'&title='.urlencode(get_the_title($post->ID)).'&summary='.urlencode(mb_substr(strip_shortcodes(strip_tags(get_the_content($post->ID))), 0, 200));
		break;
		default:
		$share_url = '';
		
	}
	
	switch($coco_format){
		case 'icon_text' :
			$button = '<a href="'.$share_url.'" title="'.sprintf(__('Share on %1$s','cocosocial'),ucfirst($name)).'" class="tdf-'.$coco_network.'" target="_blank"><i class="cocosocial-icon-'.$coco_network.'"></i>'.ucfirst($name).'</a>';
		break;
		
		case 'icon_only' :
			$button = '<a href="'.$share_url.'" title="'.sprintf(__('Share on %1$s','cocosocial'),ucfirst($name)).'" class="tdf-'.$coco_network.'" target="_blank"><i class="cocosocial-icon-'.$coco_network.'"></i></a>';
		break;
		
		case 'text_only' :
			$button = '<a href="'.$share_url.'" title="'.sprintf(__('Share on %1$s','cocosocial'),ucfirst($name)).'" class="tdf-'.$coco_network.'" target="_blank">'.ucfirst($name).'</a>';
		break;
		
		default:
			$button = '<a href="'.$share_url.'" title="'.sprintf(__('Share on %1$s','cocosocial'),ucfirst($name)).'" class="tdf-'.$coco_network.'" target="_blank"><i class="cocosocial-icon-'.$coco_network.'"></i>'.ucfirst($name).'</a>';
	}
	
	
	return $button;
}

function coco_social_get_class($number){

	$class = '';
	switch($number)
	{
		case 1:
			//$class='full';
			$class='halfs';
		break;
		case 2:
			$class='halfs';
		break;
		case 3:
			$class='thirds';
		break;
		default :
			$class='';
			
	}
	return $class;
}
