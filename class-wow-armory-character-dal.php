<?php
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

require_once(ABSPATH . WPINC . '/class-http.php');

/**
 * Provide an accessor/factory layer onto the WoW community API.
 * 
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character_DAL
{
	/**
	 * The API url to retrieve a character populated with Guild, Equipped Items, Profession and Talent information.
	 * @var string
	 */
	const CHARACTER_URL = 'http://%s.battle.net/api/wow/character/%s/%s?fields=guild,items,professions,talents,titles&amplocale=%s';
	
	const RACE_URL = 'http://%s.battle.net/api/wow/data/character/races?locale=%s';
	const CLASS_URL = 'http://%s.battle.net/api/wow/data/character/classes?locale=%s';
	
	static function fetch_all_cached_characters()
	{
		$chars = array();
		
		global $wpdb;
		$options = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE `option_name` LIKE '%wowcharcache%'");
		
		foreach ($options as $option)
		{
			$data = get_option($option->option_name);
			$api_data = json_decode($data['api_data']);
			$char = self::fetch_character($data['region'], $data['locale'], $api_data->realm, $api_data->name);
			
			$char->last_checked = $data['last_checked'];
			$char->cache_name = $option->option_name;
			
			$chars[] = $char;
		}
		
		return $chars;
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
	 * @return WoW_Armory_Character|WP_Error A completed character or an error if the retrieval was unsuccessful.
	 */
	static function fetch_character($region, $locale, $realm, $name, $expires_after = 43200)
	{
		$char_api_data_obj = self::_fetch_character($region, $locale, $realm, $name, $expires_after);
		if (is_wp_error($char_api_data_obj))
			return $char_api_data_obj;
	
		$char_api_data_obj->race = self::_fetch_race($region, $locale, $char_api_data_obj->race);
		if (is_wp_error($char_api_data_obj->race))
			return $char_api_data_obj->race;
			
		$char_api_data_obj->class = self::_fetch_class($region, $locale, $char_api_data_obj->class);
		if (is_wp_error($char_api_data_obj->class))
			return $char_api_data_obj->class;
		
		return new WoW_Armory_Character($region, $locale, $char_api_data_obj);
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
	 * @return stdClass|WP_Error A stdClass data object containing character information or an error if the retrieval was unsuccessful.
	 */
	private function _fetch_character($region, $locale, $realm, $name, $expires_after = 43200)
	{
		// Try to fetch from the cache.
		$cached_char = get_option('wowcharcache-'.md5($name . '-' . $realm . '-' . $region . '-' . $locale));
		if ($cached_char['last_checked'] > (time() - $expires_after))
		{
			// Cached available and within the expiry time.
			return json_decode($cached_char['api_data']);
		}
		else
		{
			$http_request = new WP_Http();
			$http_result = $http_request->request(
				sprintf(self::CHARACTER_URL, $region, $realm, $name, $locale));
			
			if (!is_wp_error($http_result) && $http_result['response']['code'] == 200)
			{
				$char_data = array();
				$char_data['last_checked'] = time();
				$char_data['region'] = $region;
				$char_data['locale'] = $locale;
				$char_data['api_data'] = $http_result['body'];
				
				// Cache the result so we don't have to keep fetching this. We have to update if it exists already.
				if (get_option('wowcharcache-'.md5($name . '-' . $realm . '-' . $region . '-' . $locale)))
				{
					update_option('wowcharcache-'.md5($name . '-' . $realm . '-' . $region . '-' . $locale), $char_data);
				}
				else
				{
					add_option('wowcharcache-'.md5($name . '-' . $realm . '-' . $region . '-' . $locale), $char_data);
				}
				
				return json_decode($char_data['api_data']);
			}
			
			// If we get here then it means the character does not exist in the cache and the API interrogation failed.
			return new WP_Error(500, __('Unable to fetch data from battle.net for character', 'wow_armory_character'));
		}
	}
	
	/**
	 * Fetches race information from the community API and returns the race definition for the passed in race ID.
	 * 
	 * @param string $region The region to fetch from.
	 * @param string $locale The locale in which to return the result.
	 * @param int $race_id The race ID assigned to a character API request.
	 * @param int $expires_after The expiry time for this lookup cache. Defaults to 1 week.
	 * @return WP_Error|stdClass Returns either an error if the API request failed or a stdClass encapsulating the response.
	 */
	private function _fetch_race($region, $locale, $race_id, $expires_after = 2419200)
	{
		$cached_races = get_option('wowracecache-' . $region . '-' . $locale);
		if ($cached_races['last_checked'] > (time() - $expires_after))
		{
			$races_json = $cached_races['api_data'];
		}
		else
		{
			$http_request = new WP_Http();
			$http_result = $http_request->request(sprintf(self::RACE_URL, $region, $locale));
				
			if (!is_wp_error($http_result) && $http_result['response']['code'] == 200)
			{
				$races_data = array();
				$races_data['last_checked'] = time();
				$races_data['locale'] = $locale;
				$races_data['api_data'] = $http_result['body'];
				
				if (get_option('wowracecache-' . $region . '-' . $locale))
				{
					update_option('wowracecache-' . $region . '-' . $locale, $races_data);
				}
				else
				{
					add_option('wowracecache-' . $region . '-' . $locale, $races_data);
				}
				
				$races_json = $races_data['api_data'];
			}
			else 
			{
				return new WP_Error(500, __('Unable to fetch data from battle.net for character races', 'wow_armory_character'));
			}
		}
		
		$races_obj = json_decode($races_json);
		foreach ($races_obj->races as $race)
		{
			if ($race->id == $race_id)
				return $race;
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
	 * @return WP_Error|stdClass Returns either an error if the API request failed or a stdClass encapsulating the response.
	 */
	private function _fetch_class($region, $locale, $class_id, $expires_after = 2419200)
	{
		$cached_classes = get_option('wowclasscache-' . $region . '-' . $locale);
		if ($cached_classes['last_checked'] > (time() - $expires_after))
		{
			$classes_json = $cached_classes['api_data'];
		}
		else
		{
			$http_request = new WP_Http();
			$http_result = $http_request->request(sprintf(self::CLASS_URL, $region, $locale));
				
			if (!is_wp_error($http_result) && $http_result['response']['code'] == 200)
			{
				$classes_data = array();
				$classes_data['last_checked'] = time();
				$classes_data['locale'] = $locale;
				$classes_data['api_data'] = $http_result['body'];
				
				if (get_option('wowclasscache-' . $region . '-' . $locale))
				{
					update_option('wowclasscache-' . $region . '-' . $locale, $classes_data);
				}
				else
				{
					add_option('wowclasscache-' . $region . '-' . $locale, $classes_data);
				}
				
				$classes_json = $classes_data['api_data'];
			}
			else 
			{
				return new WP_Error(500, __('Unable to fetch data from battle.net for character classes', 'wow_armory_character'));
			}
		}
		
		$classes_obj = json_decode($classes_json);
		foreach ($classes_obj->classes as $class)
		{
			if ($class->id == $class_id)
				return $class;
		}
		
		// We shouldn't ever see this as it implies that the character class returned from the API does not
		// have a lookup value stored in the API. Come on Blizz...
		return null;
	}
}