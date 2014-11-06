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
class WoW_Armory_Character_Achievements {
	public $region;
	public $locale;

	// Setting these in the object makes things a little easier.
	public $last_checked;
	public $cache_name;

	private $_totalAchievable;
	private $_flat_fos;
	private $_flat_legacy;

	private $_api_data;
	private $_flat_api_data;

	public function __construct( $region, $locale, stdClass $api_data ) {
		$this->region    = $region;
		$this->locale    = $locale;
		$this->_api_data = $api_data;

		$this->_flatten_achievement_data();
	}

	public function __get( $name ) {
		if ( isset( $this->_api_data->$name ) ) {
			return $this->_api_data->$name;
		}
	}

	public function __isset( $name ) {
		return isset( $this->_api_data->$name );
	}

	/**
	 * Takes a passed in array of achievement ID's like that returned as part of a character query and remove the
	 * feats of strength from it as they don't count.
	 *
	 * @param $achievements array An array of achievement IDs.
	 *
	 * @return array
	 */
	public function get_achievements_countable( $achievements ) {
		// remove FoS from count.
		$ret = array_diff( $achievements, array_keys( $this->_flat_fos ) );

		return $ret;
	}

	/**
	 * Returns the count of the earnable achievements.
	 *
	 * Because some of the achievements are no longer obtainable (feats of strength)
	 * or are duplicated because they are different for horde/alliance we need to
	 * calculate a count that will remove these from consideration.
	 *
	 * @todo Figure out why this produces a value that is 6 off the number given on the armory.
	 *
	 * @return int The number of achievements available to earn.
	 */
	public function get_achievement_count() {
		return $this->_totalAchievable[0] + $this->_totalAchievable[2]; // 0 and 1 are the two factions. They're both equal
	}

	/**
	 * Searches the achievement data structure for a specific ID and returns
	 * any information found.
	 *
	 * @param int $achiev_id
	 *
	 * @return stdClass|null A data object describing the achievement discovered or null otherwise.
	 */
	public function get_achievement_by_id( $achiev_id ) {
		if ( isset( $this->_flat_api_data[ $achiev_id ] ) ) {
			return $this->_flat_api_data[ $achiev_id ];
		}

		// Achievement data was not found.
		return null;
	}

	/**
	 * Flattens the hierarchical achievement data returned via the api
	 * into an array keyed on the achievement id. It adds new data to each
	 * achievement so that it's section/category can still be used.
	 */
	private function _flatten_achievement_data() {
		$this->_flat_api_data   = array();
		$this->_flat_fos        = array();
		$this->_flat_legacy     = array();
		$this->_totalAchievable = array();

		foreach ( $this->_api_data->achievements as $section ) {
			$is_fos    = ( $section->id == 81 ) ? true : false; // Feats of Strength
			$is_legacy = ( $section->id == 15234 ) ? true : false; // Legacy

			if ( isset( $section->categories ) && is_array( $section->categories ) ) {
				foreach ( $section->categories as $category ) {
					foreach ( $category->achievements as $ach ) {
						$ach->section       = new stdClass();
						$ach->section->id   = $section->id;
						$ach->section->name = $section->name;

						$ach->category       = new stdClass();
						$ach->category->id   = $category->id;
						$ach->category->name = $category->name;

						$this->_flat_api_data[ $ach->id ] = $ach;
						if ( $is_fos ) {
							$this->_flat_fos[ $ach->id ] = $ach;
						}
						if ( $is_legacy ) {
							$this->_flat_legacy[ $ach->id ] = $ach;
						}

						if ( ! $is_fos && ! $is_legacy ) {
							if ( ! isset( $this->_totalAchievable[ $ach->factionId ] ) ) {
								$this->_totalAchievable[ $ach->factionId ] = 0;
							}

							$this->_totalAchievable[ $ach->factionId ] ++;
						}
					}
				}
			}

			foreach ( $section->achievements as $ach ) {
				$ach->section       = new stdClass();
				$ach->section->id   = $section->id;
				$ach->section->name = $section->name;

				$ach->category       = new stdClass();
				$ach->category->id   = null;
				$ach->category->name = null;

				$this->_flat_api_data[ $ach->id ] = $ach;
				if ( $is_fos ) {
					$this->_flat_fos[ $ach->id ] = $ach;
				}
				if ( $is_legacy ) {
					$this->_flat_legacy[ $ach->id ] = $ach;
				}

				if ( ! $is_fos && ! $is_legacy ) {
					if ( ! isset( $this->_totalAchievable[ $ach->factionId ] ) ) {
						$this->_totalAchievable[ $ach->factionId ] = 0;
					}

					$this->_totalAchievable[ $ach->factionId ] ++;
				}
			}
		}
	}
}
