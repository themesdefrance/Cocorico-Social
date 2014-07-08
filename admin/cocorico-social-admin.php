<?php

require_once('functions.php');

$all_post_types = coco_get_all_post_types();

////////////////////////////////////
// Cocorico Framework
////////////////////////////////////

$form = new Cocorico('cocosocial_');

$form->startWrapper('titre');
$form->component('raw', __('Cocorico Social Settings', 'cocosocial'));
$form->endWrapper('titre');

$form->groupHeader(array('general'=>__('General', 'cocosocial'), 
						 'networks'=>__('Networks', 'cocosocial')));

$form->startWrapper('tab', 'general');

$form->startForm();

$form->setting(array('type'=>'checkbox',
					 'label'=>__('Buttons location', 'cocosocial'),
					 'name'=>'location',
					 'checkboxes'=>array(
					 	 'top'=>__('Top of content', 'cocosocial'),
						 'bottom'=>__('Bottom of content', 'cocosocial')
					 ),
					 'options'=>array(
					 	'after'=>'<br/>'
					 ),
					 'description'=>__("Choose where to insert share buttons in content.", 'cocosocial')
					 ));

$form->setting(array('type'=>'checkbox',
					 'label'=>__('Display buttons in', 'cocosocial'),
					 'name'=>'pt_presence',
					 'checkboxes'=> $all_post_types,
					 'options'=>array(
					 	'after'=>'<br/>'
					 ),
					 'description'=>__("Select the post types where buttons should be displayed.", 'cocosocial')
					 ));

$form->orderedList('networks_blocks',
				__('Networks buttons (order them by drag & drop)', 'cocosocial'),
				array(   'facebook'=>__('Facebook', 'cocosocial'),
						 'twitter'=>__('Twitter', 'cocosocial'),
						 'googleplus'=>__('Google+', 'cocosocial'),
						 'linkedin'=>__('LinkedIn', 'cocosocial'),
						 'viadeo'=>__('Viadeo', 'cocosocial'),
						 'pinterest'=>__('Pinterest', 'cocosocial'),
						 'email'=>__('Email', 'cocosocial')));
						

$form->setting(array('type'=>'radio',
					 'label'=>__('Buttons\' format', 'cocosocial'),
					 'name'=>'format',
					 'radios'=>array(
					 	 'icon_text'=>__('Text and icons', 'cocosocial'),
						 'icon_only'=>__('Icon only', 'cocosocial'),
						 'text_only'=>__('Text only', 'cocosocial')
					 ),
					 'options'=>array(
					 	'after'=>'<br/>',
					 	'default'=>'icon_text'
					 ),
					 'description'=>__('Choose the format that makes you feel good.', 'cocosocial')
					 ));
					 
$form->setting(array('type'=>'radio',
					 'label'=>__('Buttons\' width', 'cocosocial'),
					 'name'=>'width',
					 'radios'=>array(
					 	 'fitted_width'=>__('Fill out the content width.', 'cocosocial'),
						 'auto_width'=>__('Auto width', 'cocosocial'),
						 'big_first'=>__('Big first button *','cocosocial')
					 ),
					 'options'=>array(
					 	'after'=>'<br/>',
					 	'default'=>'fitted_width'
					 ),
					 'description'=>__("Choose your best button's width.<br>* : If you select this, button's format will be ignored (give it a try it's awesome !).", 'cocosocial')
					 ));
					 
					 
$form->setting(array('type'=>'text',
					 'name'=> 'bottom_message',
					 'label'=>__("Share incentive", 'cocosocial'),
					 'description'=>__("If you use bottom share buttons, this text will be displayed before the buttons to motivate visitors clicks. E.g : Share this.", 'cocosocial')
					 ));

$form->endForm();

$form->endWrapper('tab');


$form->startWrapper('tab', 'networks');

$form->startForm();

$form->setting(array('type'=>'url',
					 'name'=>'facebook_url',
					 'label'=>__("Facebook", 'cocosocial'),
					 'description'=>__("Add your Facebook profile/page link.", 'cocosocial')));

$form->setting(array('type'=>'text',
					 'name'=>'twitter_username',
					 'label'=>__("Twitter", 'cocosocial'),
					 'description'=>__("Add your Twitter username (without @).", 'cocosocial')));
					 
$form->setting(array('type'=>'url',
					 'name'=>'googleplus_url',
					 'label'=>__("Google+", 'cocosocial'),
					 'description'=>__("Add your Google+ profile/page link.", 'cocosocial')));
					 
$form->setting(array('type'=>'url',
					 'name'=>'linkedin_url',
					 'label'=>__("LinkedIn", 'cocosocial'),
					 'description'=>__("Add your LinkedIn profile link.", 'cocosocial')));
					 
$form->setting(array('type'=>'url',
					 'name'=>'viadeo_url',
					 'label'=>__("Viadeo", 'cocosocial'),
					 'description'=>__("Add your Viadeo profile link.", 'cocosocial')));
					 
$form->setting(array('type'=>'url',
					 'name'=>'pinterest_url',
					 'label'=>__("Pinterest", 'cocosocial'),
					 'description'=>__("Add your Pinterest profile link.", 'cocosocial')));

$form->endForm();

$form->endWrapper('tab');

$form->component('submit', 'submit', array('value'=>__('Save Changes', 'cocosocial')));

$form->render();

?>

<div style="margin-top:20px;">

<?php _e('Cocorico Social is brought to you by','cocosocial'); ?> <a href="https://www.themesdefrance.fr/?utm_source=plugin&utm_medium=link&utm_campaign=cocoricosocial" target="_blank">Themes de France</a> - <?php _e('If you found this plugin useful','cocosocial'); ?>, <a href="" target="_blank"><?php _e('give it 5 &#9733; on WordPress.org','cocosocial'); ?></a>

</div>



