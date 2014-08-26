<?php

/* Get the right buttons class */
if(!function_exists('coco_social_get_class')){
	function coco_social_get_class($number,$size){
		
		$class = '';
		
		switch($size){
			case 'auto_width':
				return 'auto_width';
			break;
			case 'big_first':
				return 'big_first';
			break;
			default:
				$class = '';
			
		}
		if($size == 'auto_width')
			return 'auto_width';
		
		switch($number){
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
			case 4:
				$class='fourths';
			break;
			case 5:
				$class='fifths';
			break;
			case 6:
				$class='sixths';
			break;
			case 7:
				$class='sevenths';
			break;
			default :
				$class='';
		}
		return $class;
	}
}

// Thanks to https://gist.github.com/jonathanmoore/2640302
if(!function_exists('coco_social_get_count')){
	function coco_social_get_count($network_name){
		global $post;
		
		$post_url = get_permalink($post->ID);
		// Post with a lot of shares for testing purpose (read it by the way, it's awesome !)
		//$post_url = "http://conversionxl.com/pricing-experiments-you-might-not-know-but-can-learn-from/";
		
		switch($network_name){
			case 'facebook' :
				$count = coco_social_facebook_count($post_url, $post->ID);
			break;
			case 'twitter' :
				$count = coco_social_twitter_count($post_url, $post->ID);
			break;
			case 'googleplus' :
				$count = coco_social_googleplus_count($post_url, $post->ID);
			break;
			case 'linkedin' :
				$count = coco_social_linkedin_count($post_url, $post->ID);
			break;
			case 'pinterest' :
				$count = coco_social_pinterest_count($post_url, $post->ID);
			break;
			case 'viadeo' :
				$count = coco_social_viadeo_count($post_url, $post->ID);
			break;
			default :
				$count = '';
		}
		
		$count = '<span class="coco-count">'.$count.'</span>';
		
		if($network_name=='email')
			$count = '';
		
		return $count;
	}	
}

/* Get Facebook Share Count */

if(!function_exists('coco_social_facebook_count')){
	function coco_social_facebook_count($url , $id){
		
		$key = "fb-".$id;
		
		// testing only
		//delete_transient($key);
		
		if(!get_transient($key)){
		
			$url = 'http://graph.facebook.com/?id='.$url;
			$response = wp_remote_get($url);
			
			// Convert json response into php array
			$response = json_decode($response['body'], true);
			
			$count = isset($response['shares']) ? coco_social_convert_count($response['shares']) : '0';
			
			// Set transient for 12h
			set_transient($key, $count, 60 * 60 * 12);
		}
		return get_transient($key);
	}
}

/* Get Twitter Share Count */

if(!function_exists('coco_social_twitter_count')){
	function coco_social_twitter_count($url , $id){
		
		$key = "tw-".$id;
		if(!get_transient($key)){
		
			$url = 'https://cdn.api.twitter.com/1/urls/count.json?url='.$url;
			$response = wp_remote_get($url);
			
			// Convert json response into php array
			$response = json_decode($response['body'], true);
			
			$count = isset($response['count']) ? coco_social_convert_count($response['count']) : '0';
			
			// Set transient for 12h
			set_transient($key, $count, 60 * 60 * 12);
		}
		return get_transient($key);
	}
}

/* Get Google+ Share Count */

if(!function_exists('coco_social_googleplus_count')){
	function coco_social_googleplus_count($url , $id){
			
		$key = "gp-".$id;
		if(!get_transient($key)){
		
			// Thanks to http://bradsknutson.com/blog/get-google-share-count-url/
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
			$curl_results = curl_exec ($curl);
			curl_close ($curl);
			
			$json = json_decode($curl_results, true);
			
			$response = $json[0]['result']['metadata']['globalCounts']['count'];
			
			$count = isset($response) ? coco_social_convert_count($response) : '0';
			
			// Set transient for 12h
			set_transient($key, $count, 60 * 60 * 12);
		}
		return get_transient($key);
	}
}

/* Get Linkedin Share Count */

if(!function_exists('coco_social_linkedin_count')){
	function coco_social_linkedin_count($url , $id){
		
		$key = "lk-".$id;
		if(!get_transient($key)){
		
			$url = 'http://www.linkedin.com/countserv/count/share?format=json&url='.$url;
			$response = wp_remote_get($url);
			
			$response = json_decode($response['body'], true);
			
			$count = isset($response['count']) ? coco_social_convert_count($response['count']) : '0';
			
			// Set transient for 12h
			set_transient($key, $count, 60 * 60 * 12);
		}
		return get_transient($key);
	}
}

/* Get Pinterest Share Count */

if(!function_exists('coco_social_pinterest_count')){
	function coco_social_pinterest_count($url , $id){
		
		$key = "pt-".$id;
		if(!get_transient($key)){
		
			$url = 'http://api.pinterest.com/v1/urls/count.json?url='.$url;
			$response = wp_remote_get($url);
			
			// Convert json response into php array
			$response = $response['body'];
			
			$response = str_replace("receiveCount(","",$response);
			$response = substr($response,0, -1);
			
			$response = json_decode($response, true);
		
			$count = isset($response['count']) ? coco_social_convert_count($response['count']) : '0';
			// Set transient for 12h
			set_transient($key, $count, 60 * 60 * 12);
		}
		return get_transient($key);
	}
}

/* Get Viadeo Share Count */

if(!function_exists('coco_social_viadeo_count')){
	function coco_social_viadeo_count($url , $id){
	
		$key = "vd-".$id;
		if(!get_transient($key)){
		
			// Thanks to https://stackoverflow.com/questions/25115109/how-get-share-count-on-viadeo-link
			$url = 'https://api.viadeo.com/recommend?url='.$url;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
			$results = curl_exec($ch);
			$response = json_decode($results, true);
			
			$count = isset($response['count']) ? coco_social_convert_count($response['count']) : '0';
			// Set transient for 12h
			set_transient($key, $count, 60 * 60 * 12);
		}
		return get_transient($key);
	}
}

/* Convert big numbers in more digest ones */
// Thanks to https://stackoverflow.com/questions/8549463/show-1k-instead-of-1-000

if(!function_exists('coco_social_convert_count')){
	function coco_social_convert_count($coco_count){
	    
	    $coco_count = number_format($coco_count);
		$coco_input_count = substr_count($coco_count, ',');
	    
	    if($coco_input_count != '0'){
	        if($coco_input_count == '1'){
	        	// Share count between 1000 and 999999 shares
	        	$coco_next = substr($coco_count, -3, 1);
	            return substr($coco_count, 0, -4) . ( $coco_next != '0' ? '.' . $coco_next : '' ) . 'k';
	        } else if($coco_input_count == '2'){
	        	// Share count is more than 1000000
	        	$coco_next = substr($coco_count, -7, 1);
	            return substr($coco_count, 0, -8) . ( $coco_next != '0' ? '.' . $coco_next : '' ) . 'm';
	        } else if($coco_input_count == '3'){
	        	// That's a lot of shares !
	        	$coco_next = substr($coco_count, -10, 1);
	            return substr($coco_count, 0, -12) . ( $coco_next != '0' ? '.' . $coco_next : '' ) . 'b';
	        } else {
	            return '0';
	        }
	    } else {
	        return $coco_count;
	    }
	}
}
