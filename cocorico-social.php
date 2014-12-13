<?php
/*
Plugin Name: Cocorico Social
Plugin URI: https://www.themesdefrance.fr/plugins/coco-social
Description: The social share plugin from Themes de France
Version: 1.1.9.3
Author: Themes de France
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

define('COCO_SOCIAL_URI', plugin_dir_url(__FILE__).'admin/Cocorico/');
define('COCO_SOCIAL_COCORICO_PREFIX', 'cocosocial_');

// Cocorico loading
if(is_admin())
	require_once 'admin/Cocorico/Cocorico.php';

// Load the plugins necessary functions
require_once 'cocorico-social-functions.php';

// Load Shortcodes
require_once 'cocorico-social-shortcodes.php';

// Load translations
function coco_social_load_textdomain() {
	$domain = 'cocosocial';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	
	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'init', 'coco_social_load_textdomain' );

// Plugin Admin
function coco_social_menu_item(){
	add_options_page('Cocorico Social', 'Cocorico Social', 'manage_options', 'coco-social', 'coco_social_options');
}
add_action('admin_menu','coco_social_menu_item');

function coco_social_options(){
	include('admin/cocorico-social-admin.php');
}

// Load Styles
function coco_social_load_style() {
	wp_enqueue_style( 'coco-social', plugins_url( '/style.css', __FILE__ ), false, '1.1.0', 'screen' );
}
add_action( 'wp_enqueue_scripts', 'coco_social_load_style' );

function coco_social_admin_enqueue(){
	wp_register_style( 'cocosocial_custom_admin_css', COCO_SOCIAL_URI . '/extensions/cocorico-social/admin-style.css', false );
	wp_enqueue_style( 'cocosocial_custom_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'coco_social_admin_enqueue' );

// Setting link thanks to http://www.geekpress.fr/wordpress/tutoriel/ajouter-reglages-plugins-1154/
function coco_social_action_links( $links, $file ) {
    array_unshift( $links, '<a href="' . admin_url( 'options-general.php?page=coco-social' ) . '">' . __( 'Settings', 'cocosocial' ) . '</a>' );
    return $links;
}
add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), 'coco_social_action_links', 10, 2 );

// Disable sharing Metaboxe loading
if (!function_exists('cocoricosocial_meta_boxes')){
	function cocoricosocial_meta_boxes(){
		
		$posttype = get_post_type();
		$posttypes = get_option('cocosocial_pt_presence');
		
		if(is_array($posttypes)){
		
			if(in_array($posttype, $posttypes) && current_user_can('level_1')){
			
				add_meta_box('cocosocial_disable_box',
						 __('Disable share buttons ?', 'cocosocial'),
						 'cocosocial_disable_sharing',
						 $posttype,
						 'side',
						 'default'
				);	
			}
		}
	}
}
add_action('add_meta_boxes', 'cocoricosocial_meta_boxes');

if (!function_exists('cocosocial_disable_sharing')){
	function cocosocial_disable_sharing($post){
		
		$template = get_post_meta($post->ID, '_wp_page_template', true);
		
		$form = new Cocorico(COCO_SOCIAL_COCORICO_PREFIX, false);
		$form->startForm();
		
		if($template == 'default'){
			$form->setting(array('type'=>'boolean',
							 'label'=>__('Check this to disable share buttons on this content.', 'cocosocial'),
							 'name'=>'disabled',
							 'options'=>array(
							 	'default'=>false
							 )));
		}else{ // No sharing buttons on custom page templates
			$form->component('raw', __('Sharing is disabled on custom page templates. Use shortcodes to display the buttons','cocosocial'));	
		}
		
		$form->endForm();
		$form->render();	
			
	}
}

// Plugin Main Functions

if(!function_exists('coco_social_share')){
	function coco_social_share($content) {
			
			global $post;
			
			// Which buttons to display ?
			$networks =  get_option('cocosocial_networks_blocks');
			
			// Where the buttons have to show up ? (top || bottom || archives)
			$location = get_option('cocosocial_location', false);

			// Do we have to show the share counters ?
			$counters = (get_option('cocosocial_count_activation') ? true : false);
			
			// On which do we have to display the share buttons
			$posttypes = get_option('cocosocial_pt_presence');
			
			// Are buttons disabled on this content ?
			$disabled = get_post_meta($post->ID, 'cocosocial_disabled',true);
			
			if(!$disabled){
				
				// Are we on a single post/page/postype ? 					Are we on a page where no page template is used ?
				if(is_singular($posttypes) && $location && $networks!='' && !is_page_template()) { 
					
						if(in_array('top', $location))
							$content = coco_social_buttons($networks,'top',$counters).$content;
						if(in_array('bottom', $location))
							$content = $content.coco_social_buttons($networks,'bottom',$counters);
		        }
		        
		        if(in_array('archives', $location)){
				    if(is_home() || is_archive() || is_search() || is_tax() || is_post_type_archive($posttypes))
				    	$content = $content.coco_social_buttons($networks,'archives',$counters);
			    }
		    }
		    
	        return $content;
	}
}
add_filter ('the_content', 'coco_social_share');
add_filter ('the_excerpt', 'coco_social_share');

if(!function_exists('coco_social_buttons')){
	function coco_social_buttons($networks,$location,$counters){
		
		// Are we in a shortcode ?
		if($location == 'shortcode'){
			// Set up the $network array in order to get the right class
			$networks = array_fill_keys($networks,1);
		}
		
		// Do we show the share counters ?
		$counters_class = ($counters ? 'counters-on' : '');
		
		// How many networks are active ?
		$networks_array = array_count_values($networks);
		
		// Format
		$format = get_option('cocosocial_format');
		$share_message = get_option('cocosocial_bottom_message');
		
		// Width
		$width = get_option('cocosocial_width');
		
		// Apply the right class 
		$buttons_class = coco_social_get_class($networks_array[1],$width);
		
		$buttons = apply_filters('coco_social_before_div_'.$location, '');
		
        $buttons.= "<div class='coco-social $location'>";
        
        if($location=='bottom')
        	$buttons.= ( $share_message ? "<h4>".sanitize_text_field($share_message)."</h4>" : '');
        
        $buttons.= apply_filters('coco_social_before_ul_'.$location, '');
        
        if($buttons_class == 'big_first'){
        	$format = $buttons_class;
        	$buttons.= "<ul class='coco-social-buttons $format $counters_class'>";
        }else{
            $buttons.= "<ul class='coco-social-buttons $format $counters_class $buttons_class'>";
        }
        
        $buttons.= apply_filters('coco_social_before_first_li_'.$location, '');
        
        foreach ($networks as $network=>$display){
			if (!$display) continue;
			$count = coco_social_get_count($network);
			$buttons.= "<li>".coco_social_button($network,$format,$counters)."</li>";
			
		}
		
		$buttons.= apply_filters('coco_social_after_last_li_'.$location, '');
		
        $buttons.= "</ul>";
        
        $buttons.= apply_filters('coco_social_after_ul_'.$location, '');
        
        $buttons.= "</div>";
        
        $buttons.= apply_filters('coco_social_after_div_'.$location, '');
        
        return $buttons;
	}
}

if(!function_exists('coco_social_button')){

	function coco_social_button($coco_network, $coco_format, $coco_counters = false){
		
		global $post;
		
		$post_title = urlencode(html_entity_decode(get_the_title($post->ID)));
		$post_url = urlencode(get_permalink($post->ID));
		$post_summary = urlencode(esc_attr(mb_substr(strip_shortcodes(strip_tags(get_the_content($post->ID))), 0, 200)));
		
		$share_url = '';
		$name = $coco_network;
		
		// Set up the share url for each network
		switch($coco_network){
			case 'facebook' :
				$share_url = 'https://www.facebook.com/sharer/sharer.php?u='.$post_url;
			break;
			case 'twitter' :
				$twitter = get_option('cocosocial_twitter_username');
				$twitter_hashtags = '';
				if(apply_filters('coco_social_cat_hashtags', true))
					$twitter_hastags = urlencode( implode( ',', wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) ) ) );
				if(has_tag() && apply_filters('coco_social_tag_hashtags', true) )
					$twitter_hastags .= urlencode( ','.implode( ',', wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ) ) );
				$share_url = 'http://twitter.com/intent/tweet?url=' . $post_url . '&text=' . $post_title . ( $twitter ? '&via='.$twitter : '') . '&hashtags=' . $twitter_hashtags;
			break;
			case 'googleplus' :
				$share_url = 'https://plus.google.com/share?url=' . $post_url;
				$name = 'Google+';
			break;
			case 'linkedin' :
				$share_url = 'http://www.linkedin.com/shareArticle?mini=true&url=' . $post_url . '&title=' . $post_title . '&summary=' . $post_summary; 
			break;
			case 'viadeo' : 
				$share_url = 'http://www.viadeo.com/?&url=' . $post_url . '&title=' . $post_title;
			break;
			case 'pinterest' :
				$pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				$share_url = 'http://pinterest.com/pin/create/button/?url=' . $post_url . '&media=' . $pinterestimage[0] . '&description=' . $post_title;
			break;
			case 'email' :
				$email_intro = apply_filters('coco_social_email_body',__('Hey, I discovered this post and I wanted to share it with you. Tell me what you think : ','cocosocial'));
				$share_url = 'mailto:?subject='.urldecode($post_title).'&body='.$email_intro.' '.urldecode($post_summary).' '.$post_url;
			break;
			default:
				$share_url = $post_url;
		}
		
		switch($coco_format){
			case 'icon_text' :
				$button = '<a onclick="window.open(this.href, \'partage\', \'height=400, width=500, top=300, left=300, toolbar=no, menubar=yes, location=no, resizable=yes, scrollbars=no, status=no\'); return false;" href="'.$share_url.'" title="'.apply_filters('coco_social_share_label', __('Share on','cocosocial')).' '.ucfirst($name).'" class="coco-'.$coco_network.'" rel="nofollow"><i class="cocosocial-icon-'.$coco_network.'"></i><span>'.ucfirst($name).($coco_counters ? coco_social_get_count($coco_network) : '').'</span></a>';
			break;
			
			case 'icon_only' :
				$button = '<a onclick="window.open(this.href, \'partage\', \'height=400, width=500, top=300, left=300, toolbar=no, menubar=yes, location=no, resizable=yes, scrollbars=no, status=no\'); return false;" href="'.$share_url.'" title="'.apply_filters('coco_social_share_label', __('Share on','cocosocial')).' '.ucfirst($name).'" class="coco-'.$coco_network.'" rel="nofollow"><i class="cocosocial-icon-'.$coco_network.'"></i>'.($coco_counters ? coco_social_get_count($coco_network) : '').'</a>';
			break;
			
			case 'text_only' :
				$button = '<a onclick="window.open(this.href, \'partage\', \'height=400, width=500, top=300, left=300, toolbar=no, menubar=yes, location=no, resizable=yes, scrollbars=no, status=no\'); return false;" href="'.$share_url.'" title="'.apply_filters('coco_social_share_label', __('Share on','cocosocial')).' '.ucfirst($name).'" class="coco-'.$coco_network.'" rel="nofollow">'.ucfirst($name).($coco_counters ? coco_social_get_count($coco_network) : '').'</a>';
			break;
			
			case 'big_first' :
			$button = '<a onclick="window.open(this.href, \'partage\', \'height=400, width=500, top=300, left=300, toolbar=no, menubar=yes, location=no, resizable=yes, scrollbars=no, status=no\'); return false;" href="'.$share_url.'" title="'.apply_filters('coco_social_share_label', __('Share on','cocosocial')).' '.ucfirst($name).'" class="coco-'.$coco_network.'" rel="nofollow"><i class="cocosocial-icon-'.$coco_network.'"></i><span>'.apply_filters('coco_social_big_first_share_label', __('Share this via','cocosocial')).' '.ucfirst($name).'</span></a>';
			break;
			
			default:
				$button = '<a onclick="window.open(this.href, \'partage\', \'height=400, width=500, top=300, left=300, toolbar=no, menubar=yes, location=no, resizable=yes, scrollbars=no, status=no\'); return false;" href="'.$share_url.'" title="'.apply_filters('coco_social_share_label', __('Share on','cocosocial')).' '.ucfirst($name).'" class="coco-'.$coco_network.'" rel="nofollow"><i class="cocosocial-icon-'.$coco_network.'"></i>'.ucfirst($name).'</a>';
		}
		
		return $button;
	}

}
