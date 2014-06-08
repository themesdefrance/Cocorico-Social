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
        if(!is_feed() && !is_home()) { 
                $buttons = "<div id='coco-social'>";
                $buttons.= "<ul class='coco-social-buttons'>";
                $buttons.= "<li>".coco_social_button('facebook')."</li>";
				$buttons.= "<li>".coco_social_button('twitter')."</li>";
				$buttons.= "<li>".coco_social_button('googleplus')."</li>";
				$buttons.= "<li>".coco_social_button('linkedin')."</li>";
                $buttons.= "</div>";
                $content = $buttons.$content;
        }
        return $content;
}
add_filter ('the_content', 'coco_social_share_top');

function coco_social_share_bottom($content) {
        if(!is_feed() && !is_home()) { 
                $content.= "<div id='coco-social'>";
                $content.= "<h4>Partager cet article</h4>";
                $content.= "<ul class='coco-social-buttons'>";
                $content.= "<li>".coco_social_button('facebook')."</li>";
				$content.= "<li>".coco_social_button('twitter')."</li>";
				$content.= "<li>".coco_social_button('googleplus')."</li>";
				$content.= "<li>".coco_social_button('linkedin')."</li>";
                $content.= "</div>";
        }
        return $content;
}
add_filter ('the_content', 'coco_social_share_bottom');

function coco_social_button($network){
	
	global $post;
	$share_url = '';
	$name = $network;
	switch($network){
		case 'facebook' :
			$share_url = 'https://www.facebook.com/sharer/sharer.php?u=';
		break;
		case 'twitter' :
			$share_url = 'http://twitter.com/share?url=';
		break;
		case 'googleplus' :
			$share_url = 'https://plus.google.com/share?url=';
			$name = 'Google+';
		break;
		case 'linkedin' :
			$share_url = 'http://www.linkedin.com/shareArticle?mini=true&url=';
		break;
		default:
		$share_url = '';
		
	}
	
	$button = '<a href="'.$share_url.get_permalink($post->ID).'" title="'.sprintf(__('Partager sur %1$s','coco_social'),ucfirst($name)).'" class="tdf-'.$network.'"><i class="cocosocial-icon-'.$network.'"></i>'.ucfirst($name).'</a>';
	return $button;
}

// Exemples urls

// https://www.facebook.com/sharer/sharer.php?u=http://www.businessinsider.com/chrome-browser-share-2014-6

// http://www.businessinsider.com/chrome-browser-share-2014-6&via=sai&text=Internet%20Explorer%20Has%20Basically%20Been%20Annihilated%20By%20Google%27s%20Chrome%20Browser

// https://plus.google.com/share?url=http://www.businessinsider.com/chrome-browser-share-2014-6

// http://www.linkedin.com/shareArticle?mini=true&url=http://www.businessinsider.com/chrome-browser-share-2014-6&title=Internet%20Explorer%20Has%20Basically%20Been%20Annihilated%20By%20Google%27s%20Chrome%20Browser&summary=On%20mobile%20devices,%20probably%20the%20most%20important%20arena%20for%20browsers%20right%20now%20due%20to%20its%20growth,%20Explorer%20barely%20exists.

