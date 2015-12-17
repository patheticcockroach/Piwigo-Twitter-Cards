<?php
/*
Version: 1.0
Plugin Name: TwitterCards
Author: umrysh
Description: Twitter Cards
*/

// Provide your Twitter Username here in order to use Twitter analytics
$twitter_site = ''; // @username

// Check whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('SKELETON_PATH', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');

include_once(SKELETON_PATH . '/' . 'include/events.inc.php');

add_event_handler('picture_pictures_data', 'test_for_gvideo');

?>
