<?php
/*
Version: 1.0
Plugin Name: TwitterCards
Author: umrysh
Description: Twitter Cards
*/

// Check whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

include_once(PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/' . 'include/events.inc.php');

add_event_handler('picture_pictures_data', 'test_for_gvideo');

?>