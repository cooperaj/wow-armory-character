=== WoW Armory Character ===
Contributors: blueajcooper
Tags: widget, world of warcraft, wow, armory, character, warcraft, blizzard, toon, gear, achievements, professions
Requires at least: 3.0.0
Tested up to: 3.2.1
Stable tag: 0.9
License: GPLv2

Pulls character information from the WoW community API and displays it.

== Description ==

This plugin displays World of Warcraft character profiles pulled from data made available by the WoW community API.
It allows you to view a basic profile that shows your character information and picture. It can be easily configured to 
show the gear that you are wearing (with links to [wowhead](http://wowhead.com)), your talents, professions and recent
achievements.

To see an example of the plugin in action [check it out on my wordpress blog](http://realmenweardress.es/about/).

= Please Note =

This is a ground up rewrite of seifertim's effort [WoW Armory](http://wordpress.org/extend/plugins/wow-armory/). The 
API that was used by the WoW Armory plugin will be discontinued on the 12th of August and so it will cease to function.
I've tried to keep as much of the display markup as possible so that people migrating from WoW Armory will not 
have to redo any theming they have carried out - however, there are some minor changes so it may not be a 100% fit.

I have not replicated all the functionality that was offered by WoW Armory and have instead concentrated on getting it 
working well for how I use it. Consequently you are unable to view a 3D view of your character or see achievements or
professions (though these are planned).

= Known Issues =

* Professions do not show
* Achievements do not show

== Installation ==

1. Upload 'wow-armory-character' to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure a widget and add it to your site or use the `[armory-character]` shortcode.

= Shortcode Use =

The shortcode can be added to any page or post and can be configured in an identical fashion to the 
widget. At a minimum you will need to specify the realm and character name for it to work - though this will assume
that you wish to show all the details of character from the EU region in English.

`[armory-character realm="Terokkar" name="Grokknar"]`

The configuration parameters available to use are:

* **region** - *EU* and *US* currently supported
* **show_portrait** - *1* or *0*
* **show_title** - *1* or *0*
* **show_talents** - *1* or *0*
* **show_items** - *1* or *0*
* **show_profs** - *1* or *0*
* **show_achievs** - *1* or *0*
* **locale** - *en_GB*, *en_US*, *de_DE*, *es_ES*, *es_MX*, *fr_FR*, *ru_RU*, *ko_KR*, *zh_TW* or *zh_CN*

== Frequently Asked Questions ==

= The plugin is displaying old information. How do I update it? =

The plugin will cache the characters it retrieves from the community API for 12 hours. This ensures that 
your website will make no more then 2 requests in any 24 hour period. In order to force your character display 
to update you are able to clear the cache.

1. Navigate to the *Character Cache* section within the *Settings* area of your wordpress admin area.
1. Tick the checkbox next to the character you wish to refresh and click the *Clear Cache* button.

When you next look at your character fresh information will be pulled from the community API. It may be that the 
community API is returning old information, in this case you will need to wait until your armory page is updated.

== Screenshots ==

1. The default widget output
2. The widget configuration screen.

== Changelog ==

= 0.9 =
* The initial release of the plugin.

