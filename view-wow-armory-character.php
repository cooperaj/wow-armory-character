<div id="armory-<?php echo $randNo; ?>" class="armory_display">
<div class="armory_section armory_character">
	<?php if ( $options['show_portrait'] == 1 ) : ?>
		<img src="<?php echo $this->get_portrait_icon_url(); ?>" class="armory_char_portrait"
		     alt="Character Portrait"/>
	<?php endif; ?>
	<span class="armory_char_name">
            <a href="<?php echo $this->get_profile_url(); ?>"><?php if ( $options['show_title'] == 1 && $name = $this->get_name_with_title_text() ) :
		            echo $name;
	            else :
		            echo $character->name;
	            endif; ?>
            </a>
        </span>

	<?php if ( isset( $character->guild ) ) : ?>
		<span class="armory_char_guild"><a href="<?php echo $this->get_guild_url(); ?>">
				&lt;<?php echo $character->guild->name ?>&gt;</a>
			<?php _e( 'of', 'wow_armory_character' ) ?> <?php echo $character->realm; ?>
			-<?php echo $character->region; ?>
            </span>
	<?php endif; ?>

	<span class="armory_char_info">
            <?php _e( 'Level', 'wow_armory_character' ) ?>
		<?php echo $character->level; ?>
		<?php echo $character->race->name; ?>
		<?php echo $character->class->name; ?>

		<?php if ( $options['show_talents'] == 1 && $character->talents ) : ?>
			<span class="armory_char_spec">
                <?php $count = 0;
                foreach ( $character->talents as $talent ) :
	                if ( $talent->calcSpec != "" ) : ?>
		                <span
			                class="<?php echo ( isset( $talent->selected ) && $talent->selected ) ? 'active' : 'inactive'; ?>_spec">
                                    <a href="<?php echo $this->get_talent_url( $talent ); ?>"><img
		                                    src="<?php echo $this->get_talent_tree_icon_url( $talent ); ?>"
		                                    alt="<?php echo $talent->spec->name; ?> talent spec icon"/> <?php echo $this->get_talent_tree_text( $talent ); ?>
                                    </a>
                                </span>

	                <?php endif;
	                $count ++;
                endforeach; ?>
				<br/>
                </span>
		<?php endif; ?>
        </span>
</div>

<?php if ( ( ( $options['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_BADGES ) === WoW_Armory_Character_Plugin::STYLE_PROF_BADGES )
           && $character->professions
) : ?>
	<div class="armory_section armory_profession">
		<h4><?php _e( 'Professions', 'wow_armory_character' ) ?></h4>
		<ul class="armory_profession_badges">
			<?php if ( $character->professions->primary ) :
				$count = 0;
				foreach ( $character->professions->primary as $prof ) : ?>
					<li class="armory_profession_primary">
						<a href="<?php echo $this->get_profession_url( $prof ); ?>"><img
								src="<?php echo $this->get_profession_icon_url( $prof ); ?>"
								alt="<?php echo $prof->name; ?> profession icon"/> <?php echo $this->get_profession_badge_text( $prof ); ?>
						</a>
					</li>
					<?php $count ++;
				endforeach;
			endif; ?>
			<?php if ( ( $options['show_profs'] & WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY ) === WoW_Armory_Character_Plugin::STYLE_PROF_SECONDARY &&
			           $character->professions->secondary
			) :
				$count = 0;
				foreach ( $character->professions->secondary as $prof ) : ?>
					<li class="armory_profession_secondary">
						<a href="<?php echo $this->get_profession_url( $prof ); ?>"><img
								src="<?php echo $this->get_profession_icon_url( $prof ); ?>"
								alt="<?php echo $prof->name; ?> profession icon"/> <?php echo $this->get_profession_badge_text( $prof ); ?>
						</a>
					</li>
					<?php $count ++;
				endforeach;
			endif; ?>
		</ul>
		<br/>
	</div>
<?php endif; ?>

<?php if ( $options['show_items'] == 1 && $character->items ) : ?>
	<div class="armory_section armory_equip">
		<h4><?php _e( 'Equipment List', 'wow_armory_character' ) ?></h4>
		<ul class="armory_equip_list"><?php foreach ( $this->_slot_table as $slot ) :
				if ( isset( $character->items->$slot ) && $item = $character->items->$slot ) :
					?>
					<li><a href="<?php echo $this->get_item_url( $item->id ); ?>"
					       rel="<?php echo $this->get_wowhead_item_rel( $item->tooltipParams, $item->bonusLists ); ?>"><img
							src="<?php echo $this->get_item_icon_url( $item->icon ); ?>"
							alt="<?php echo $item->id; ?>" class="armory_item_icon"/></a></li><?php
				endif;
			endforeach; ?></ul>
	</div>
<?php endif; ?>

<?php if ( ( $options['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR ) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_BAR ) :
	$ach_data = $character->get_completed_achievement_data();
	if ( ! is_null( $ach_data ) ) : ?>
		<div class="armory_section armory_achiev_points">
			<h4><?php _e( 'Achievements', 'wow_armory_character' ); ?></h4>

			<div class="bar-wrap"
			     title="<?php printf( __( 'Completed %1$s out of a possible %2$s achievements earning a total of %3$s achievement points.' ),
				     $ach_data->completed,
				     $ach_data->total,
				     $character->achievementPoints ); ?>">
				<div class="bar-value" style="width: <?php echo $ach_data->percent_complete; ?>%;">
					<div class="bar-text">
						<span><?php echo $character->achievementPoints; ?></span> &mdash; <?php echo $ach_data->completed; ?>
						/<?php echo $ach_data->total; ?> (<?php echo $ach_data->percent_complete; ?>%)
					</div>
				</div>
			</div>
		</div>
	<?php endif;
endif; ?>

<?php if ( ( $options['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST ) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST ) : ?>
	<div class="armory_section armory_achiev">
		<h4><?php _e( 'Recent Achievements', 'wow_armory_character' ); ?></h4>
		<ul class="armory_achiev_list">
			<?php foreach ( $character->get_latest_achievements( 5 ) as $ach ) : // Shows the 5 latest achievements. ?>
				<li>
					<span class="points"><?php echo $ach->points; ?></span>
					<a href="<?php echo $this->get_achievement_url( $ach->id,
						$ach->section->id,
						$ach->category->id ); ?>"
					   rel="<?php echo $this->get_wowhead_achievement_rel( $ach->completed ); ?>"><?php echo $ach->title; ?></a><br/>
					<?php if ( ( $options['show_achievs'] & WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC ) === WoW_Armory_Character_Plugin::STYLE_ACHIEV_LIST_DESC ) {
						echo $ach->description;
					} ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php if ( $options['show_feed'] ) : ?>
	<div class="armory_section armory_feed">
		<h4><?php _e( 'Recent Activity', 'wow_armory_character' ); ?></h4>
		<ul class="armory_feed_list">
			<?php $feed_icons = ( $options['show_feed'] & WoW_Armory_Character_Plugin::STYLE_FEED_ICONS ) === WoW_Armory_Character_Plugin::STYLE_FEED_ICONS;
			$feed_achiev      = ( $options['show_feed'] & WoW_Armory_Character_Plugin::STYLE_FEED_ACHIEVEMENTS ) === WoW_Armory_Character_Plugin::STYLE_FEED_ACHIEVEMENTS;
			$feed_criteria    = ( $options['show_feed'] & WoW_Armory_Character_Plugin::STYLE_FEED_CRITERIA ) === WoW_Armory_Character_Plugin::STYLE_FEED_CRITERIA;
			$feed_loot        = ( $options['show_feed'] & WoW_Armory_Character_Plugin::STYLE_FEED_ITEMS ) === WoW_Armory_Character_Plugin::STYLE_FEED_ITEMS;

			$feed_items = $character->get_activity_feed_items( 5 ); // return a max of 5 of each type.
			for ( $i = 0; $i < ( count( $feed_items ) < 5 ? count( $feed_items ) : 5 ); $i ++ ) : // and only print five or less
			{
				switch ( $feed_items[ $i ]->type ) {
					case WoW_Armory_Character_FeedItem::ITEM_ACHIEVEMENT :
						if ( $feed_achiev ) :
							$url_parts = $feed_items[ $i ]->get_item_url_components();
							if ( ! is_null( $url_parts ) ) : ?>
								<li>
									<?php if ( $feed_icons ) : ?>
										<a href="<?php echo $this->get_achievement_url( $url_parts['id'],
											$url_parts['section'],
											$url_parts['category'] ); ?>">
											<img class="armory_feed_icon achievement"
											     src="<?php echo $this->get_feed_icon_url( $feed_items[ $i ]->get_item_icon() ) ?>"/>
										</a>
									<?php endif; ?>
									<p><?php printf( __( 'Earned the Achievement %s for %d points.',
												'wow_armory_character' ),
											sprintf( '<a href="%s">%s</a>',
												$this->get_achievement_url( $url_parts['id'],
													$url_parts['section'],
													$url_parts['category'] ),
												$feed_items[ $i ]->get_item_title() ),
											$feed_items[ $i ]->get_item_related() ); ?> <span
											class="timeago"><?php echo $this->get_fuzzy_time( $feed_items[ $i ]->timestamp ); ?></span>
									</p>
								</li>
							<?php endif;
						endif;
						break;
					case WoW_Armory_Character_FeedItem::ITEM_CRITERIA :
						if ( $feed_criteria ) :
							$url_parts = $feed_items[ $i ]->get_item_url_components();
							if ( ! is_null( $url_parts ) ) : ?>
								<li>
									<?php if ( $feed_icons ) : ?>
										<a href="<?php echo $this->get_achievement_url( $url_parts['id'],
											$url_parts['section'],
											$url_parts['category'] ); ?>">
											<img class="armory_feed_icon criteria"
											     src="<?php echo WP_PLUGIN_URL ?>/wow-armory-character/images/check.png"/>
										</a>
									<?php endif; ?>
									<p><?php printf( __( 'Completed step %1$s of achievement %2$s.',
												'wow_armory_character' ),
											'<span class="criteria_step">' . $feed_items[ $i ]->get_item_title() . '</span>',
											sprintf( '<a href="%s">%s</a>',
												$this->get_achievement_url( $url_parts['id'],
													$url_parts['section'],
													$url_parts['category'] ),
												$feed_items[ $i ]->get_item_related() ) ); ?>
										<span
											class="timeago"><?php echo $this->get_fuzzy_time( $feed_items[ $i ]->timestamp ); ?></span>
									</p>
								</li>
							<?php endif;
						endif;
						break;
					case WoW_Armory_Character_FeedItem::ITEM_LOOT :
						if ( $feed_loot ) :
							$url_parts = $feed_items[ $i ]->get_item_url_components(); ?>
							<li>
								<?php if ( $feed_icons ) : ?>
									<a href="<?php echo $this->get_item_url( $url_parts['id'] ); ?>">
										<img class="armory_feed_icon item"
										     src="<?php echo $this->get_feed_icon_url( $feed_items[ $i ]->get_item_icon() ) ?>"/>
									</a>
								<?php endif; ?>
								<p><?php printf( __( 'Obtained %s.', 'wow_armory_character' ),
										sprintf( '<a href="%s">%s</a>',
											$this->get_item_url( $url_parts['id'] ),
											$feed_items[ $i ]->get_item_title() ) ); ?>
									<span
										class="timeago"><?php echo $this->get_fuzzy_time( $feed_items[ $i ]->timestamp ); ?></span>
								</p>
							</li>
						<?php endif;
						break;
				}
			}
			endfor; ?>
		</ul>
	</div>
<?php endif; ?>
</div>
