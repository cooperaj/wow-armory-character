<?php
/*
Plugin Name: WoW Armory Character
Plugin URI: http://realmenweardress.es/wow-armory-character/
Description: Pulls a wow character from the new WoW community API and displays it.
Version: 1.1.1
Author: Adam Cooper
Author URI: http://realmenweardress.es
License: GPLv2
*/

/*
Copyright 2014  Adam Cooper  (email : adam@networkpie.co.uk)

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

define( 'TWELVE_HOUR_IN_SECONDS', 12 * HOUR_IN_SECONDS );

include_once( 'class-wow-armory-character-plugin.php' );

// Store the plugin path globally so that it can find itself later.
// Normally the use of __FILE__ would be ok except I develop using
// symbolic links and __FILE__ breaks horribly with symbolic links.
$wacpath = $plugin;

// OO all the way baby.
$wacplugin = new WoW_Armory_Character_Plugin();
add_action( 'init', array( &$wacplugin, 'init' ) );
add_action( 'admin_menu', array( &$wacplugin, 'admin_menu' ) );
add_action( 'admin_init', array( &$wacplugin, 'admin_init' ) );
add_action( 'widgets_init', array( &$wacplugin, 'widget_init' ) );
add_action( 'wp_ajax_admin_ajax_realms', array( &$wacplugin, 'admin_ajax_realms' ) );
add_shortcode( 'armory-character', array( &$wacplugin, 'shortcode' ) );

// These methods need to be defined as static in the class.
register_activation_hook( $wacpath, array( &$wacplugin, 'on_activate' ) );
register_deactivation_hook( $wacpath, array( &$wacplugin, 'on_deactivate' ) );
