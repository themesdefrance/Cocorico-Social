<?php

function cocosocialTitreWrapper($content){
	$output = '<h2 style="font-size: 23px;font-weight: 400;padding: 9px 15px 4px 0px;line-height: 29px;">';
	$output .= $content;
	$output .= '</h2>';
	return $output;
}
CocoDictionary::register(CocoDictionary::WRAPPER, 'titre', 'cocosocialTitreWrapper');

function cocosocialUlWrapper($content){
	$output = '<ul class="slip-target">';
	$output .= $content;
	$output .= '</ul>';
	return $output;
}
CocoDictionary::register(CocoDictionary::WRAPPER, 'ul', 'cocosocialUlWrapper');

function cocosocialLiWrapper($content){
	$output = '<li>';
	$output .= $content;
	$output .= '</li>';
	return $output;
}
CocoDictionary::register(CocoDictionary::WRAPPER, 'li', 'cocosocialLiWrapper');

function cocosocialOrderedListShorthand($cocorico, $name, $label, $checkboxes){
	
	$stored = CoCoRequest::request($name);
	if (!$stored) $stored = $cocorico->getStore()->get($name);
	
	$local = array();
	foreach ($checkboxes as $index=>$value){
		$local[$index] = false;
	}
	
	foreach($local as $index=>$value){
		if(!array_key_exists($index,$stored))
			$stored[$index]=$value;
	}
	
	$reorderedCheckboxes = array();
	foreach ($stored as $index=>$value){
		$reorderedCheckboxes[$index] = $checkboxes[$index];
	}
	
	$cocorico->startWrapper('tr');
	$cocorico->startWrapper('th');
	$cocorico->component('raw', $label);
	$cocorico->endWrapper('th');
	
	$cocorico->startWrapper('td');
	$cocorico->startWrapper('ul');

	foreach ($reorderedCheckboxes as $value=>$label){
		$cocorico->startWrapper('li');
		$cocorico->component('boolean', $name.'['.$value.']', array('default'=>$stored[$value]))->filter('saveOrder', $name);
		$cocorico->component('label', $name.'['.$value.']', $label);
		$cocorico->endWrapper('li');
	}
	
	$cocorico->endWrapper('ul');
	$cocorico->endWrapper('td');
	$cocorico->endWrapper('tr');
	
	wp_register_script('cocosocial_slip', COCO_SOCIAL_URI.'/extensions/cocorico-social/slip.js', array(), '1', true);
	wp_enqueue_script('cocosocial_slip');
	wp_register_script('cocosocial_ordre', COCO_SOCIAL_URI.'/extensions/cocorico-social/ordre.js', array('jquery'), '1', true);
	wp_enqueue_script('cocosocial_ordre');
}
CocoDictionary::register(CocoDictionary::SHORTHAND, 'orderedList', 'cocosocialOrderedListShorthand');

function cocosocialSaveOrderFilter($value, $name, $component){
	$stored = CoCoRequest::request($name);
	$component->getStore()->set($name, $stored);
}
CocoDictionary::register(CocoDictionary::FILTER, 'saveOrder', 'cocosocialSaveOrderFilter');