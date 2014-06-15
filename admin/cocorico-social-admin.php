<?php

////////////////////////////////////
// Cocorico Framework
////////////////////////////////////

require_once 'Cocorico/Cocorico.php';

$form = new Cocorico('cocosocial_');

$form->startWrapper('titre');
$form->component('raw', __('Options Cocorico Social', 'cocosocial'));
$form->endWrapper('titre');

$form->groupHeader(array('general'=>__('Général', 'cocosocial'), 
						 'networks'=>__('Réseaux', 'cocosocial')));

$form->startWrapper('tab', 'general');

$form->startForm();

$form->setting(array('type'=>'checkbox',
					 'label'=>__('Placement des boutons', 'cocosocial'),
					 'name'=>'location',
					 'checkboxes'=>array(
					 	 'top'=>__('Avant les articles', 'cocosocial'),
						 'bottom'=>__('Après les articles', 'cocosocial')
					 ),
					 'options'=>array(
					 	'after'=>'<br/>'
					 )));


$form->setting(array('type'=>'checkbox',
					 'label'=>__('Réseaux à utiliser', 'cocosocial'),
					 'name'=>'networks',
					 'checkboxes'=>array(
					 	 'facebook'=>__('Facebook', 'cocosocial'),
						 'twitter'=>__('Twitter', 'cocosocial'),
						 'googleplus'=>__('Google+', 'cocosocial'),
						 'linkedin'=>__('LinkedIn', 'cocosocial')
					 ),
					 'options'=>array(
					 	'after'=>'<br/>'
					 )));

$form->setting(array('type'=>'radio',
					 'label'=>__('Format des boutons', 'cocosocial'),
					 'name'=>'format',
					 'radios'=>array(
					 	 'icon_text'=>__('Texte et icônes', 'cocosocial'),
						 'icon_only'=>__('Icônes seules', 'cocosocial'),
						 'text_only'=>__('Texte seul', 'cocosocial')
					 ),
					 'options'=>array(
					 	'after'=>'<br/>',
					 	'default'=>'icon_text'
					 )));	

$form->endForm();

$form->endWrapper('tab');


$form->startWrapper('tab', 'networks');

$form->startForm();

$form->setting(array('type'=>'url',
					 'name'=>'facebook_url',
					 'label'=>__("Facebook", 'cocosocial'),
					 'description'=>__("Entrez l'adresse de votre profil/page Facebook.", 'cocosocial')));

$form->setting(array('type'=>'text',
					 'name'=>'twitter_username',
					 'label'=>__("Twitter", 'cocosocial'),
					 'description'=>__("Entrez votre identifiant Twitter (sans le @).", 'cocosocial')));
					 
$form->setting(array('type'=>'url',
					 'name'=>'googleplus_url',
					 'label'=>__("Google+", 'cocosocial'),
					 'description'=>__("Entrez l'adresse de votre profil/page Google+.", 'cocosocial')));
					 
$form->setting(array('type'=>'url',
					 'name'=>'linkedin_url',
					 'label'=>__("LinkedIn", 'cocosocial'),
					 'description'=>__("Entrez l'adresse de votre profil LinkedIn.", 'cocosocial')));

$form->endForm();

$form->endWrapper('tab');

$form->component('submit', 'submit', array('value'=>__('Sauvegarder les réglages', 'cocosocial')));

$form->render();