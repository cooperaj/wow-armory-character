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

/**
 * Provides the wordpress integration.
 * 
 * @author Adam Cooper <adam@networkpie.co.uk>
 */
class WoW_Armory_Character_Plugin
{
	public function init()
	{
		load_plugin_textdomain('wow_armory_character', false, __DIR__ . '/languages');
		
		wp_enqueue_script('wowhead',"http://static.wowhead.com/widgets/power.js");
		wp_enqueue_style('wow_armory_character', plugins_url('css/style.css', $GLOBALS['wacpath']));
	}
	
	public function admin_init()
	{
		wp_register_style('wow-armory-character-admin', 
			plugins_url('css/admin.css', $GLOBALS['wacpath']));
			
		wp_enqueue_script('wow-armory-character-admin', 
			plugins_url('javascript/admin.js', $GLOBALS['wacpath']), array('jquery'));
	}
	
	public function admin_menu()
	{
		$page_name = add_options_page(
			__('WoW Character Cache', 'wow_armory_character'), 
			'Character Cache', 
			'administrator', 
			'wowcharcache', 
			array($this, 'options_page'));
			
		// Using registered $page_name handle to hook stylesheet loading
    add_action( 'admin_print_styles-' . $page_name, array($this, 'admin_styles'));
	}
	
	public function admin_styles()
	{
		wp_enqueue_style('wow-armory-character-admin');
	}
	
	public function widget_init()
	{
		register_widget('WoW_Armory_Character_Widget');
	}
	
	public function options_page()
	{ 
		if (isset($_POST['deleteit']) && isset($_POST['delete']))
		{
			// Verify nonce
			check_admin_referer('wowcharcache');
			
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
	    <h2><?php _e('World of Warcraft Character Cache', 'wow_armory_character')?></h2>
			<form id="cache-list" action="options-general.php?page=wowcharcache" method="post">
				<?php
				// Add a nonce
				wp_nonce_field('wowcharcache');
				?>
			
				<div class="tablenav">
	    		<input type="submit" value="<?php _e('Refresh', 'wow_armory_character')?>" name="refresh" class="button-secondary" />
	    		<input type="submit" value="<?php _e('Clear Cache', 'wow_armory_character')?>" name="deleteit" class="button-secondary delete" />
	    		<br class="clear" />
	    	</div>
	    
	    	<br class="clear" />
	    		
	    	<table class="widefat">
	    		<thead>
	    			<tr>
	    				<th scope="col" class="check-column"><input type="checkbox" onclick="checkAll(document.getElementById('cache-list'));" /></th>
	        		<th scope="col"><?php _e('Character Name', 'wow_armory_character')?></th>
	        		<th scope="col"><?php _e('Realm', 'wow_armory_character')?></th>
	    				<th scope="col"><?php _e('Cached On', 'wow_armory_character')?></th>
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
							<td scope="row" style="text-align: left"><?php echo date("F j, Y, g:i a", $character->last_checked); ?></td>
						</tr>
						<?php
							}
						}
						?>
	    		
	    		</tbody>
	    	</table>
	    	<div class="tablenav">
	    		<br class="clear" />
	    	</div>
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
			'show_profs' => 1,
			'show_achievs' => 1,
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