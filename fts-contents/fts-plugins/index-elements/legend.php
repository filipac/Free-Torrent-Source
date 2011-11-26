<?php
/**
@Plugin Name:Index Element - Show the class legend
@Plugin URL:http://freetosu.berlios.de
@Description:Every class has an specific username style. This plugins is meant to show an legend with all classes styles.
@Author: FTS Team
@Author URL: http://www.freetosu.berlios.de
@version: 0.1
**/
function _index_element_legend() {
	echo _br;
	insert_legend(0,1);
}
add_action("index_elements_aftershout","_index_element_legend");
?>