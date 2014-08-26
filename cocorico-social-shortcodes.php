<?php

// Shortcode to insert share buttons everywhere (in the loop for now)
// Shortcodes internationalization thanks to http://www.remicorson.com/how-to-create-translation-ready-shortcodes/

if(!function_exists('coco_social_shortcode')){
	function coco_social_shortcode($atts){
		
		$atts = shortcode_atts( array(
	        __('networks', 'cocosocial') => '',
	        __('counters', 'cocosocial') => false
	        ), $atts, 'shortcode_atts_cocosocial');
		
		$networks = $atts[__('networks', 'cocosocial')];
		$counters = $atts[__('counters', 'cocosocial')];
		
		if($networks == '')
			return;
		
		// if it's not an array, create an array with networks parameters
		$networks = ( !is_array($networks) ? explode( ",", $networks ) : $networks);
		
		// Display the share counters ?
		$counters = ( $counters == __('yes', 'cocosocial') ? true : false);
		
		// Generate buttons
		$buttons = coco_social_buttons($networks,'shortcode',$counters);
		
		return $buttons;
	}
}
add_shortcode('cocosocial', 'coco_social_shortcode');

if(!function_exists('coco_social_single_button')){
	function coco_social_single_button($atts){
		
		$atts = shortcode_atts( array(
	        __('network', 'cocosocial') => '',
	        'format'  => 'big_first',
	        __('counter','cocosocial') => false
	        ), $atts, 'shortcode_atts_cocosocial_button');
	        
	    $network = $atts[__('network', 'cocosocial')];
	    $counter = ( $atts[__('counter', 'cocosocial')] == __('yes', 'cocosocial') ? true : false);
	    
	    if($network == '')
	    	return;
	    
	    $networks = get_option('cocosocial_networks_blocks');
	    
	    // if input network isn't correct
	    if(!array_key_exists($network, $networks))
		    return '';
	    	
	    // translate format parameter (French => English)
	    $format = $atts['format'];
	    switch($format){
		    case 'texte_icone':
		    $format = 'icon_text';
		    break;
		    case 'icone_seule':
		    $format = 'icon_only';
		    break;
		    case 'texte_seul':
		    $format = 'text_only';
		    break;
		    case 'gros_bouton':
		    $format = 'big_first';
		    break;
	    }
	    
	    $button = "<div class='coco-social-single $format'>";
	    $button.= coco_social_button($network,$format,$counter);
	    $button.= "</div>";
	    
	    return $button;
	}
}
add_shortcode( 'cocosocial_button' , 'coco_social_single_button');
add_shortcode( 'bouton_cocosocial' , 'coco_social_single_button');