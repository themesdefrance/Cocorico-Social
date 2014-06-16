<?php


function cocosocialTitreWrapper($content){
	$output = '<h2 style="font-size: 23px;font-weight: 400;padding: 9px 15px 4px 0px;line-height: 29px;">';
	$output .= $content;
	$output .= '</h2>';
	return $output;
}
CocoDictionary::register(CocoDictionary::WRAPPER, 'titre', 'cocosocialTitreWrapper');