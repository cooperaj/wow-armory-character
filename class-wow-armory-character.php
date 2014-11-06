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
 * extra properties and methods on that data.
 *
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character {
	public $region;
	public $locale;

	// Setting these in the object makes things a little easier.
	public $last_checked;
	public $cache_name;
	public $notes;

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

	public function get_completed_achievement_data() {
		$data = new stdClass();

		if ( ! $this->_has_valid_achievement_data() ) {
			return $data;
		}

		$achiev_data = WoW_Armory_Character_DAL::fetch_achievements( $this->region, $this->locale );

		if ( is_wp_error( $achiev_data ) ) {
			WoW_Armory_Character_DAL::persist_character_note(
				$this,
				__(
					'The global achievement data is corrupt. Please clear the cache.',
					'wow_armory_character'
				)
			);

			return null;
		}

		$data->completed         = count( $achiev_data->get_achievements_countable( $this->achievements->achievementsCompleted ) );
		$data->total             = $achiev_data->get_achievement_count();
		$data->percent_complete  = round( ( $data->completed / $data->total ) * 100 );
		$data->percent_remaining = 100 - $data->percent_complete;

		return $data;
	}

	public function get_latest_achievements( $no_to_fetch ) {
		$achievs = array();
		$count   = 0;

		if ( ! $this->_has_valid_achievement_data() ) {
			return $achievs;
		}

		$achiev_data = WoW_Armory_Character_DAL::fetch_achievements( $this->region, $this->locale );

		if ( is_wp_error( $achiev_data ) ) {
			WoW_Armory_Character_DAL::persist_character_note(
				$this,
				__(
					'The global achievement data is corrupt. Please clear the cache.',
					'wow_armory_character'
				)
			);
		} else {
			arsort( $this->achievements->achievementsCompletedTimestamp );
			foreach ( $this->achievements->achievementsCompletedTimestamp as $key => $timestamp ) {
				if ( $count >= $no_to_fetch ) {
					break;
				}

				$ach = $achiev_data->get_achievement_by_id( $this->achievements->achievementsCompleted[ $key ] );

				// Our achievement data may not contain what we need so we skip achievements that don't get
				// returned correctly. Come on Blizz...
				if ( ! is_null( $ach ) ) {
					$ach->completed = $timestamp;
					$achievs[]      = $ach;

					$count ++;
				} else {
					WoW_Armory_Character_DAL::persist_character_note(
						$this,
						__(
							'The achievement data does not contain a match for achievement id ' .
							$this->achievements->achievementsCompleted[ $key ] . '.',
							'wow_armory_character'
						)
					);
				}
			}
		}

		return $achievs;
	}

	public function get_activity_feed_items( $max_items = 5 ) {
		// We want five of each item so this slightly confusing thing keeps track of
		// what we've added to the return to ensure this happens.
		$feed_items     = array();
		$item_count     = 0;
		$achiev_count   = 0;
		$criteria_count = 0;

		foreach ( $this->feed as $feed_item ) {
			$item = new WoW_Armory_Character_FeedItem( $this->region, $this->locale, $feed_item );

			switch ( $item->type ) {
				case 'ACHIEVEMENT' :
					if ( $achiev_count < $max_items ) {
						$feed_items[] = $item;
					}
					$achiev_count ++;
					break;
				case 'CRITERIA' :
					if ( $criteria_count < $max_items ) {
						$feed_items[] = $item;
					}
					$criteria_count ++;
					break;
				case 'LOOT' :
					if ( $item_count < $max_items ) {
						$feed_items[] = $item;
					}
					$item_count ++;
					break;
			}

		}

		return $feed_items;
	}

	/**
	 * Return whether or not our achievement data is valid.
	 *
	 * It appears that sometimes the api will return incorrect data for the achievements of a
	 * character. We now make sure to check before attempting any procession on that data.
	 * @return boolean
	 */
	private function _has_valid_achievement_data() {
		if ( is_array( $this->achievements->achievementsCompleted ) &&
		     is_array( $this->achievements->achievementsCompletedTimestamp )
		) {
			return true;
		}

		WoW_Armory_Character_DAL::persist_character_note(
			$this,
			__(
				'The achievement data for this character is not fully formed.',
				'wow_armory_character'
			)
		);

		return false;
	}
}
