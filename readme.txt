=== WoW Armory Character ===
Contributors: blueajcooper
Tags: widget, world of warcraft, wow, armory, character, warcraft, blizzard, toon, gear, achievements, professions
Requires at least: 3.5.0
Tested up to: 4.0
Stable tag: 1.1.1
License: GPLv2

Pulls character information from the WoW community API and displays it.

== Description ==

This plugin displays World of Warcraft character profiles pulled from data made available by the WoW community API.
It allows you to view a basic profile that shows your character information and picture. It can be easily configured to 
show the gear that you are wearing (with optional links to [wowhead](http://wowhead.com)), your talents, professions,
activity feed and recent achievements.

To see a customised example of the plugin in action [check it out on my wordpress blog](http://realmenweardress.es/about/).
For an example of the plugin configured to show all the available information check out the screenshots tab.

= Known Issues =

* When showing characters with Chinese or Korean locales the wowhead tooltips will show in english. I'm actively 
  seeking a solution.
* The widget configuration will keep reseting your language choice to English. When making changes be sure to first
  re-choose your region and then choose your language if you do not wish english to be selected.
* Multiple Armory widgets on one page will cause issue with the administration pages javascript. For the time being I
  recommend using shortcodes if you need more then one character displayed on a page.

== Translation ==

The plugin is fully setup to use the excellent internationalisation features of wordpress. However, since I sadly do not
speak any of the languages I'd like to support (those used by World of Warcraft itself) I am not able to provide the
translations I need. If you are interested in providing any translations for this project please contact me.

== With Thanks ==

Many thanks to seifertim and his effort [WoW Armory](http://wordpress.org/extend/plugins/wow-armory/) for providing the
inspiration (and base upon which to work).

== Installation ==

1. Upload 'wow-armory-character' to the `/wp-content/plugins/` directory
1. Ensure your webserver has write permissions to the `/wp-content/plugins/wow-armory-character/cache` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure a widget and add it to your site or use the `[armory-character]` shortcode.

= Shortcode Use =

The shortcode can be added to any page or post and can be configured in an identical fashion to the
widget. At a minimum you will need to specify the realm and character name for it to work - though this will assume
that you wish to show all the details of character from the EU region in English.

    [armory-character realm="Terokkar" name="Grokknar"]


The configuration parameters available to use are:

* **region** - *EU*, *US*, *KO* and *TW* currently supported
* **show_portrait** - *1* or *0*
* **show_title** - *1* or *0*
* **show_talents** - *1* or *0*
* **show_items** - *1* or *0*
* **show_profs** - This is a bitwise field. To get the setting you want add together the numbers below.
    * *1* - Show profession badges
    * *2* - Show secondary professions
* **show_achievs** - This is a bitwise field. To get the setting you want add together the numbers below.
    * *1* - Show achievement bar
    * *2* - Show achievement list
    * *4* - Show descriptions on the achievement list.
* **show_feed** - This is a bitwise field. To get the setting you want add together the numbers below.
    * *1* - Show items (loot).
    * *2* - Show acheivements gained.
    * *4* - Show acheivement criteria.
    * *8* - Show icons next to each item.
* **locale** - *en_GB*, *en_US*, *de_DE*, *es_ES*, *es_MX*, *fr_FR*, *it_IT*, *ru_RU*, *ko_KR*, *zh_TW* or *zh_CN*

Note: Certain locale choices are only available when selecting some regions.

= Available Filters =

For more capable/adventurous developers there are a number of hooks that allow you to change the way the
plugin functions without altering its code. This means you get to upgrade in the future without worrying
about breaking any changes you may have made.

**wow-armory-character-template**  
Allows you to specify an alternate template file to use to display your profile. Defaults to `view-wow-armory-character.php`

    function my_function_name($file_path) {
      // e.g. return realpath(__FILE__) . '/view-wow-armory-character.php'
      return "THE_PATH_TO_YOUR_TEMPLATE_FILE";
    }
    add_filter('wow-armory-character-template','my_function_name');


**wow-armory-character-display**  
Your profile once the template has been processed. It may be quicker to alter the display at runtime rather 
than duplicate the template when making only minor changes. As well as the output that will be displayed 
the Character data as retireved from the Community API is also passed. This should allow you to make any changes
you need.

    function my_function_name($output, $character_data) {
      // Do something to the $output, perhaps using the $character_data
      return $output;
    }
    add_filter('wow-armory-character-display','my_function_name');

**wow-armory-character-css**
Allows you to specify an alternate stylesheet. This is the replacement for the tickbox provided in the administration
screen. You should now consider that option as deprecated and it will be removed in a future release.
The rationale behind this is that if you're overriding the css completely you will have access to your theme to be able
to add this hook.

    function my_function_name($file_path) {
      // e.g. return realpath(__FILE__) . '/wowcss.css'
      return "THE_PATH_TO_YOUR_CSS_FILE";
    }
    add_filter('wow-armory-character-css','my_function_name');

== Frequently Asked Questions ==

= The plugin is displaying old information. How do I update it? =

The plugin will cache the characters it retrieves from the community API for 12 hours. This ensures that 
your website will make no more then 2 requests per character in any 24 hour period. In order to force your character
display to update you are able to clear the cache.

1. Navigate to the *Armory Character* section within the *Settings* area of your wordpress admin area.
1. Tick the checkbox next to the character you wish to refresh and click the *Clear selected cache items* button.

When you next look at your character fresh information will be pulled from the community API. It may be that the 
community API is returning old information, in this case you will need to wait until your armory page is updated.

= How do I change the styling of the character display =

There are two ways you can do this. You can use a pure CSS based approach or alter the html output using either of
the two display filters that have been made available. 

To use the filters please create the necessary function (perhaps in your template.php file) and alter the html as
you see fit. You can either alter the html after it has been created or you can override the template that produces
the output.

The CSS can be overridden by using the global setting 'Add plugin css to the page'. Unticking this box will allow
you to provide your own CSS as part of your theme. _NOTE_ This option is now deprecated. It will be replaced in a future
release by the filter documented in the installation tab. For simple edits I suggest you add the necessary tweaks to
your themes CSS and keep the basic styling the plugin provides.

== Screenshots ==

1. The default widget output
2. The widget configuration screen.

== Changelog ==

= 1.1.1 =
* Updated the wowhead tooltips to work with the new way of handling item ID's.

= 1.1.0 =
* Allow the clearing of all the cached items. Possible fix for the issue with acheivements reported by SilverCha0s
* Include Italian language configuration options.

= 1.0.0 =
* Starting at the big 1.0. Is stable enough for that I reckon.
* Tooltip updates to show item upgrades/reforges.
* Reworked achievements to calculate based on API - not using a hardcoded value.
* Fixed a large number of bugs
* Styling tweaks to work better in Twenty Fourteen
* The activity feed for your character can now be shown. This is just like the one from your armory page and shows
  the last 5 items, achievements and achievement criteria you gained. PLEASE NOTE: If you're upgrading to this release
  your widgets may give errors until their settings are adjusted.
* Realms are now chosen from a dropdown list rather then typed. This should help where the slug varies from the
  realm name.
* _DEPRECATED_ setting for css inclusion. A filter is now available for theme authors.
* Moved to grunt for building.
* New banner!

= 0.9.6 =
* Update for patch 5.0.4 talent changes.
* Achievement count update
* Include Portugese language configuration options.

= 0.9.5 =
* New global settings introduced. Including the ability to disable the built in stylesheet and wowhead integration.
* Professions now show.
* Shoulder slot items now show correctly. Thanks to xsherbearx for reporting the issue.
* Numerous other bug fixes and code cleanups.

= 0.9.4 =
* Achievements are now available to be shown.
* New filter added to allow you to change the template file location.

= 0.9.3 =
* Fixed usages of __DIR__ which is PHP5.3 only. The plugin should now work on PHP5.2+ Thanks to @Flavio_Torelli
  for reporting the issue.

= 0.9.2 =
* Retagging since I messed it up. Give us a break it's my first plugin :)

= 0.9.1 =
* Changed file address from __FILE__ since it does not work well with symlinks
* Enforced correct language choices per region
* Fixed realms with spaces in their names
* Fixed some images not working in a non-english locale
* Added KR and TW regions

= 0.9 =
* The initial release of the plugin.

== Upgrade Notice ==

= 0.9.7 =
Your activity feed can now be shown - complete with icons - just like your armory page. Please resave your
widget settings afterwards to ensure they work.

= 0.9.6 =
This update makes changes to how the talents are displayed due to the changes to talents in 5.0.4

= 0.9.5 =
Profession information can now be show as a number of badges and new global settings have been introduced
to help with theming.

= 0.9.4 =
You can now show your characters achievements. For easier theming you can now specify your own
template file to render the profile.

= 0.9.3 =
Prior to this version the plugin was non-functional on anything less then PHP 5.3. If your running 5.2
then it should now work.

= 0.9.1 =
Numerous bug fixes plus the addition of support for the KR and TW regions.

== Development Version ==

The development version, as well as all the tagged releases of this project, are hosted on 
[github.com](https://github.com/cooperaj/wow-armory-character) and merely pushed to 
the Wordpress plugin repository for distribution purposes. 

Should you need to use the development version (perhaps to fix a bug, or test a feature) please 
download it from the [github website](https://github.com/cooperaj/wow-armory-character/zipball/master).

I also maintain a more  comprehensive issue/bug list at github so if you have anything to report it would 
be very helpful if you could post the problem there.
