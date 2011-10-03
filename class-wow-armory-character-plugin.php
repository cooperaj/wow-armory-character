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

require_once('class-wow-armory-character.php');
require_once('class-wow-armory-character-dal.php');
require_once('class-wow-armory-character-view.php');
require_once('class-wow-armory-character-widget.php');
require_once('class-wow-armory-character-achievements.php');

/**
 * Provides the wordpress integration.
 * 
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character_Plugin
{
	// Bitwise operators for achievement display style.
	const STYLE_ACHIEV_BAR = 1;
	const STYLE_ACHIEV_LIST = 2;
	const STYLE_ACHIEV_LIST_DESC = 4;
	
	// Bitwise operators for profession display.
	const STYLE_PROF_BADGES = 1;
	const STYLE_PROF_BAR = 2;
	const STYLE_PROF_SECONDARY = 4;
	
	public function init()
	{
		global $wacpath;
	
		load_plugin_textdomain('wow_armory_character', false, plugin_dir_path($wacpath) . '/languages');
		
		wp_enqueue_script('wowhead',"http://static.wowhead.com/widgets/power.js");
		wp_enqueue_style('wow_armory_character', plugins_url('css/style.css', $wacpath));
	}
	
	public function admin_init()
	{
		global $wacpath;
		
		wp_enqueue_style('wow_armory_character-admin', plugins_url('css/admin.css', $wacpath));
		
		wp_enqueue_script('wow-armory-character-admin', 
			plugins_url('javascript/admin.js', $wacpath), array('jquery'));
			
		register_setting('wac_options_group', 'wowhead_tooltips');
	}
	
	public function admin_menu()
	{
		$page_name = add_options_page(
			__('WoW Armory Character', 'wow_armory_character'), 
			'Armory Character', 
			'administrator', 
			'wowarmchar', 
			array($this, 'options_page'));
	}
	
	public function widget_init()
	{
		register_widget('WoW_Armory_Character_Widget');
	}
	
	public function options_page()
	{ 
		global $wacpath;
		
		if (isset($_POST['deleteit']) && isset($_POST['delete']))
		{
			// Verify nonce
			check_admin_referer('wowarmchar');
			
			$clear_count = 0;
			foreach((array)$_POST['delete'] as $clear_name)
			{
				delete_option($clear_name);
				$clear_count++;
			}
			
			echo '<div id="message" class="updated fade"><p>' . 
				sprintf(__('Cleared %s caches.', 'wow_armory_character'), $clear_count) . '</p></div>';
		}
		
	?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e('World of Warcraft Armory Character', 'wow_armory_character')?></h2>
			<form method="post" action="<?php echo admin_url('options.php'); ?>"> 
				<?php settings_fields('wac_options_group'); ?>
				
				<table class="form-table">
					<tr>
						<th>Global settings</th>
						<td>
							<input id="attach_css" name="wac_settings[attach_css]" type="checkbox"  value="true" /> 
							<label for="attach_css"><?php _e('Add plugin css to the page.', 'wow_armory_character'); ?></label>
							<br />
							<input id="wowhead_tooltips" name="wac_settings[wowhead_tooltips]" type="checkbox"  value="true" /> 
							<label for="wowhead_tooltips"><?php _e('Display wowhead tooltips when hovering over equipped items.', 'wow_armory_character'); ?></label>
						</td>
					</tr>
				</table>
				
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
			
			<h3 class="title"><?php _e('Character Cache', 'wow_armory_character')?></h3>
			<form id="cache-list" action="options-general.php?page=wowarmchar" method="post">
				<?php
				// Add a nonce
				wp_nonce_field('wowcharcache');
				?>
				
				<table class="widefat">
					<thead>
						<tr>
							<th scope="col" class="check-column"><input type="checkbox" onclick="checkAll(document.getElementById('cache-list'));" /></th>
							<th scope="col"><?php _e('Character Name', 'wow_armory_character')?></th>
							<th scope="col"><?php _e('Realm', 'wow_armory_character')?></th>
							<th scope="col"><?php _e('Cached On', 'wow_armory_character')?></th>
							<th scope="col"><?php _e('Note/s', 'wow_armory_character')?></th>
						</tr>
					</thead>
					<tbody>

					<?php 
					$chars = WoW_Armory_Character_DAL::fetch_all_cached_characters();
						if (count($chars) == 0) 
						{
						?>
						<tr>
							<td scope="row" colspan="4" style="text-align: center;"><strong><?php _e('No Caches Found', 'wow_armory_character')?></strong></td>
						</tr>
						<?php
						}
						else
						{
							foreach ($chars as $character)
							{
								$char_view = new WoW_Armory_Character_View($character);
						?>
						<tr>
							<th scope="row" class="check-column"><input type="checkbox" name="delete[]" value="<?php echo $character->cache_name; ?>" /></th>
							<td scope="row" style="text-align: left">
								<img class="icon-race-18" src="<?php echo $char_view->get_race_icon_url(); ?>" />
								<span class="<?php echo $char_view->get_class_icon_class(); ?>"></span>
								<?php echo $character->name; ?>
							</td>
							<td scope="row" style="text-align: left"><?php echo $character->realm; ?></td>
							<td scope="row" style="text-align: left"><?php echo date(__('F j, Y, g:i a', 'wow_armory_character'), $character->last_checked); ?></td>
							<td scope="row" style="text-align: left" class="notes">
							<?php if (count($character->notes) > 0) : ?>
								<img class="warning-icon" src="<?php echo plugins_url('images/warning.png', $wacpath) ?>" alt="<?php _e('Warning Icon', 'wow_armory_character'); ?>" />
								<p>
							<?php 	foreach ($character->notes as $note) : ?>
								<?php echo $note; ?><br />
							<?php 	endforeach; ?>
								</p>
							<?php endif; ?>
							</td>
						</tr>
						<?php
							}
						}
						?>

					</tbody>
				</table>
				<div class="tablenav">
					<input type="submit" value="<?php _e('Clear selected cache items', 'wow_armory_character')?>" name="deleteit" class="button-secondary delete" />
					<br class="clear" />
				</div>

				<br class="clear" />
			</form>	
		</div>
	<?php 
	}

	public function shortcode($atts, $content = null) 
	{
		$options = shortcode_atts(array(
			'name' => '', 
			'realm' => '',
			'region' => 'EU',
			'show_portrait' => 1,
			'show_title' => 1,
			'show_talents' => 1,
			'show_items' => 1,
			'show_profs' => self::STYLE_PROF_BAR | self::STYLE_PROF_SECONDARY,
			'show_achievs' => self::STYLE_ACHIEV_BAR | self::STYLE_ACHIEV_LIST,
			'locale' => 'en_GB',
		), $atts);
		
		$char = WoW_Armory_Character_DAL::fetch_character($options['region'], $options['locale'], $options['realm'], $options['name']);
		
		if (!is_wp_error($char))
		{
			$view = new WoW_Armory_Character_View($char);
			return $view->display_character($options);
		}
		else
		{
			// Show the error message.
			return $char->get_error_message();
		}
	}
}