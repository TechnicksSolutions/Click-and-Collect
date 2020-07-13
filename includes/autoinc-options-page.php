<?php
function cac_register_options_page()
{
	//Add to settings menu
	//add_options_page('Page Title', 'Plugin Menu', 'manage_options', 'myplugin', 'myplugin_options_page');
	// Add to admin_menu function
	global $current_user;
	get_currentuserinfo();
	$email = (string) $current_user->user_email;
	if($email=='edward@technicks.com') {
		//add_menu_page(__('Click and Collect Menu'), __('Click and Collect Options'), 'manage_options', 'cac', 'cac_options_page', 'dashicons-update', 2);
	}


}

//add_action('admin_menu', 'cac_register_options_page');

function cac_options_page() {
	//setBranchStockStatus('yes');
	//setDummyStockLvls();
	echo 'Finished.';
}