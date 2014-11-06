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
 * Wraps the JSON returned by the WoW community API to provide some
 * extra properties and methods on that data. Since a feed item can
 * consist of 3 different datatypes these methods homogenise the data
 * into the properties we'll eventually want to display.
 *
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character_FeedItem {
	const ITEM_ACHIEVEMENT = 'ACHIEVEMENT';
	const ITEM_CRITERIA = 'CRITERIA';
	const ITEM_LOOT = 'LOOT';

	public $region;
	public $locale;

	// Setting these in the object makes things a little easier.
	public $last_checked;
	public $cache_name;

	private $_api_data;

	public function __construct( $region, $locale, stdClass $api_data ) {
		$this->region    = $region;
		$this->locale    = $locale;
		$this->_api_data = $api_data;
	}

	public function __get( $name ) {
		if ( isset( $this->_api_data->$name ) ) {
			return $this->_api_data->$name;
		}
	}

	public function __isset( $name ) {
		return isset( $this->_api_data->$name );
	}

	public function get_item_title() {
		switch ( $this->type ) {
			case self::ITEM_ACHIEVEMENT :
				return $this->achievement->title;
				break;
			case self::ITEM_CRITERIA :
				return $this->criteria->description;
				break;
			case self::ITEM_LOOT :
				$item = WoW_Armory_Character_DAL::fetch_item( $this->region, $this->locale, $this->itemId );

				return $item->name;
				break;
		}
	}

	public function get_item_url_components() {
		switch ( $this->type ) {
			case self::ITEM_ACHIEVEMENT :
			case self::ITEM_CRITERIA :
				$achievements = WoW_Armory_Character_DAL::fetch_achievements( $this->region, $this->locale );
				if ( is_wp_error( $achievements ) ) {
					return null;
				}

				$achiev     = $achievements->get_achievement_by_id( $this->achievement->id );
				$components = array(
					'id'       => $achiev->id,
					'section'  => $achiev->section->id,
					'category' => $achiev->category->id
				);

				return $components;
				break;
			case self::ITEM_LOOT :
				return array( 'id' => $this->itemId );
				break;
		}
	}

	public function get_item_icon() {
		switch ( $this->type ) {
			case self::ITEM_ACHIEVEMENT :
				return $this->achievement->icon;
				break;
			case self::ITEM_CRITERIA :
				break;
			case self::ITEM_LOOT :
				$item = WoW_Armory_Character_DAL::fetch_item( $this->region, $this->locale, $this->itemId );

				return $item->icon;
				break;
		}
	}

	public function get_item_related() {
		switch ( $this->type ) {
			case self::ITEM_ACHIEVEMENT :
				return $this->achievement->points;
				break;
			case self::ITEM_CRITERIA :
				return $this->achievement->title;
				break;
			case self::ITEM_LOOT :
				break;
		}
	}
}
