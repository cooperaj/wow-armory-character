<div class="wow_armory_options">
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e(
			'Title:',
			'wow_armory_character'
		); ?></label><br/>
	<input type="text" class="wa-title widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
	       name="<?php echo $this->get_field_name( 'title' ); ?>"
	       value="<?php echo esc_attr( $instance['title'] ); ?>"/>
	<small><?php _e( 'Use %NAME% for the character\'s name.', 'wow_armory_character' ); ?></small>
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e(
			'Character name:',
			'wow_armory_character'
		); ?></label><br/>
	<input type="text" class="wa-name widefat" id="<?php echo $this->get_field_id( 'name' ); ?>"
	       name="<?php echo $this->get_field_name( 'name' ); ?>"
	       value="<?php echo esc_attr( $instance['name'] ); ?>"/>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'realm' ); ?>"><?php echo __(
			'Realm:',
			'wow_armory_character'
		); ?></label><br/>
	<select class="wa-region" id="<?php echo $this->get_field_id( 'region' ); ?>"
	        name="<?php echo $this->get_field_name( 'region' ); ?>">
		<option value="US"<?php selected( $instance['region'], 'US', true ); ?>>US</option>
		<option value="EU"<?php selected( $instance['region'], 'EU', true ); ?>>EU</option>
		<option value="KR"<?php selected( $instance['region'], 'KR', true ); ?>>KR</option>
		<option value="TW"<?php selected( $instance['region'], 'TW', true ); ?>>TW</option>
	</select>
	<select class="wa-realm" style="width: 150px" id="<?php echo $this->get_field_id( 'realm' ); ?>"
	        name="<?php echo $this->get_field_name( 'realm' ); ?>">
		<?php echo $this->get_realms_options( $instance['region'], $instance['realm'] ); ?>
	</select>
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'locale' ); ?>"><?php echo __(
			'Language:',
			'wow_armory_character'
		); ?></label><br/>
	<select class="wa-lang widefat" id="<?php echo $this->get_field_id( 'locale' ); ?>"
	        name="<?php echo $this->get_field_name( 'locale' ); ?>">
		<option value="en_GB"<?php selected( $instance['locale'], 'en_GB', true ); ?>><?php _e(
				'English (EU)',
				'wow_armory_character'
			); ?></option>
		<option value="en_US"<?php selected( $instance['locale'], 'en_US', true ); ?>><?php _e(
				'English (US)',
				'wow_armory_character'
			); ?></option>
		<option value="de_DE"<?php selected( $instance['locale'], 'de_DE', true ); ?>><?php _e(
				'Deutsch',
				'wow_armory_character'
			); ?></option>
		<option value="es_ES"<?php selected( $instance['locale'], 'es_ES', true ); ?>><?php _e(
				'Español (EU)',
				'wow_armory_character'
			); ?></option>
		<option value="es_MX"<?php selected( $instance['locale'], 'es_MX', true ); ?>><?php _e(
				'Español (AL)',
				'wow_armory_character'
			); ?></option>
		<option value="fr_FR"<?php selected( $instance['locale'], 'fr_FR', true ); ?>><?php _e(
				'Française',
				'wow_armory_character'
			); ?></option>
		<option value="it_IT"<?php selected( $instance['locale'], 'it_IT', true ); ?>><?php _e(
				'Italiano',
				'wow_armory_character'
			); ?></option>
		<option value="pt_PT"<?php selected( $instance['locale'], 'pt_PT', true ); ?>><?php _e(
				'Português (EU)',
				'wow_armory_character'
			); ?></option>
		<option value="pt_BR"<?php selected( $instance['locale'], 'pt_BR', true ); ?>><?php _e(
				'Português (AL)',
				'wow_armory_character'
			); ?></option>
		<option value="ru_RU"<?php selected( $instance['locale'], 'ru_RU', true ); ?>><?php _e(
				'Pусский',
				'wow_armory_character'
			); ?></option>
		<option value="ko_KR"<?php selected( $instance['locale'], 'ko_KR', true ); ?>><?php _e(
				'한국의',
				'wow_armory_character'
			); ?></option>
		<option value="zh_TW"<?php selected( $instance['locale'], 'zh_TW', true ); ?>><?php _e(
				'中國',
				'wow_armory_character'
			); ?></option>
	</select>
</p>
<h4><?php _e( 'Display Options', 'wow_armory_character' ); ?></h4>

<p>
	<input id="<?php echo $this->get_field_id( 'show_portrait' ); ?>"
	       name="<?php echo $this->get_field_name( 'show_portrait' ); ?>" value="1"
	       type="checkbox" <?php echo $instance['show_portrait'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id( 'show_portrait' ); ?>"><?php _e(
			'Show Portrait',
			'wow_armory_character'
		); ?></label><br/>

	<input id="<?php echo $this->get_field_id( 'show_title' ); ?>"
	       name="<?php echo $this->get_field_name( 'show_title' ); ?>" value="1"
	       type="checkbox" <?php echo $instance['show_title'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e(
			'Show Title',
			'wow_armory_character'
		); ?></label><br/>

	<input id="<?php echo $this->get_field_id( 'show_talents' ); ?>"
	       name="<?php echo $this->get_field_name( 'show_talents' ); ?>" value="1"
	       type="checkbox" <?php echo $instance['show_talents'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id( 'show_talents' ); ?>"><?php _e(
			'Show Talents',
			'wow_armory_character'
		); ?></label><br/>

	<input id="<?php echo $this->get_field_id( 'show_items' ); ?>"
	       name="<?php echo $this->get_field_name( 'show_items' ); ?>" value="1"
	       type="checkbox" <?php echo $instance['show_items'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id( 'show_items' ); ?>"><?php _e(
			'Show Items',
			'wow_armory_character'
		); ?></label><br/>

	<input id="<?php echo $this->get_field_id( 'show_profs' ); ?>"
	       name="<?php echo $this->get_field_name( 'show_profs' ); ?>" value="1"
	       type="checkbox" <?php echo $instance['show_profs'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id( 'show_profs' ); ?>"><?php _e(
			'Show Professions',
			'wow_armory_character'
		); ?></label><br/>
        <span class="sub_options<?php echo ! $instance['show_profs'] ? ' sub_options_hidden' : ''; ?>"
              rel="<?php echo $this->get_field_id( 'show_profs' ); ?>">
            <input id="<?php echo $this->get_field_id( 'show_profs_badges' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_profs_badges' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_PROF_BADGES; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_BADGES ) === WoW_Armory_Character_Plugin::STYLE_PROF_BADGES ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_profs_badges' ); ?>" title="<?php _e(
	            'Show your professions as a series of badges.',
	            'wow_armory_character'
            ); ?>"><?php _e( 'Badges', 'wow_armory_character' ); ?></label><br/>
            <input id="<?php echo $this->get_field_id( 'show_profs_secondary' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_profs_secondary' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY ) === WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_profs_secondary' ); ?>" title="<?php _e(
	            'Show your secondary professions (e.g. fishing, first aid).',
	            'wow_armory_character'
            ); ?>"><?php _e( 'Secondary Professions', 'wow_armory_character' ); ?></label><br/>
        </span>

	<input id="<?php echo $this->get_field_id( 'show_achievs' ); ?>"
	       name="<?php echo $this->get_field_name( 'show_achievs' ); ?>" value="1"
	       type="checkbox" <?php echo $instance['show_achievs'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id( 'show_achievs' ); ?>"><?php _e(
			'Show Achievements',
			'wow_armory_character'
		); ?></label><br/>
        <span class="sub_options<?php echo ! $instance['show_achievs'] ? ' sub_options_hidden' : ''; ?>"
              rel="<?php echo $this->get_field_id( 'show_achievs' ); ?>">
            <input id="<?php echo $this->get_field_id( 'show_achievs_bar' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_achievs_bar' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR ) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_achievs_list' ); ?>" title="<?php _e(
	            'Show a progress bar indicating the number of achievements this character has gained from the total.',
	            'wow_armory_character'
            ); ?>"><?php _e( 'Completion Bar', 'wow_armory_character' ); ?></label><br/>
            <input id="<?php echo $this->get_field_id( 'show_achievs_list' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_achievs_list' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST ) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_achievs_list' ); ?>" title="<?php _e(
	            'Show a listing of the characters most recent achievements.',
	            'wow_armory_character'
            ); ?>"><?php _e( 'Recent Achievements', 'wow_armory_character' ); ?></label><br/>
            <input id="<?php echo $this->get_field_id( 'show_achievs_list_desc' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_achievs_list_desc' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC ) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_achievs_list_desc' ); ?>" title="<?php _e(
	            'Show the achievement description text when showing the Recent Achievements listing.',
	            'wow_armory_character'
            ); ?>"><?php _e( 'Achievement Descriptions', 'wow_armory_character' ); ?></label>
        </span>

	<input id="<?php echo $this->get_field_id( 'show_feed' ); ?>"
	       name="<?php echo $this->get_field_name( 'show_feed' ); ?>" value="1"
	       type="checkbox" <?php echo $instance['show_feed'] ? 'checked="checked"' : ''; ?> />
	<label for="<?php echo $this->get_field_id( 'show_feed' ); ?>"><?php _e(
			'Show Activity Feed',
			'wow_armory_character'
		); ?></label><br/>
        <span class="sub_options<?php echo ! $instance['show_feed'] ? ' sub_options_hidden' : ''; ?>"
              rel="<?php echo $this->get_field_id( 'show_feed' ); ?>">
            <input id="<?php echo $this->get_field_id( 'show_feed_items' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_feed_items' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_FEED_ITEMS; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_feed'] & WoW_Armory_Character_Plugin::STYLE_FEED_ITEMS ) === WoW_Armory_Character_Plugin::STYLE_FEED_ITEMS ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_feed_items' ); ?>"
                   title="<?php _e( 'Show loot/items you have received.', 'wow_armory_character' ); ?>"><?php _e(
		            'Show Items',
		            'wow_armory_character'
	            ); ?></label><br/>
            <input id="<?php echo $this->get_field_id( 'show_feed_achievs' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_feed_achievs' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_FEED_ACHIEVEMENTS; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_feed'] & WoW_Armory_Character_Plugin::STYLE_FEED_ACHIEVEMENTS ) === WoW_Armory_Character_Plugin::STYLE_FEED_ACHIEVEMENTS ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_feed_achievs' ); ?>"
                   title="<?php _e( 'Show achievements you have earned.', 'wow_armory_character' ); ?>"><?php _e(
		            'Show Achievements',
		            'wow_armory_character'
	            ); ?></label><br/>
            <input id="<?php echo $this->get_field_id( 'show_feed_criteria' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_feed_criteria' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_FEED_CRITERIA; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_feed'] & WoW_Armory_Character_Plugin::STYLE_FEED_CRITERIA ) === WoW_Armory_Character_Plugin::STYLE_FEED_CRITERIA ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_feed_criteria' ); ?>" title="<?php _e(
	            'Show achievement criteria you have earned.',
	            'wow_armory_character'
            ); ?>"><?php _e( 'Show Achievement Criteria', 'wow_armory_character' ); ?></label><br/>
            <input id="<?php echo $this->get_field_id( 'show_feed_icons' ); ?>"
                   name="<?php echo $this->get_field_name( 'show_feed_icons' ); ?>"
                   value="<?php echo WoW_Armory_Character_Plugin::STYLE_FEED_ICONS; ?>"
                   type="checkbox"
	            <?php echo ( ( $instance['show_feed'] & WoW_Armory_Character_Plugin::STYLE_FEED_ICONS ) === WoW_Armory_Character_Plugin::STYLE_FEED_ICONS ) ? 'checked="checked"' : ''; ?>
	            />
            <label for="<?php echo $this->get_field_id( 'show_feed_icons' ); ?>" title="<?php _e(
	            'Show icons on each of the above items. Criteria will show a tick.',
	            'wow_armory_character'
            ); ?>"><?php _e( 'Show Icons', 'wow_armory_character' ); ?></label>
        </span>
</p>
</div>
