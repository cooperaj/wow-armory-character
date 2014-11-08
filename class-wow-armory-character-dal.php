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

require_once( ABSPATH . WPINC . '/class-http.php' );

/**
 * Provide an accessor/factory layer onto the WoW community API.
 *
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character_DAL {
	/**
	 * The API url to retrieve a character populated with Guild, Equipped Items, Profession and Talent information.
	 * @var string
	 */
	const CHARACTER_URL = 'http://%s.battle.net/api/wow/character/%s/%s?fields=guild,items,professions,talents,titles,achievements,feed&locale=%s';

	const REALM_URL = 'http://%s.battle.net/api/wow/realm/status';
	const RACE_URL = 'http://%s.battle.net/api/wow/data/character/races?locale=%s';
	const CLASS_URL = 'http://%s.battle.net/api/wow/data/character/classes?locale=%s';
	const ACHIEV_URL = 'http://%s.battle.net/api/wow/data/character/achievements?locale=%s';
	const ITEM_URL = 'http://%s.battle.net/api/wow/item/%s?locale=%s';

	const REQUEST_TIMEOUT = 5;

	static function fetch_all_cached_characters() {
		$chars = array();

		global $wpdb;
		$options = $wpdb->get_results(
			"SELECT * FROM " . $wpdb->prefix . "options WHERE `option_name` LIKE '_transient_wowcharcache%'"
		);

		foreach ( $options as $option ) {
			$data     = get_option( $option->option_name );
			$api_data = json_decode( $data['api_data'] );

			if ( $api_data != null ) {
				$char = self::fetch_character( $data['region'], $data['locale'], $api_data->realm, $api_data->name );
			} else {
				$char = new WP_Error( 500, 'Invalid character cache' );
			}

			$char->last_checked = $data['last_checked'];
			$char->cache_name   = $option->option_name;
			$char->notes        = $data['notes'];

			if ( $api_data == null ) {
				$char->notes[] = 'There has been an error whilst retrieving this record from the cache. Please clear it and try again.';
			}

			$chars[] = $char;
		}

		return $chars;
	}

	static function clear_all_cache() {
		global $wpdb;
		$wpdb->query(
			"
			DELETE FROM $wpdb->options
			WHERE `option_name` LIKE '_transient_wow%'
			OR `option_name` LIKE '_transient_timeout_wow%'
			"
		);
	}

	/**
	 * Fetch a WoW character from the Community API.
	 *
	 * This attempts to fetch a WoW character from the community API utilising a cache to limit data requests.
	 * By default it will attempt to cache character information for 12 hours - limiting the number of requests
	 * to the API to 2 in any 24hr period.
	 *
	 * @param string $region The region to fetch from.
	 * @param string $locale The locale in which to return the result.
	 * @param string $realm The realm of the character to fetch.
	 * @param string $name The character name.
	 * @param int $expires_after A value in seconds for which the character should be retrieved from the cache.
	 *
	 * @return WP_Error|WoW_Armory_Character A completed character or an error if the retrieval was unsuccessful.
	 */
	static function fetch_character( $region, $locale, $realm, $name, $expires_after = TWELVE_HOUR_IN_SECONDS ) {
		$char_api_data_obj = self::_fetch_character( $region, $locale, $realm, $name, $expires_after );
		if ( is_wp_error( $char_api_data_obj ) ) {
			return $char_api_data_obj;
		}

		$char_api_data_obj->race = self::_fetch_race( $region, $locale, $char_api_data_obj->race );
		if ( is_wp_error( $char_api_data_obj->race ) ) {
			return $char_api_data_obj->race;
		}

		// We need the english locale too since all images are named in english
		$char_api_data_obj->en_race = self::_fetch_race( $region, 'en_GB', $char_api_data_obj->race->id );
		if ( is_wp_error( $char_api_data_obj->en_race ) ) {
			return $char_api_data_obj->en_race;
		}

		$char_api_data_obj->class = self::_fetch_class( $region, $locale, $char_api_data_obj->class );
		if ( is_wp_error( $char_api_data_obj->class ) ) {
			return $char_api_data_obj->class;
		}

		// We need the english locale too since all images are named in english
		$char_api_data_obj->en_class = self::_fetch_class( $region, 'en_GB', $char_api_data_obj->class->id );
		if ( is_wp_error( $char_api_data_obj->en_class ) ) {
			return $char_api_data_obj->en_class;
		}

		return new WoW_Armory_Character( $region, $locale, $char_api_data_obj );
	}

	/**
	 * Fetches achievement information from the community API
	 *
	 * @param string $region The region to fetch from.
	 * @param string $locale The locale in which to return the result.
	 * @param int $expires_after The expiry time for this lookup cache. Defaults to 1 week.
	 *
	 * @return WP_Error|WoW_Armory_Character_Achievements Returns either an error if the API request failed or a class encapsulating the response.
	 */
	static function fetch_achievements( $region, $locale, $expires_after = WEEK_IN_SECONDS ) {
		if ( false !== ( $cached_achievs = get_transient( 'wowachcache-' . $region . '-' . $locale ) ) ) {
			return $cached_achievs['api_data'];
		} else {
			$http_request = new WP_Http();
			$http_result  = $http_request->request(
				self::_encode_url( sprintf( self::ACHIEV_URL, strtolower( $region ), $locale ) )
			);

			if ( ! is_wp_error( $http_result ) && $http_result['response']['code'] == 200 ) {
				$achievs_json = json_decode( $http_result['body'] );
				if ( $achievs_json == null ) {
					return new WP_Error( 500, __(
						'Unable to fetch data from battle.net for character achievements',
						'wow_armory_character'
					) );
				}

				$achievs_data                 = array();
				$achievs_data['last_checked'] = time();
				$achievs_data['locale']       = $locale;

				// We cache the completed object rather then just the raw data as we do a fairly intensive bit
				// of processing on it that we don't want to keep repeating.
				$achievs_data['api_data'] = new WoW_Armory_Character_Achievements( $region, $locale, $achievs_json );

				set_transient( 'wowachcache-' . $region . '-' . $locale, $achievs_data, $expires_after );

				return $achievs_data['api_data'];
			} else {
				return new WP_Error( 500, __(
					'Unable to fetch data from battle.net for character achievements',
					'wow_armory_character'
				) );
			}
		}
	}

	/**
	 * Fetches achievement information from the community API
	 *
	 * @param string $region The region to fetch from.
	 * @param string $locale The locale in which to return the result.
	 * @param int $id The id of the item to fetch.
	 * @param int $expires_after The expiry time for this lookup cache. Defaults to 1 week.
	 *
	 * @return WP_Error|WoW_Armory_Character_Item Returns either an error if the API request failed or a class encapsulating the response.
	 */
	static function fetch_item( $region, $locale, $id, $expires_after = WEEK_IN_SECONDS ) {
		if ( false !== ( $cached_item = get_transient( 'wowitemcache-' . $region . '-' . $locale . '-' . $id ) ) ) {
			$item_json = json_decode( $cached_item['api_data'] );
		} else {
			$http_request = new WP_Http();
			$http_result  = $http_request->request(
				self::_encode_url( sprintf( self::ITEM_URL, strtolower( $region ), $id, $locale ) )
			);

			if ( ! is_wp_error( $http_result ) && $http_result['response']['code'] == 200 ) {
				$item_json = json_decode( $http_result['body'] );
				if ( $item_json == null ) {
					return new WP_Error( 500, __(
						'Unable to fetch data from battle.net for item',
						'wow_armory_character'
					) );
				}

				$item_data                 = array();
				$item_data['last_checked'] = time();
				$item_data['locale']       = $locale;
				$item_data['api_data']     = $http_result['body'];

				set_transient( 'wowitemcache-' . $region . '-' . $locale . '-' . $id, $item_data, $expires_after );
			} else {
				return new WP_Error( 500,
					__( 'Unable to fetch data from battle.net for item', 'wow_armory_character' ) );
			}
		}

		return new WoW_Armory_Character_Item( $region, $locale, $item_json );
	}

	/**
	 * Fetches realm information from the community API
	 *
	 * @param string $region The region to fetch from.
	 * @param int $expires_after The expiry time for this lookup cache. Defaults to 1 week.
	 *
	 * @return WP_Error|WoW_Armory_Character_Realms Returns either an error if the API request failed or a class encapsulating the response.
	 */
	static function fetch_realms( $region, $expires_after = WEEK_IN_SECONDS ) {
		if ( false !== ( $cached_realms = get_transient( 'wowrealmcache-' . $region ) ) ) {
			$realms_json = json_decode( $cached_realms['api_data'] );
		} else {
			$http_request = new WP_Http();
			$http_result  = $http_request->request(
				self::_encode_url( sprintf( self::REALM_URL, strtolower( $region ) ) ),
				array( 'timeout' => self::REQUEST_TIMEOUT )
			);

			if ( ! is_wp_error( $http_result ) && $http_result['response']['code'] == 200 ) {
				$realms_json = json_decode( $http_result['body'] );
				if ( $realms_json == null ) {
					return new WP_Error( 500, __(
						'Unable to fetch data from battle.net for realms',
						'wow_armory_character'
					) );
				}

				$realms_data                 = array();
				$realms_data['last_checked'] = time();
				$realms_data['api_data']     = $http_result['body'];

				set_transient( 'wowrealmcache-' . $region, $realms_data, $expires_after );
			} else {
				return new WP_Error( 500,
					__( 'Unable to fetch data from battle.net for realms', 'wow_armory_character' ) );
			}
		}

		return new WoW_Armory_Character_Realms( $region, $realms_json );
	}

	/**
	 * Attaches a note to a character for display in the admin page.
	 *
	 * @param $character WoW_Armory_Character The character to attach the note to.
	 * @param string $note The note text.
	 * @param int $expires_after An expiry date for the character (not the note, the whole character)
	 */
	static function persist_character_note( $character, $note, $expires_after = TWELVE_HOUR_IN_SECONDS ) {
		$char_data = get_transient(
			'wowcharcache-' . md5(
				$character->name . '-' . $character->realm . '-' . $character->region . '-' . $character->locale
			)
		);
		if ( $char_data !== false && ! in_array( $note, $char_data['notes'] ) ) {
			$char_data['notes'][] = $note;
			set_transient(
				'wowcharcache-' . md5(
					$character->name . '-' . $character->realm . '-' . $character->region . '-' . $character->locale
				),
				$char_data,
				$expires_after
			);
		}
	}

	/**
	 * Fetch a WoW character from the Community API.
	 *
	 * This attempts to fetch a WoW character from the community API utilising a cache to limit data requests.
	 * By default it will attempt to cache character information for 12 hours - limiting the number of requests
	 * to the API to 2 in any 24hr period.
	 *
	 * @param string $region The region to fetch from.
	 * @param string $locale The locale in which to return the result.
	 * @param string $realm The realm of the character to fetch.
	 * @param string $name The character name.
	 * @param int $expires_after A value in seconds for which the character should be retrieved from the cache. Defaults to 12 hours.
	 *
	 * @return stdClass|WP_Error A stdClass data object containing character information or an error if the retrieval was unsuccessful.
	 */
	private function _fetch_character( $region, $locale, $realm, $name, $expires_after = TWELVE_HOUR_IN_SECONDS ) {
		// Try to fetch from the cache.
		if ( false !== ( $cached_char = get_transient(
				'wowcharcache-' . md5( $name . '-' . $realm . '-' . $region . '-' . $locale )
			) )
		) {
			// Cached available and within the expiry time.
			return json_decode( $cached_char['api_data'] );
		} else {
			$http_request = new WP_Http();
			$http_result  = $http_request->request(
				self::_encode_url( sprintf( self::CHARACTER_URL, strtolower( $region ), $realm, $name, $locale ) )
			);

			if ( ! is_wp_error( $http_result ) && $http_result['response']['code'] == 200 ) {
				$char_json = json_decode( $http_result['body'] );
				if ( $char_json == null ) {
					return new WP_Error( 500, __(
						'Unable to fetch data from battle.net for character',
						'wow_armory_character'
					) );
				}

				$char_data                 = array();
				$char_data['last_checked'] = time();
				$char_data['region']       = $region;
				$char_data['locale']       = $locale;
				$char_data['api_data']     = $http_result['body'];
				$char_data['notes']        = array();

				// Cache the result so we don't have to keep fetching this.
				set_transient(
					'wowcharcache-' . md5( $name . '-' . $char_json->realm . '-' . $region . '-' . $locale ),
					$char_data,
					$expires_after
				);

				return $char_json;
			}

			// If we get here then it means the character does not exist in the cache and the API interrogation failed.
			return new WP_Error( 500,
				__( 'Unable to fetch data from battle.net for character', 'wow_armory_character' ) );
		}
	}

	/**
	 * Fetches race information from the community API and returns the race definition for the passed in race ID.
	 *
	 * @param string $region The region to fetch from.
	 * @param string $locale The locale in which to return the result.
	 * @param int $race_id The race ID assigned to a character API request.
	 * @param int $expires_after The expiry time for this lookup cache. Defaults to 1 week.
	 *
	 * @return WP_Error|stdClass Returns either an error if the API request failed or a stdClass encapsulating the response.
	 */
	private function _fetch_race( $region, $locale, $race_id, $expires_after = WEEK_IN_SECONDS ) {
		if ( false !== ( $cached_races = get_transient( 'wowracecache-' . $region . '-' . $locale ) ) ) {
			$races_json = json_decode( $cached_races['api_data'] );
		} else {
			$http_request = new WP_Http();
			$http_result  = $http_request->request(
				self::_encode_url( sprintf( self::RACE_URL, strtolower( $region ), $locale ) )
			);

			if ( ! is_wp_error( $http_result ) && $http_result['response']['code'] == 200 ) {
				$races_json = json_decode( $http_result['body'] );
				if ( $races_json == null ) {
					return new WP_Error( 500, __(
						'Unable to fetch data from battle.net for character races',
						'wow_armory_character'
					) );
				}

				$races_data                 = array();
				$races_data['last_checked'] = time();
				$races_data['locale']       = $locale;
				$races_data['api_data']     = $http_result['body'];

				set_transient( 'wowracecache-' . $region . '-' . $locale, $races_data, $expires_after );
			} else {
				return new WP_Error( 500, __(
					'Unable to fetch data from battle.net for character races',
					'wow_armory_character'
				) );
			}
		}

		foreach ( $races_json->races as $race ) {
			if ( $race->id == $race_id ) {
				return $race;
			}
		}

		// We shouldn't ever see this as it implies that the character race returned from the API does not
		// have a lookup value stored in the API. Come on Blizz...
		return null;
	}

	/**
	 * Fetches class information from the community API and returns the class definition for the passed in class ID.
	 *
	 * @param string $region The region to fetch from.
	 * @param string $locale The locale in which to return the result.
	 * @param int $class_id The class ID assigned to a character API request.
	 * @param int $expires_after The expiry time for this lookup cache. Defaults to 1 week.
	 *
	 * @return WP_Error|stdClass Returns either an error if the API request failed or a stdClass encapsulating the response.
	 */
	private function _fetch_class( $region, $locale, $class_id, $expires_after = WEEK_IN_SECONDS ) {
		if ( false !== ( $cached_classes = get_transient( 'wowclasscache-' . $region . '-' . $locale ) ) ) {
			$classes_json = json_decode( $cached_classes['api_data'] );
		} else {
			$http_request = new WP_Http();
			$http_result  = $http_request->request(
				self::_encode_url( sprintf( self::CLASS_URL, strtolower( $region ), $locale ) )
			);

			if ( ! is_wp_error( $http_result ) && $http_result['response']['code'] == 200 ) {
				$classes_json = json_decode( $http_result['body'] );
				if ( $classes_json == null ) {
					return new WP_Error( 500, __(
						'Unable to fetch data from battle.net for character classes',
						'wow_armory_character'
					) );
				}

				$classes_data                 = array();
				$classes_data['last_checked'] = time();
				$classes_data['locale']       = $locale;
				$classes_data['api_data']     = $http_result['body'];

				set_transient( 'wowclasscache-' . $region . '-' . $locale, $classes_data, $expires_after );
			} else {
				return new WP_Error( 500, __(
					'Unable to fetch data from battle.net for character classes',
					'wow_armory_character'
				) );
			}
		}

		foreach ( $classes_json->classes as $class ) {
			if ( $class->id == $class_id ) {
				return $class;
			}
		}

		// We shouldn't ever see this as it implies that the character class returned from the API does not
		// have a lookup value stored in the API. Come on Blizz...
		return null;
	}

	private function _encode_url( $url ) {
		// http://php.net/manual/en/function.rawurlencode.php
		// https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/encodeURI
		$reserved  = array(
			'%2D' => '-',
			'%5F' => '_',
			'%2E' => '.',
			'%21' => '!',
			'%2A' => '*',
			'%27' => "'",
			'%28' => '(',
			'%29' => ')'
		);
		$unescaped = array(
			'%3B' => ';',
			'%2C' => ',',
			'%2F' => '/',
			'%3F' => '?',
			'%3A' => ':',
			'%40' => '@',
			'%26' => '&',
			'%3D' => '=',
			'%2B' => '+',
			'%24' => '$'
		);
		$score     = array(
			'%23' => '#'
		);

		return strtr( rawurlencode( $url ), array_merge( $reserved, $unescaped, $score ) );
	}
}
