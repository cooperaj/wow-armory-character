<?php
/*
Plugin Name: WoW Armory Character
Plugin URI: http://realmenweardress.es/wow-armory-character/
Description: Pulls a wow character from the new WoW community API and displays it.
Version: 0.9
Author: Adam Cooper
Author URI: http://realmenweardress.es
License: GPLv2
*/

/*  
Copyright 2011  Adam Cooper  (email : adam@networkpie.co.uk)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once('class-wow-armory-character-plugin.php');

$wacplugin = new WoW_Armory_Character_Plugin();
add_action('init', array($wacplugin, 'init'));
add_action('admin_menu', array($wacplugin, 'admin_menu'));
add_action('admin_init', array($wacplugin, 'admin_init'));
add_action( 'widgets_init', array($wacplugin, 'widget_init'));
add_shortcode('armory-character', array($wacplugin, 'shortcode'));