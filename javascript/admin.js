function wac_disable_lang(select_clone, chosen_value)
{
	var valid_options = {
			'US' : ['en_GB', 'es_ES'],
			'EU' : ['en_GB', 'es_ES', 'fr_FR', 'ru_RU', 'de_DE'],
			'KR' : ['ko_KR'],
			'TW' : ['zh_TW']
	};
	
	//reset the second select on each change
	jQuery('select.wa-lang').html(select_clone.html())
	
	var valid = valid_options[chosen_value];
	jQuery("select.wa-lang option").each(function() {
		if (jQuery.inArray(jQuery(this).val(), valid) == -1)
		{
			jQuery(this).remove();
		}
	});
}

jQuery(document).ready(function() {
	//copy the lang select, so we can easily reset it
    var select_clone = jQuery('select.wa-lang').clone();
    
    // setup the initial options
    var start_val = jQuery('select.wa-region').val();
    wac_disable_lang(select_clone, start_val);
    
    // and an onchange event to keep things pucka.
    jQuery('select.wa-region').change(function() {
        var val = jQuery(this).val();
        wac_disable_lang(select_clone, val);
    });
});