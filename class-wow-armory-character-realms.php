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
class WoW_Armory_Character_Realms {
	public $region;

	// Setting these in the object makes things a little easier.
	public $last_checked;
	public $cache_name;

	private $_api_data;

	public function __construct( $region, stdClass $api_data ) {
		$this->region    = $region;
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

	public function get_realms_as_options( $current_realm = null ) {
		$options = '';

		foreach ( $this->realms as $realm ) {
			$options .= '<option value="' . $realm->slug . '"' . selected(
					$current_realm,
					$realm->slug,
					true
				) . '>' . $realm->name . '</option>' . PHP_EOL;
		}

		return $options;
	}
}
