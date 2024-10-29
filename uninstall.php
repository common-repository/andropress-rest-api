<?php
/**
*This file is triggered on plugin uninstall
*
*@package andropress-rest-api
*/

if(!defined('WP_UNINSTALL_PLUGIN')){
	die;
}

//drop the plugin table
global $wpdb;
$table_name = $wpdb->prefix.'andropress_rest_api';
$sql = "DROP TABLE IF EXISTS ".$table_name.";";
$wpdb->query($sql);
