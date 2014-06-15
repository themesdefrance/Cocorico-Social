<?php
/*
Plugin Name: Cocorico Social
Plugin URI: https://www.themesdefrance.fr/plugins/coco-social
Description: The share plugin from Themes de France
Version: 1.0.0
Author: Alex from Themes de France
Author URI: https://www.themesdefrance.fr
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

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

function coco_social_options(){
    	include('admin/cocorico-social-admin.php');
}

// Plugin Functions

function coco_social_share_top($content) {
		
		$location = get_option('cocosocial_location');
		$networks =  get_option('cocosocial_networks');
		
		if(is_single() && $location[0] == 'top' && $networks!='') { 
			
			$nb_networks = count($networks);
			
			// Format
			$format = get_option('cocosocial_format');
			
			// Apply the right class 
    		$buttons_class = coco_social_get_class($nb_networks);
			
            $buttons = "<div class='coco-social'>";
            $buttons.= "<ul class='coco-social-buttons $format $buttons_class'>";
            
            for($i=0;$i<$nb_networks;$i++){
                $buttons.= "<li>".coco_social_button($networks[$i],$format)."</li>";
            }

            $buttons.= "</ul></div>";
            $content = $buttons.$content;
        }
        return $content;
}
add_filter ('the_content', 'coco_social_share_top');

function coco_social_share_bottom($content) {

		$location = get_option('cocosocial_location');
		$networks =  get_option('cocosocial_networks');
		
		if(is_single() && $location[0] == 'bottom' && $networks!='') { 
		
			$nb_networks = count($networks);
    		
    		// Format
			$format = get_option('cocosocial_format');
			
			// Apply the right class 
    		$buttons_class = coco_social_get_class($nb_networks);
    		
            $content.= "<div class='coco-social'>";
            $content.= "<h4>Partager cet article</h4>";
            $content.= "<ul class='coco-social-buttons $format $buttons_class'>";
            
            for($i=0;$i<$nb_networks;$i++){
                $content.= "<li>".coco_social_button($networks[$i],$format)."</li>";
            }
			
            $content.= "</ul></div>";
        }
        return $content;
}
add_filter ('the_content', 'coco_social_share_bottom');

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
			$share_url = 'http://www.linkedin.com/shareArticle?mini=true&url='.urlencode(get_permalink($post->ID));
		break;
		default:
		$share_url = '';
		
	}
	
	switch($coco_format){
		case 'icon_text' :
			$button = '<a href="'.$share_url.'" title="'.sprintf(__('Partager sur %1$s','cocosocial'),ucfirst($name)).'" class="tdf-'.$coco_network.'" target="_blank"><i class="cocosocial-icon-'.$coco_network.'"></i>'.ucfirst($name).'</a>';
		break;
		
		case 'icon_only' :
			$button = '<a href="'.$share_url.'" title="'.sprintf(__('Partager sur %1$s','cocosocial'),ucfirst($name)).'" class="tdf-'.$coco_network.'" target="_blank"><i class="cocosocial-icon-'.$coco_network.'"></i></a>';
		break;
		
		case 'text_only' :
			$button = '<a href="'.$share_url.'" title="'.sprintf(__('Partager sur %1$s','cocosocial'),ucfirst($name)).'" class="tdf-'.$coco_network.'" target="_blank">'.ucfirst($name).'</a>';
		break;
		
		default:
			$button = '<a href="'.$share_url.'" title="'.sprintf(__('Partager sur %1$s','cocosocial'),ucfirst($name)).'" class="tdf-'.$coco_network.'" target="_blank"><i class="cocosocial-icon-'.$coco_network.'"></i>'.ucfirst($name).'</a>';
	}
	
	
	return $button;
}

function coco_social_get_class($number){

	$class = '';
	switch($number)
	{
		case 1:
			$class='full';
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

// Exemples urls

// https://www.facebook.com/sharer/sharer.php?u=http://www.businessinsider.com/chrome-browser-share-2014-6

// http://www.businessinsider.com/chrome-browser-share-2014-6&via=sai&text=Internet%20Explorer%20Has%20Basically%20Been%20Annihilated%20By%20Google%27s%20Chrome%20Browser

// https://plus.google.com/share?url=http://www.businessinsider.com/chrome-browser-share-2014-6

// http://www.linkedin.com/shareArticle?mini=true&url=http://www.businessinsider.com/chrome-browser-share-2014-6&title=Internet%20Explorer%20Has%20Basically%20Been%20Annihilated%20By%20Google%27s%20Chrome%20Browser&summary=On%20mobile%20devices,%20probably%20the%20most%20important%20arena%20for%20browsers%20right%20now%20due%20to%20its%20growth,%20Explorer%20barely%20exists.

