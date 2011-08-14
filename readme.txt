=== Plugin Name ===
Contributors: shockware
Donate link: 
Tags: wordpress, datepicker, calendar, contact form 7, forms
Requires at least: WordPress 2.9
Tested up to: WordPress 3.2.1
Stable tag: 0.7.2

Datepicker for Contact Form 7 Wordpress Plugin based on jsDatePick script.

== Description ==

Implements a new **[date]** tag in Contact Form 7 that adds a date field to a form. When clicking the field a calendar pops up enabling your site visitors to easily select any date. Now you can use the [datepicker] shortcode outside of CF7.
To use, just insert the **[date your-field-name]** or **[date* your-required-field-name]** to any form in Contact Form 7 edit area where you want users to input a date.

This plugin is somewhat a fork of [Contact Form 7 Calendar](http://wordpress.org/extend/plugins/cf7-calendar/) by [harrysudana](http://profiles.wordpress.org/users/harrysudana/).

== Installation ==

Please follow the [standard installation procedure for WordPress plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

== Frequently Asked Questions ==

= How to use the [date] and [date*] tags in CF7? =

These shortcodes have the same syntax as any other CF7 text input field shortcode
`[date name_of_field (id:id_of_field class:classes_of_field integer_size/integer_maxlength "value")]`

If you do not provide an **id:** the plugin will use the **name_of_field**. Everything between the parenthesis is optional (do not include parenthesis, I've wrapped everything in parenthesis just to mark attributes that are optional).  

= Can I use the datepicker outside of CF7? =

Yes you can! Just use the new **[datepicker]** shortcode for that.
`[datepicker name="name_of_field" (id="id_of_field" class="classes_of_field" newfield="true/false" value="YYYY-MM-DD")]`

Again, everything wrapped between parenthesis is optional.

	- **name**: name of the input field you want to append the datepicker to
	- **id**: id of the input field you want to append the datepicker to
	- **class**: the CSS classes of the input field
	- **newfield**: specify weather you are appending to an existing input field (false) or create a new input field to append to (true) (default value is true)
	- **value**: the preselected value of the input field
	
If you choose to append to an already existent input field (**newfield**="false"), I recommend you use both name and id (both with the values of the existing input field's attribute values)

For the **value** attribute you can use any date format that can be used as a HTML attribute value, I recommend you use the ISO 8601 (YYYY-MM-DD) date format (ex: 2011-07-30). More info [here](http://php.net/manual/en/function.strtotime.php#refsect1-function.strtotime-notes)

= Where do I submit a bug? =

You can [open an issue on github](https://github.com/relu/contact-form-7-datepicker/issues) or just contact me via email.

= I've translated this plugin, how can I share my translation? =

You can contact me anywhere and I'll add them to the project :)

== Screenshots ==

1. The datepicker in action

== Changelog ==

= 0.7.2 =
* Bugfix:
	- fixed field value not showing up in email message when field values contain hyphens and other non-alphanumeric chars

= 0.7.1 =
* Bugfix:
	- fixed calendar not popping out when no id attribute specified in CF7 shortcode

= 0.7 =
* New: 
	- Added new [datepicker] tag to use outside of CF7
	- Added CF7 specific attributes for the shortcodes
	- Now you can have input fields prefilled with a desired date either from the configuration menu, or by specifying it in the shortcode as an attribute

= 0.6 =
* Bugfixes:
	- the entry in admin menu is now being translated
	- fixed an IE issue where clicking on move forward/backward (years and months) buttons would close the calendar (thanks to [bik](https://github.com/bik) for reporting)

* New:
	- added Italian l11n (Kudos go to Andrea Cavaliero)
	- added the possibility to load scrips/styles on what page you like (thanks to Rodolfo Buaiz for suggesting this)
to achieve this put this into your wp-config.php
`define('CF7_DATE_PICKER_ENQUEUES', false);`

then in your theme's functions.php file you have two options:

`if (is_page('Form page')) {
    if (function_exists('CF7DatePicker'))
       add_action('wp_enqueue_scripts', array('CF7DatePicker', 'plugin_enqueues'));
}`

or

`function cf7dp_enqueues() {
    if (is_page('Form page')) {
        if (function_exists('CF7DatePicker'))
            CF7DatePicker::plugin_enqueues();
    }
}
add_action('init', 'cf7dp_enqueues');`

= 0.5 =
* Bugfixes:
	- the name of the js var that holds the jsDatePick object is now escaped so no illegal char gets printed (regards [Petrus](http://wordpress.org/support/profile/petrus006))
	- removed any posibility of the calendar being displayed more than once at a time on a page when in Mode 2
	- controls option now works
* New:
	- animate the calendar on display option
	- added Dutch translations (regards [Petrus](http://wordpress.org/support/profile/petrus006))

= 0.4 =
* Added new configuration options: 
	- limit selectable dates according to current date (before or or after)
	- available years range option
	- the posibility to show/hide the month and year controls (forward/backward)
* Added new scheme (red)
* Added the posibility to use custom stylesheet files with color schemes (located in css/schemes/<scheme_name>.css)

= 0.3.1 =
* Fixed translations

= 0.3 =
* Added tag generator

= 0.2.1 =
* Fixed input field not being populated on date selection

= 0.2 =
* Fixed some romanian translation typos
* Only one calendar can be shown on a page at a time (datepicker closes onblur)
* Changed default activate configuration values
(Props @Andrea Cavaliero)

= 0.1 =
First release

== Upgrade Notice ==

= 0.7.1 =

If you installed 0.7, do upgrade quick! This will fix all issues!
