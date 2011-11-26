<?php
/**
@Plugin Name:Test
@Plugin URL:http://freetosu.berlios.de
@Description:4test
@Author: FTS Team
@Author URL: http://www.freetosu.berlios.de
@version: 0.1
**/
function test() {
add_admin_plugin_page('test_admin','ForTest');
}
add_action('plugins_admin_menu','test');
function test_admin() {
	create_variable('fortest_action','fortest_action', 'showoptions');
	global $fortest_action;
	if($fortest_action == 'showoptions') {
		$fortest_option = @get('fortest_option');
		_option_page_start("fortest_action",'save','test_admin');
		heading('test');
		tr("Option","<input type='text' size='45' name=fortest_option value='" . ($fortest_option ? $fortest_option : "")."'> Test.\n", 1);
		_option_page_end('Save');
	}elseif($fortest_action == 'save') {
	_save("fortest_option",'fortest_option');
    _redir('test_admin&success');
}
}
?>