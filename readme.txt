=== WoW Armory Character ===
Contributors: blueajcooper
Tags: widget, world of warcraft, wow, armory, character, warcraft, blizzard, toon, gear, achievements, professions
Requires at least: 3.0.0
Tested up to: 3.4.2
Stable tag: 0.9.6
License: GPLv2

Pulls character information from the WoW community API and displays it.

== Description ==

This plugin displays World of Warcraft character profiles pulled from data made available by the WoW community API.
It allows you to view a basic profile that shows your character information and picture. It can be easily configured to 
show the gear that you are wearing (with optional links to [wowhead](http://wowhead.com)), your talents, professions and
recent achievements.

To see an example of the plugin in action [check it out on my wordpress blog](http://realmenweardress.es/about/).

= Please Note =

This is a ground up rewrite of seifertim's effort [WoW Armory](http://wordpress.org/extend/plugins/wow-armory/). The 
API that was used by the WoW Armory plugin was discontinued on the 12th of August 2011 and so the plugin has ceased to
function. I've tried to keep as much of the display markup as possible so that people migrating from WoW Armory will not 
have to redo any theming they have carried out - however, there are some minor changes so it may not be a 100% fit.

I have not replicated all the functionality that was offered by WoW Armory and have instead concentrated on getting it 
working well for how I use it. Consequently you are unable to view a 3D view of your character.

= Known Issues =

* When showing characters with Chinese or Korean locales the wowhead tooltips will show in english. I'm actively 
  seeking a solution.
* The widget configuration will keep reseting your language choice to English. When making changes be sure to first
  re-choose your region and then choose your language if you do not wish english to be selected.
* Profession completion bars do not show when configured to.

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
* **show_profs** - This is a bitwise field. To get the setting you want use add together the numbers below.
    * *1* - Show profession badges
    * *2* - Show profession completion bars
    * *4* - Show seconday professions
* **show_achievs** - This is a bitwise field. To get the setting you want use add together the numbers below.
    * *1* - Show achievement bar
    * *2* - Show achievement list
    * *4* - Show descriptions on the achievement list.
* **locale** - *en_GB*, *en_US*, *de_DE*, *es_ES*, *es_MX*, *fr_FR*, *ru_RU*, *ko_KR*, *zh_TW* or *zh_CN*

Note: Certain locale choices are only available when selecting some regions.

= Available Filters =

For more capable/adventurous developers there are a number of hooks that allow you to change the way the
plugin functions without altering it's code. This means you get to upgrade in the future without worrying
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

The CSS can be overriden by using the global setting 'Add plugin css to the page'. Unticking this box will allow
you to provide your own CSS as part of your theme. For simple edits I suggest you add the necessary tweaks to your
themes CSS and keep the basic styling the plugin provides.

== Screenshots ==

1. The default widget output
2. The widget configuration screen.

== Changelog ==

= 0.9.7 =
* Realms are now chosen from a dropdown list rather then typed. This should help where the slug varies from the
realm name.

= 0.9.6 =
* Update for patch 5.0.4 talent changes.
* Achievemnt count update
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

