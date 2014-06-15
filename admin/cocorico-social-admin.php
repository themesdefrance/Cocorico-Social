<?php

////////////////////////////////////
// Cocorico Framework
////////////////////////////////////

require_once 'Cocorico/Cocorico.php';

$form = new Cocorico('cocosocial_');

$form->startWrapper('titre');
$form->component('raw', __('Options Cocorico Social', 'cocosocial'));
$form->endWrapper('titre');

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
					 	'after'=>'<br/>',
					 	'default'=>array('facebook','twitter','googleplus','linkedin')
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

$form->component('submit', 'submit', array('value'=>__('Sauvegarder les réglages', 'cocosocial')));

$form->render();