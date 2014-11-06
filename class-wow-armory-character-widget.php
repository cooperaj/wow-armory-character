<?php
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

/**
 * Provides the wordpress widget.
 *
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character_Widget extends WP_Widget {
	protected $_default_options;

	public function __construct() {
		$this->_default_options = array(
			'name'          => '',
			'realm'         => '',
			'region'        => 'EU',
			'show_portrait' => 1,
			'show_title'    => 1,
			'show_talents'  => 1,
			'show_items'    => 1,
			'show_profs'    => WoW_Armory_Character_Plugin::STYLE_PROF_BADGES,
			'show_achievs'  => WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR | WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST,
			'show_feed'     => WoW_Armory_Character_Plugin::STYLE_FEED_ITEMS | WoW_Armory_Character_Plugin::STYLE_FEED_ACHIEVEMENTS |
			                   WoW_Armory_Character_Plugin::STYLE_FEED_ICONS,
			'locale'        => 'en_GB',
			'title'         => __( 'Armory for %NAME%', 'wow_armory_character' ),
		);

		$widget_ops = array(
			'classname'   => 'WoW_Armory_Character_Widget',
			'description' => __( "Displays a World of Warcraft character's information", 'wow_armory_character' )
		);
		parent::__construct(
			'wow-armory-character-widget',
			__( 'WoW Armory Character', 'wow_armory_character' ),
			$widget_ops
		);
	}

	public function form( $instance ) {
		global $wacpath;

		$instance = wp_parse_args( (array) $instance, $this->_default_options );

		include( plugin_dir_path( $wacpath ) . '/view-wow-armory-character-widget.php' );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['name']          = strip_tags( stripslashes( $new_instance['name'] ) );
		$instance['realm']         = strip_tags( stripslashes( $new_instance['realm'] ) );
		$instance['region']        = strip_tags( stripslashes( $new_instance['region'] ) );
		$instance['show_portrait'] = strip_tags( stripslashes( $new_instance['show_portrait'] ) );
		$instance['show_title']    = strip_tags( stripslashes( $new_instance['show_title'] ) );
		$instance['show_talents']  = strip_tags( stripslashes( $new_instance['show_talents'] ) );
		$instance['show_items']    = strip_tags( stripslashes( $new_instance['show_items'] ) );
		$instance['locale']        = strip_tags( stripslashes( $new_instance['locale'] ) );
		$instance['title']         = strip_tags( stripslashes( $new_instance['title'] ) );

		// We ignore the setting of 'show_profs' and parse the bitwise setting instead. Having
		// bitwise settings checked implies you want the master setting configured.
		$prof_config = null;
		if ( $new_instance['show_profs_badges'] ) {
			$prof_config = $prof_config | WoW_Armory_Character_Plugin::STYLE_PROF_BADGES;
		}
		if ( $new_instance['show_profs_secondary'] ) {
			$prof_config = $prof_config | WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY;
		}

		$ach_config = null;
		if ( $new_instance['show_achievs_bar'] ) {
			$ach_config = $ach_config | WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR;
		}
		if ( $new_instance['show_achievs_list'] ) {
			$ach_config = $ach_config | WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST;
		}
		if ( $new_instance['show_achievs_list_desc'] ) {
			$ach_config = $ach_config | WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC;
		}

		$feed_config = null;
		if ( $new_instance['show_feed_items'] ) {
			$feed_config = $feed_config | WoW_Armory_Character_Plugin::STYLE_FEED_ITEMS;
		}
		if ( $new_instance['show_feed_achievs'] ) {
			$feed_config = $feed_config | WoW_Armory_Character_Plugin::STYLE_FEED_ACHIEVEMENTS;
		}
		if ( $new_instance['show_feed_criteria'] ) {
			$feed_config = $feed_config | WoW_Armory_Character_Plugin::STYLE_FEED_CRITERIA;
		}
		if ( $new_instance['show_feed_icons'] ) {
			$feed_config = $feed_config | WoW_Armory_Character_Plugin::STYLE_FEED_ICONS;
		}

		// If no settings are configured but the master or useless setting is ticked then set the defaults.
		if ( ( $new_instance['show_profs'] && $prof_config == null ) ||
		     $prof_config == WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY
		) {
			$prof_config = $this->_default_options['show_profs'];
		}
		$instance['show_profs'] = $prof_config;

		if ( ( $new_instance['show_achievs'] && $ach_config == null ) ||
		     $ach_config == WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC
		) {
			$ach_config = $this->_default_options['show_achievs'];
		}
		$instance['show_achievs'] = $ach_config;

		if ( ( $new_instance['show_feed'] && $feed_config == null ) ||
		     $feed_config == WoW_Armory_Character_Plugin::STYLE_FEED_ICONS
		) {
			$feed_config = $this->_default_options['feed_config'];
		}
		$instance['show_feed'] = $feed_config;

		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;

		$title = apply_filters( 'widget_title', str_replace( '%NAME%', $instance['name'], $instance['title'] ) );
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		$char = WoW_Armory_Character_DAL::fetch_character(
			$instance['region'],
			$instance['locale'],
			$instance['realm'],
			$instance['name']
		);

		if ( ! is_wp_error( $char ) ) {
			$view = new WoW_Armory_Character_View( $char );
			echo $view->display_character( $instance );
		} else {
			// Show the error message.
			echo $char->get_error_message();
		}

		echo $after_widget;
	}

	/**
	 * @param $region The region for which to fetch a list of realms.
	 * @param $realm The currently selected realm (if any).
	 *
	 * @return string A string of html option elements to display within a select element.
	 */
	public function get_realms_options( $region, $current_realm = null ) {
		$realms_for_region = WoW_Armory_Character_DAL::fetch_realms( $region );

		if ( ! $realms_for_region instanceof WP_Error ) {
			return $realms_for_region->get_realms_as_options( $current_realm );
		}
	}
}
