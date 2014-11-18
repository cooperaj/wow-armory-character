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
 * Provides data display methods for WoW_Armory_Character objects.
 *
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character_View {
	// Needed so that we can grab profession icons. They aren't consistant with the actual
	// names we can't do any automated conversion.
	const PROFESSION_JSON = 'db/professionNames.json';

	const PROFILE_URL = 'http://%s.battle.net/wow/%s';
	const STATIC_URL = 'http://%s.battle.net/wow/static/images';
	const PORTRAIT_URL = 'http://%s.battle.net/static-render/%s';
	const CDN_URL = 'http://%s.media.blizzard.com/wow';

	const WOWHEAD_ITEM_URL = 'http://%s.wowhead.com/?item=%s';
	const WOWHEAD_ACHIEV_URL = 'http://%s.wowhead.com/?achievement=%s';

	const CACHE_FOLDER_NAME = 'cache/';

	public $character;

	protected $_global_options;

	protected $_gender_table;
	protected $_slot_table;
	protected $_locale_table;

	public function __construct( WoW_Armory_Character $character ) {
		$this->character = $character;

		// Make available global options
		$this->_global_options = get_option(
			'wac_settings',
			WoW_Armory_Character_Plugin::admin_settings_default_values()
		);

		$this->_gender_table = array(
			'male',
			'female',
		);

		$this->_slot_table = array(
			'head',
			'neck',
			'shoulder',
			'back',
			'chest',
			'shirt',
			'tabard',
			'wrist',
			'hands',
			'waist',
			'legs',
			'feet',
			'finger1',
			'finger2',
			'trinket1',
			'trinket2',
			'mainHand',
			'offHand',
			'ranged',
		);

		$this->_locale_table = array(
			'en_US' => 'en',
			'es_MX' => 'es',
			'en_GB' => 'en',
			'es_ES' => 'es',
			'pt_PT' => 'pt',
			'pt_BR' => 'pt',
			'fr_FR' => 'fr',
			'ru_RU' => 'ru',
			'de_DE' => 'de',
			'it_IT' => 'it',
			'ko_KR' => 'ko',
			'zh_TW' => 'zh',
			'zh_CN' => 'zh'
		);
	}

	public function display_character( $options ) {
		$randNo = rand( 0, pow( 10, 5 ) );

		$character = $this->character;

		ob_start();
		include( apply_filters( 'wow-armory-character-template', 'view-wow-armory-character.php' ) );
		$display = apply_filters( 'wow-armory-character-display', ob_get_clean(), $this->character );

		return $display;
	}

	public function get_achievement_url( $achiev_id, $section_id, $category_id ) {
		if ( $this->_global_options['wowhead_links'] ) {
			return sprintf(
				self::WOWHEAD_ACHIEV_URL,
				( $this->_locale_table[ $this->character->locale ] == 'en'
					? 'www'
					: $this->_locale_table[ $this->character->locale ] ),
				$achiev_id
			);
		} else {
			return sprintf(
				       self::PROFILE_URL,
				       strtolower( $this->character->region ),
				       $this->_locale_table[ $this->character->locale ]
			       ) . '/character/' .
			       $this->character->realm . '/' . $this->character->name . '/achievement#' .
			       $section_id . ':' .
			       ( ( $category_id != null ) ? $category_id . ':' : '' ) .
			       'a' . $achiev_id;
		}
	}

	public function get_class_icon_class() {
		// Ensure that we cache this file for the css to use.
		$this->fetch_asset( $this->_get_static_url() . '/icons/class/classes-18.jpg' );

		return 'icon-class-18 icon-' . strtolower( str_replace( ' ', '-', $this->character->en_class->name ) ) . '-18';
	}

	public function get_feed_icon_url( $icon ) {
		return $this->fetch_asset(
			$this->_get_cdn_url() . '/icons/56/' . $icon . '.jpg'
		);
	}

	public function get_fuzzy_time( $timestamp ) {
		// values in microseconds
		$time_formats = array(
			array( 60000, __( 'just now', 'wow_armory_character' ) ),
			array( 90000, __( '1 minute ago', 'wow_armory_character' ) ),
			array( 3600000, __( 'minutes', 'wow_armory_character' ), 60000 ),
			array( 5400000, __( '1 hour ago', 'wow_armory_character' ) ),
			array( 86400000, __( 'hours ago', 'wow_armory_character' ), 3600000 ),
			array( 129600000, __( '1 day ago', 'wow_armory_character' ) ),
			array( 604800000, __( 'days ago', 'wow_armory_character' ), 86400000 ),
			array( 907200000, __( '1 week ago', 'wow_armory_character' ) ),
			array( 2628000000, __( 'weeks ago', 'wow_armory_character' ), 604800000 ),
			array( 3942000000, __( '1 month ago', 'wow_armory_character' ) ),
			array( 31536000000, __( 'months ago', 'wow_armory_character' ), 2628000000 ),
			array( 47304000000, __( '1 year ago', 'wow_armory_character' ) ),
			array( 3153600000000, __( 'years ago', 'wow_armory_character' ), 31536000000 ),
		);

		$now = time() * 1000; // current unix timestamp boosted to milliseconds

		// if a number is passed assume it is a unix time stamp
		// if string is passed try and parse it to unix time stamp
		if ( is_numeric( $timestamp ) ) {
			$dateFrom = $timestamp;
		} elseif ( is_string( $timestamp ) ) {
			$dateFrom = strtotime( $timestamp );
		}

		$difference = $now - $dateFrom; // difference between now and the passed time.
		$val        = ''; // value to return

		if ( $dateFrom <= 0 ) {
			$val = __( 'a long time ago', 'wow_armory_character' );
		} else {
			// loop through each format measurement in array
			foreach ( $time_formats as $format ) {
				// if the difference from now and passed time is less than first option in format measurment
				if ( $difference < $format[0] ) {
					// if the format array item has no calculation value
					if ( count( $format ) == 2 ) {
						$val = $format[1];
						break;
					} else {
						// divide difference by format item value to get number of units
						$val = sprintf( '%d %s', ceil( $difference / $format[2] ), $format[1] );
						break;
					}
				}
			}
		}

		return $val;
	}

	public function get_guild_url() {
		return sprintf(
			       self::PROFILE_URL,
			       strtolower( $this->character->region ),
			       $this->_locale_table[ $this->character->locale ]
		       ) . '/guild/' .
		       $this->character->realm . '/' . $this->character->guild->name . '/';
	}

	public function get_item_url( $item_id ) {
		if ( $this->_global_options['wowhead_links'] ) {
			return sprintf(
				self::WOWHEAD_ITEM_URL,
				( $this->_locale_table[ $this->character->locale ] == 'en'
					? 'www' : $this->_locale_table[ $this->character->locale ] ),
				$item_id
			);
		} else {
			return sprintf(
				       self::PROFILE_URL,
				       strtolower( $this->character->region ),
				       $this->_locale_table[ $this->character->locale ]
			       ) .
			       '/item/' . $item_id;
		}
	}

	public function get_item_icon_url( $icon_name ) {
		return $this->fetch_asset(
			$this->_get_cdn_url() . '/icons/56/' . $icon_name . '.jpg'
		);
	}

	public function get_portrait_icon_url() {
		$lcr      = strtolower( $this->character->region );
		$portrait = sprintf( self::PORTRAIT_URL, $lcr, $lcr . '/' . $this->character->thumbnail );

		// The alt image is just a dark silouette but is needed incase a portrait hasn't been generated.
		$alt_img = $this->character->race->id . '-' . $this->character->gender . '.jpg';

		return $this->fetch_asset(
			$portrait . '?alt=/wow/static/images/2d/avatar/' . $alt_img
		);
	}

	public function get_profession_badge_text( stdClass $prof ) {
		return sprintf( '%s / %s', $prof->rank, $prof->max );
	}

	public function get_profession_url( stdClass $prof ) {
		global $wacpath;

		// Professions are linked using the english names. We need to load these from our own sources
		// since there are no API endpoints for this information.
		$prof_names_json = json_decode(
			file_get_contents( plugin_dir_path( $wacpath ) . DIRECTORY_SEPARATOR . self::PROFESSION_JSON )
		);
		if ( $prof_names_json == null ) {
			return new WP_Error( 500, __( 'Unable to load the profession db file.', 'wow_armory_character' ) );
		}

		return sprintf(
			       self::PROFILE_URL,
			       strtolower( $this->character->region ),
			       $this->_locale_table[ $this->character->locale ]
		       ) . '/character/' .
		       $this->character->realm . '/' . $this->character->name . '/profession/' . $prof_names_json->{$prof->id};
	}

	public function get_profession_icon_url( stdClass $prof ) {
		return $this->fetch_asset(
			$this->_get_cdn_url() . '/icons/56/' . $prof->icon . '.jpg'
		);
	}

	public function get_profile_url( $type = 'simple' ) {
		return sprintf(
			       self::PROFILE_URL,
			       strtolower( $this->character->region ),
			       $this->_locale_table[ $this->character->locale ]
		       ) . '/character/' .
		       $this->character->realm . '/' . $this->character->name . '/' . $type;
	}

	public function get_race_icon_url() {
		return $this->fetch_asset(
			$this->_get_cdn_url() . '/icons/56/race_' .
			strtolower( str_replace( ' ', '-', $this->character->en_race->name ) ) . '_' .
			$this->_gender_table[ $this->character->gender ] . '.jpg'
		);
	}

	public function get_talent_url( stdClass $talent ) {
		return sprintf(
			       self::PROFILE_URL,
			       strtolower( $this->character->region ),
			       $this->_locale_table[ $this->character->locale ]
		       ) . '/tool/talent-calculator#' .
		       $this->character->calcClass . $talent->calcSpec . '!' . $talent->calcTalent . '!' . $talent->calcGlyph;
	}

	public function get_talent_tree_icon_url( stdClass $talent ) {
		return $this->fetch_asset(
			$this->_get_cdn_url() . '/icons/56/' . $talent->spec->icon . '.jpg'
		);
	}

	public function get_talent_tree_text( stdClass $talent ) {
		return $talent->spec->name; //$talent->trees[0]->total . ' / ' . $talent->trees[1]->total . ' / ' . $talent->trees[2]->total;
	}

	public function get_name_with_title_text() {
		foreach ( $this->character->titles as $title ) {
			if ( isset( $title->selected ) && $title->selected ) {
				return sprintf( $title->name, $this->character->name );
			}
		}

		return null;
	}

	public function get_wowhead_achievement_rel( $timestamp ) {
		return '&who=' . $this->character->name . '&when=' . $timestamp;
	}

	public function get_wowhead_item_rel( $tooltip_params, $bonus_lists = null ) {
		$output = '&amp;lvl=' . $this->character->level;
		$output .= ( isset( $tooltip_params->upgrade ) ) ? '&amp;upgd=' . $tooltip_params->upgrade->current : '';
		$output .= ( isset( $tooltip_params->enchant ) ) ? '&amp;ench=' . $tooltip_params->enchant : '';
		$output .= ( isset( $tooltip_params->extraSocket ) && $tooltip_params->extraSocket ) ? '&amp;sock' : '';
		$output .= ( isset( $tooltip_params->suffix ) ) ? '&amp;rand=' . $tooltip_params->suffix : '';
		$output .= ( isset( $tooltip_params->reforge ) ) ? '&amp;forg=' . $tooltip_params->reforge : '';

		// Gems
		$gems = '';
		$i    = 0;
		while ( $i <= 9 ) // If anything ever has more then 10 sockets then something is horribly wrong.
		{
			$gemName = "gem$i";
			if ( isset( $tooltip_params->$gemName ) ) {
				$gems .= ( strlen( $gems ) > 0 ) ? ':' : '';
				$gems .= $tooltip_params->$gemName;
				$i ++;
			} else {
				break; // Sockets are sequential so exit when we can't find one.
			}
		}
		$output .= ( strlen( $gems ) > 0 ) ? '&amp;gems=' . $gems : '';

		// Set Pieces
		$set = '';
		if ( isset( $tooltip_params->set ) ) {
			foreach ( $tooltip_params->set as $set_item ) {
				$set .= ( strlen( $set ) > 0 ) ? ':' : '';
				$set .= $set_item;
			}
		}
		$output .= ( strlen( $set ) > 0 ) ? '&amp;pcs=' . $set : '';

		// Bonus Items
		// http://www.wowhead.com/tooltips#related-warlords-of-draenor-tooltip-changes
		$bonus = '';
		if ( isset( $bonus_lists ) && is_array( $bonus_lists ) ) {
			foreach ( $bonus_lists as $bonus_item ) {
				$bonus .= ( strlen( $bonus ) > 0 ) ? ':' : '';
				$bonus .= $bonus_item;
			}
		}
		$output .= ( strlen( $bonus ) > 0 ) ? '&amp;bonus=' . $bonus : '';

		return $output;
	}

	/**
	 * Attempt to cache an image asset locally and use that instead of always hotlinking to the blizzard assets.
	 *
	 * @param $asset_url string An aboslute url pointing at a cacheable image asset.
	 *
	 * @return string The url of the asset to fetch. This could be the newly cached image or the original url dependant
	 *                on if the attempt to cache was successful.
	 */
	public function fetch_asset( $asset_url ) {
		global $wacpath;

		// Check to see if cache folder is writable before attempting to cache anything
		$cache_folder = plugin_dir_path( $wacpath ) . self::CACHE_FOLDER_NAME;
		if ( ! @is_writable( $cache_folder ) ) {
			return $asset_url;
		}

		$cache_url = plugins_url( self::CACHE_FOLDER_NAME, plugin_basename( $wacpath ) );

		$exploded_asset_url  = explode( '/', parse_url( $asset_url, PHP_URL_PATH ) );
		$asset_name          = end( $exploded_asset_url );
		$exploded_asset_name = explode( '.', $asset_name );
		$extension           = end( $exploded_asset_name );
		$new_asset_url       = $cache_url . $asset_name;

		$final_url      = $asset_url;
		$new_asset_path = $cache_folder . $asset_name;

		if ( $extension == 'gif' || $extension == 'jpg' || $extension == 'png' ) {
			if ( file_exists( $new_asset_path ) ) {
				$final_url = $new_asset_url;
			} else {
				$http_request = new WP_Http();
				$http_result  = $http_request->request(
					$asset_url,
					array(
						'stream'   => true,
						'filename' => $new_asset_path
					)
				);

				if ( ! is_wp_error( $http_result ) && $http_result['response']['code'] == 200 ) {
					$final_url = $new_asset_url;
				} else {
					unlink( $new_asset_path );
				}

			}
		}

		return $final_url;
	}

	private function _get_cdn_url() {
		return sprintf( self::CDN_URL, strtolower( $this->character->region ) );
	}

	private function _get_static_url() {
		return sprintf( self::STATIC_URL, strtolower( $this->character->region ) );
	}
}
