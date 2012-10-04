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

/**
 * Provides the wordpress widget.
 * 
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character_Widget extends WP_Widget
{
	protected $_default_options;
	
	public function __construct()
	{
		$this->_default_options = array(
			'name' => '', 
			'realm' => '',
			'region' => 'EU',
			'show_portrait' => 1,
			'show_title' => 1,
			'show_talents' => 1,
			'show_items' => 1,
			'show_profs' => WoW_Armory_Character_Plugin::STYLE_PROF_BAR | WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY,
			'show_achievs' => WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR | WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST,
			'locale' => 'en_GB',
			'title' => __('Armory for %NAME%', 'wow_armory_character'),
		);
		
		$widget_ops = array('classname' => 'WoW_Armory_Character_Widget', 'description' => __("Displays a World of Warcraft character's information", 'wow_armory_character'));
		parent::__construct('wow-armory-character-widget', __('WoW Armory Character', 'wow_armory_character'), $widget_ops);
	}
	
	public function form($instance)
	{
		$instance = wp_parse_args ((array)$instance, $this->_default_options);
	?>
		<div class="wow_armory_options">
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wow_armory_character'); ?></label><br />
				<input type="text" class="wa-title widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
				<small><?php _e('Use %NAME% for the character\'s name.', 'wow_armory_character'); ?></small>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('name'); ?>"><?php _e('Character name:', 'wow_armory_character'); ?></label><br />
				<input type="text" class="wa-name widefat" id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" value="<?php echo esc_attr($instance['name']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('realm'); ?>"><?php echo __('Realm:', 'wow_armory_character'); ?></label><br />
				<select class="wa-region" id="<?php echo $this->get_field_id('region'); ?>" name="<?php echo $this->get_field_name('region'); ?>">
					<option value="US"<?php selected($instance['region'], 'US', true); ?>>US</option>
					<option value="EU"<?php selected($instance['region'], 'EU', true); ?>>EU</option>
					<option value="KR"<?php selected($instance['region'], 'KR', true); ?>>KR</option>
					<option value="TW"<?php selected($instance['region'], 'TW', true); ?>>TW</option>
				</select>
				<input type="text" class="wa-realm" style="width: 150px" id="<?php echo $this->get_field_id('realm'); ?>" name="<?php echo $this->get_field_name('realm'); ?>" value="<?php echo htmlspecialchars($instance['realm']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('locale'); ?>"><?php echo __('Language:', 'wow_armory_character'); ?></label><br />
				<select class="wa-lang widefat" id="<?php echo $this->get_field_id('locale'); ?>" name="<?php echo $this->get_field_name('locale'); ?>">
					<option value="en_GB"<?php selected($instance['locale'], 'en_GB', true); ?>><?php _e('English (EU)', 'wow_armory_character'); ?></option>
					<option value="en_US"<?php selected($instance['locale'], 'en_US', true); ?>><?php _e('English (US)', 'wow_armory_character'); ?></option>
					<option value="de_DE"<?php selected($instance['locale'], 'de_DE', true);  ?>><?php _e('Deutsch', 'wow_armory_character'); ?></option>
					<option value="es_ES"<?php selected($instance['locale'], 'es_ES', true);  ?>><?php _e('Español (EU)', 'wow_armory_character'); ?></option>
					<option value="es_MX"<?php selected($instance['locale'], 'es_MX', true);  ?>><?php _e('Español (AL)', 'wow_armory_character'); ?></option>
					<option value="fr_FR"<?php selected($instance['locale'], 'fr_FR', true);  ?>><?php _e('Française', 'wow_armory_character'); ?></option>
					<option value="pt_PT"<?php selected($instance['locale'], 'pt_PT', true);  ?>><?php _e('Português (EU)', 'wow_armory_character'); ?></option>
					<option value="pt_BR"<?php selected($instance['locale'], 'pt_BR', true);  ?>><?php _e('Português (AL)', 'wow_armory_character'); ?></option>
					<option value="ru_RU"<?php selected($instance['locale'], 'ru_RU', true);  ?>><?php _e('Pусский', 'wow_armory_character'); ?></option>
					<option value="ko_KR"<?php selected($instance['locale'], 'ko_KR', true);  ?>><?php _e('한국의', 'wow_armory_character'); ?></option>
					<option value="zh_TW"<?php selected($instance['locale'], 'zh_TW', true);  ?>><?php _e('中國', 'wow_armory_character'); ?></option>
				</select>
			</p>
			<h4><?php _e ('Display Options', 'wow_armory_character'); ?></h4>
			<p>
				<input id="<?php echo $this->get_field_id('show_portrait'); ?>" name="<?php echo $this->get_field_name('show_portrait'); ?>" value="1" type="checkbox" <?php echo $instance['show_portrait'] ? 'checked="checked"' : ''; ?> />
				<label for="<?php echo $this->get_field_id('show_portrait'); ?>"><?php _e('Show Portrait', 'wow_armory_character'); ?></label><br/>
				<input id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" value="1" type="checkbox" <?php echo $instance['show_title'] ? 'checked="checked"' : ''; ?> />
				<label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Show Title', 'wow_armory_character'); ?></label><br/>
				<input id="<?php echo $this->get_field_id('show_talents'); ?>" name="<?php echo $this->get_field_name('show_talents'); ?>" value="1" type="checkbox" <?php echo $instance['show_talents'] ? 'checked="checked"' : ''; ?> />
				<label for="<?php echo $this->get_field_id('show_talents'); ?>"><?php _e('Show Talents', 'wow_armory_character'); ?></label><br/>
				<input id="<?php echo $this->get_field_id('show_items'); ?>" name="<?php echo $this->get_field_name('show_items'); ?>" value="1" type="checkbox" <?php echo $instance['show_items'] ? 'checked="checked"' : ''; ?> />
				<label for="<?php echo $this->get_field_id('show_items'); ?>"><?php _e('Show Items', 'wow_armory_character'); ?></label><br/>
				<input id="<?php echo $this->get_field_id('show_profs'); ?>" name="<?php echo $this->get_field_name('show_profs'); ?>" value="1" type="checkbox" <?php echo $instance['show_profs'] ? 'checked="checked"' : ''; ?> />
				<label for="<?php echo $this->get_field_id('show_profs'); ?>"><?php _e('Show Professions', 'wow_armory_character'); ?></label><br/>
				<span class="sub_options<?php echo !$instance['show_profs'] ? ' sub_options_hidden' : ''; ?>" rel="<?php echo $this->get_field_id('show_profs'); ?>">
					<input id="<?php echo $this->get_field_id('show_profs_badges'); ?>"
								 name="<?php echo $this->get_field_name('show_profs_badges'); ?>"
								 value="<?php echo WoW_Armory_Character_Plugin::STYLE_PROF_BADGES; ?>"
								 type="checkbox"
								 <?php echo (($instance['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_BADGES) === WoW_Armory_Character_Plugin::STYLE_PROF_BADGES) ? 'checked="checked"' : ''; ?>
					/>
					<label for="<?php echo $this->get_field_id('show_profs_badges'); ?>"title="<?php _e('Show your professions as a series of badges.', 'wow_armory_character'); ?>"><?php _e('Badges', 'wow_armory_character'); ?></label><br/>
					<input id="<?php echo $this->get_field_id('show_profs_bar'); ?>"
								 name="<?php echo $this->get_field_name('show_profs_bar'); ?>"
								 value="<?php echo WoW_Armory_Character_Plugin::STYLE_PROF_BAR; ?>"
								 type="checkbox"
								 <?php echo (($instance['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_BAR) === WoW_Armory_Character_Plugin::STYLE_PROF_BAR) ? 'checked="checked"' : ''; ?>
					/>
					<label for="<?php echo $this->get_field_id('show_profs_bar'); ?>"title="<?php _e('Show your profession levels with a progress bar.', 'wow_armory_character'); ?>"><?php _e('Progress Bars', 'wow_armory_character'); ?></label><br/>
					<input id="<?php echo $this->get_field_id('show_profs_secondary'); ?>"
								 name="<?php echo $this->get_field_name('show_profs_secondary'); ?>"
								 value="<?php echo WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY; ?>"
								 type="checkbox"
								 <?php echo (($instance['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY) === WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY) ? 'checked="checked"' : ''; ?>
					/>
					<label for="<?php echo $this->get_field_id('show_profs_secondary'); ?>"title="<?php _e('Show your secondary professions (e.g. fishing, first aid).', 'wow_armory_character'); ?>"><?php _e('Secondary Professions', 'wow_armory_character'); ?></label><br/>
				</span>
				<input id="<?php echo $this->get_field_id('show_achievs'); ?>" name="<?php echo $this->get_field_name('show_achievs'); ?>" value="1" type="checkbox" <?php echo $instance['show_achievs'] ? 'checked="checked"' : ''; ?> />
				<label for="<?php echo $this->get_field_id('show_achievs'); ?>"><?php _e('Show Achievements', 'wow_armory_character'); ?></label><br/>
				<span class="sub_options<?php echo !$instance['show_achievs'] ? ' sub_options_hidden' : ''; ?>" rel="<?php echo $this->get_field_id('show_achievs'); ?>">
					<input id="<?php echo $this->get_field_id('show_achievs_bar'); ?>"
								 name="<?php echo $this->get_field_name('show_achievs_bar'); ?>"
								 value="<?php echo WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR; ?>"
								 type="checkbox"
								 <?php echo (($instance['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR) ? 'checked="checked"' : ''; ?>
					/>
					<label for="<?php echo $this->get_field_id('show_achievs_list'); ?>"title="<?php _e('Show a progress bar indicating the number of achievements this character has gained from the total.', 'wow_armory_character'); ?>"><?php _e('Completion Bar', 'wow_armory_character'); ?></label><br/>
					<input id="<?php echo $this->get_field_id('show_achievs_list'); ?>"
								 name="<?php echo $this->get_field_name('show_achievs_list'); ?>"
								 value="<?php echo WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST; ?>"
								 type="checkbox"
								 <?php echo (($instance['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST) ? 'checked="checked"' : ''; ?>
					/>
					<label for="<?php echo $this->get_field_id('show_achievs_list'); ?>" title="<?php _e('Show a listing of the characters most recent achievements.', 'wow_armory_character'); ?>"><?php _e('Recent Achievements', 'wow_armory_character'); ?></label><br/>
					<input id="<?php echo $this->get_field_id('show_achievs_list_desc'); ?>"
								 name="<?php echo $this->get_field_name('show_achievs_list_desc'); ?>"
								 value="<?php echo WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC; ?>"
								 type="checkbox"
								 <?php echo (($instance['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC) ? 'checked="checked"' : ''; ?>
					/>
					<label for="<?php echo $this->get_field_id('show_achievs_list_desc'); ?>" title="<?php _e('Show the achievement description text when showing the Recent Achievements listing.', 'wow_armory_character'); ?>"><?php _e('Achievement Descriptions', 'wow_armory_character'); ?></label>
				</span>
			</p>
		</div>
	<?php
	}
	
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		$instance['name'] = strip_tags(stripslashes($new_instance['name']));
		$instance['realm'] = strip_tags(stripslashes($new_instance['realm']));
		$instance['region'] = strip_tags(stripslashes($new_instance['region']));
		$instance['show_portrait'] = strip_tags(stripslashes($new_instance['show_portrait']));
		$instance['show_title'] = strip_tags(stripslashes($new_instance['show_title']));
		$instance['show_talents'] = strip_tags(stripslashes($new_instance['show_talents']));
		$instance['show_items'] = strip_tags(stripslashes($new_instance['show_items']));
		$instance['locale'] = strip_tags(stripslashes($new_instance['locale']));
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		
		// We ignore the setting of 'show_profs' and parse the bitwise setting instead. Having
		// bitwise settings checked implies you want the master setting configured.
		$prof_config = null;
		if ($new_instance['show_profs_badges']) $prof_config = $prof_config | WoW_Armory_Character_Plugin::STYLE_PROF_BADGES;
		if ($new_instance['show_profs_bar']) $prof_config = $prof_config | WoW_Armory_Character_Plugin::STYLE_PROF_BAR;
		if ($new_instance['show_profs_secondary']) $prof_config = $prof_config | WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY;
		
		$ach_config = null;
		if ($new_instance['show_achievs_bar']) $ach_config = $ach_config | WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR;
		if ($new_instance['show_achievs_list']) $ach_config =  $ach_config | WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST;
		if ($new_instance['show_achievs_list_desc']) $ach_config =  $ach_config | WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC;

		// If no settings are configured but the master or useless setting is ticked then set the defaults.
		if (($new_instance['show_profs'] && $prof_config == null) || 
				$prof_config == WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY) 
			$prof_config = $this->_default_options['show_profs'];
		$instance['show_profs'] = $prof_config;
		
		if (($new_instance['show_achievs'] && $ach_config == null) ||
				$ach_config == WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC) 
			$ach_config = $this->_default_options['show_achievs'];
		$instance['show_achievs'] = $ach_config;		
		
		return $instance;
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		
		echo $before_widget;
		
		$title = apply_filters('widget_title', str_replace('%NAME%', $instance['name'], $instance['title']));		
		if (!empty($title))
			echo $before_title . $title . $after_title;		
		
		$char = WoW_Armory_Character_DAL::fetch_character($instance['region'], $instance['locale'], $instance['realm'], $instance['name']);
		
		if (!is_wp_error($char))
		{
			$view = new WoW_Armory_Character_View($char);
			echo $view->display_character($instance);
		}
		else
		{
			// Show the error message.
			echo $char->get_error_message();
		}
		
		echo $after_widget;
	}
}