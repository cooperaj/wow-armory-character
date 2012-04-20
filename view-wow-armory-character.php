<div id="armory-<?php echo $randNo; ?>" class="armory_display">
	<div class="armory_character">
<?php if ($options['show_portrait'] == 1) : ?>
		<img src="<?php echo $this->get_portrait_icon_url(); ?>" class="armory_char_portrait" alt="Character Portrait" />
<?php endif; ?>
		<span class="armory_char_name">
			<a href="<?php echo $this->get_profile_url(); ?>"><?php if ($options['show_title'] == 1 && $name = $this->get_name_with_title_text()) :
				echo $name;
			else :
				echo $character->name;
			endif; ?>
			</a>
		</span>
		
<?php if (isset($character->guild)) : ?>
		<span class="armory_char_guild"><a href ="<?php echo $this->get_guild_url(); ?>">&lt;<?php echo $character->guild->name?>&gt;</a>
			<?php _e('of', 'wow_armory_character')?> <?php echo $character->realm; ?>-<?php echo $character->region; ?>
		</span>
<?php endif; ?>
		
		<span class="armory_char_info" >
			<?php _e('Level', 'wow_armory_character')?> 
			<?php echo $character->level; ?>
			<?php echo $character->race->name; ?>
			<?php echo $character->class->name; ?>

<?php if ($options['show_talents'] == 1 && $character->talents) : ?>
			<span class="armory_char_spec">
<?php		$count = 0;
				foreach ($character->talents as $talent) :
					$type = ($count == 0) ? 'primary' : 'secondary'; ?>
				<span class="<?php echo (isset($talent->selected) && $talent->selected) ? 'active' : 'inactive'; ?>_spec">
					<a href="<?php echo $this->get_talent_url(); ?>/<?php echo $type; ?>"><img src="<?php echo $this->get_talent_tree_icon_url($talent); ?>" alt="<?php echo $talent->name; ?> talent spec icon" /> <?php echo $this->get_talent_tree_text($talent); ?></a>	
				</span>
<?php			$count++;
				endforeach; ?>
				<br />
			</span>
<?php	endif; ?>
		</span>
	</div>
	
<?php if (($options['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_BADGES) === WoW_Armory_Character_Plugin::STYLE_PROF_BADGES && 
					$character->professions ) : ?>
	<div class="armory_section armory_profession">
		<h4><?php _e('Professions', 'wow_armory_character')?></h4>
		<ul class="armory_profession_list">
<?php		if ($character->professions->primary) :
					$count = 0;
					foreach ($character->professions->primary as $prof) : ?>
			<li class="armory_profession_primary">
				<a href="<?php echo $this->get_profession_url($prof); ?>"><img src="<?php echo $this->get_profession_icon_url($prof); ?>" alt="<?php echo $prof->name; ?> profession icon" /> <?php echo $this->get_profession_badge_text($prof); ?></a>
			</li>
<?php				$count++;
					endforeach; 
				endif; ?>
<?php		if (($options['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY) === WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY &&
						$character->professions->secondary) :
					$count = 0;
					foreach ($character->professions->secondary as $prof) : ?>
			<li class="armory_profession_secondary">
				<a href="<?php echo $this->get_profession_url($prof); ?>"><img src="<?php echo $this->get_profession_icon_url($prof); ?>" alt="<?php echo $prof->name; ?> profession icon" /> <?php echo $this->get_profession_badge_text($prof); ?></a>
			</li>
<?php				$count++;
					endforeach; 
				endif; ?>
		</ul>
		<br />
	</div>
<?php	endif; ?>
	
<?php if ($options['show_items'] == 1 && $character->items) : ?>
	<div class="armory_section armory_equip">
		<h4><?php _e('Equipment List', 'wow_armory_character')?></h4>
		<ul class="armory_equip_list">
<?php		foreach ($this->_slot_table as $slot) : 
					if (isset($character->items->$slot) && $item = $character->items->$slot) : ?>
			<li><a href="<?php echo $this->get_item_url($item->id); ?>" rel="<?php echo $this->get_wowhead_item_rel($item->tooltipParams); ?>"><img src="<?php echo $this->get_item_icon_url($item->icon); ?>" alt="<?php echo $item->id; ?>" class="armory_item_icon" /></a></li>
<?php 		endif;
				endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php if (($options['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR) : 
				$ach_data = $character->get_completed_achievement_data();
?>
	<div class="armory_section armory_achiev_points">
		<h4><?php _e('Achievements', 'wow_armory_character'); ?></h4>
		<div class="bar-wrap" title="<?php printf(__('Completed %1$s out of a possible %2$s achievements earning a total of %3$s achievement points.'), $ach_data->completed, $ach_data->total, $character->achievementPoints); ?>">
			<div class="bar-value" style="width: <?php echo $ach_data->percent_complete; ?>%;">
				<div class="bar-text">
					<span><?php echo $character->achievementPoints; ?></span> &mdash; <?php echo $ach_data->completed; ?>/<?php echo $ach_data->total; ?> (<?php echo $ach_data->percent_complete; ?>%)
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
	
<?php if (($options['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST) : ?>
	<div class="armory_section armory_achiev">
		<h4><?php _e('Recent Achievements', 'wow_armory_character'); ?></h4>
		<ul class="armory_achiev_list">
<?php		foreach ($character->get_latest_achievements(5) as $ach) : // Shows the 5 latest achievements. ?>
			<li>
				<span class="points"><?php echo $ach->points; ?></span>
				<a href="<?php echo $this->get_achievement_url($ach->id, $ach->section->id, $ach->category->id); ?>" rel="<?php echo $this->get_wowhead_achievement_rel($ach->completed);?>"><?php echo $ach->title; ?></a><br/>
				<?php if (($options['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC) echo $ach->description; ?> 
			</li>
<?php 	endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
</div>
