<div id="armory-<?php echo $randNo; ?>" class="armory_display">
	<div class="armory_character">
		<?php if ($options['show_portrait'] == 1) : ?>
		<img src="<?php echo $this->get_portrait_icon_url(); ?>" class="armory_char_portrait" alt="Character Portrait" />
		<?php endif; ?>
		<span class="armory_char_name">
			<a href="<?php echo $this->get_profile_url(); ?>"><?php if ($options['show_title'] == 1 && $name = $this->get_name_with_title_text()) :
				echo $name;
			else :
				echo $this->character->name;
			endif; ?>
			</a>
		</span>
		
		<?php if (isset($this->character->guild)) : ?>
		<span class="armory_char_guild"><a href ="<?php echo $this->get_guild_url(); ?>">&lt;<?php echo $this->character->guild->name?>&gt;</a>
			<?php _e('of', 'wow_armory_character')?> <?php echo $this->character->realm; ?>-<?php echo $this->character->region; ?>
		</span>
		<?php endif; ?>
		
		<span class="armory_char_info" >
			<?php _e('Level', 'wow_armory_character')?> 
			<?php echo $this->character->level; ?>
			<?php echo $this->character->race->name; ?>
			<?php echo $this->character->class->name; ?>
		
			<?php if ($options['show_talents'] == 1 && $this->character->talents) : ?>
			<span class="armory_char_spec">
			<?php		$count = 0;
							foreach ($this->character->talents as $talent) :
								$type = ($count == 0) ? 'primary' : 'secondary'; ?>
				<span class="<?php echo (isset($talent->selected) && $talent->selected) ? 'active' : 'inactive'; ?>_spec">
					<img src="<?php echo $this->get_talent_tree_icon_url($talent); ?>" alt="<?php echo $talent->name; ?> talent spec icon" />
					<a href="<?php echo $this->get_talent_url(); ?>/<?php echo $type; ?>"><?php echo $this->get_talent_tree_text($talent); ?></a>	
				</span>
			<?php			$count++;
							endforeach; ?>
			</span>
			<?php	endif; ?>
		</span>
	</div>
	
	<?php if ($options['show_items'] == 1 && $this->character->items) : ?>
	<div class="armory_equip">
		<h4><?php _e('Equipment List', 'wow_armory_character')?></h4>
		<ul class="armory_equip_list">
		<?php		foreach ($this->_slot_table as $slot) : 
							if (isset($this->character->items->$slot) && $item = $this->character->items->$slot) : ?>
			<li><a href="<?php echo $this->get_wowhead_item_url($item->id); ?>" rel="<?php echo $this->get_wowhead_item_rel($item->tooltipParams); ?>"><img src="<?php echo $this->get_item_icon_url($item->icon); ?>" alt="<?php echo $item->id; ?>" class="armory_item_icon" /></a></li>
		<?php 		endif;
						endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
</div>
