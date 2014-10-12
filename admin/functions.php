<?php

// Return all post types in an array
if(! function_exists('coco_get_all_post_types')){
	function coco_get_all_post_types(){
		
		global $wp_post_types;
		
		$classic_pt = get_post_types( array(
										'public'=> true,
										'show_ui' => true,
										'_builtin' => true
										));
										
		$custom_pt = get_post_types( array(
										'public'=> true,
										'show_ui' => true,
										'_builtin' => false
										));
		
		$all_pt = array_merge($classic_pt,$custom_pt);
		
		// Get the post types names instead of slugs
		foreach($all_pt as $pt=>$name){
			
			$all_pt[$pt] = $wp_post_types[$pt]->label;
		}
		
		return $all_pt;
		
	}
}
