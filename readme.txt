=== Plugin Name ===
Contributors: shockware, baden03, szepe.viktor, xsonic, sanidm, imelgrat
Tags: wordpress, datepicker, timepicker, date, time, calendar, contact form 7, forms, jqueryui
Requires at least: 3.6.1
Tested up to: 4.7.1
Stable tag: 2.6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add a date field using jQuery UI's datepicker to your CF7 forms.
This plugin depends on Contact Form 7.

== Description ==

Enables adding a date field for Contact Form 7 Wordpress Plugin using jQuery UI's
datepicker.

== Installation ==

Please follow the [standard installation procedure for WordPress plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

== Frequently Asked Questions ==

= Where do I submit a bug? =

You can [open an issue on github](https://github.com/relu/contact-form-7-datepicker/issues) or just contact me via email.

== Screenshots ==

1. The datepicker in action
2. Change themes
3. Date field generator

== Changelog ==

= 2.6.0 =
* Compatibility with CF7 4.6, replace deprecated calls (imelgrat)
* Add bosnian translation for timepicker (sanidm)

= 2.5.2 =
* Fix validation error (props to xsonic)

= 2.5.1 =
* Fix i18n

= 2.5 =
* Compatible with CF7 4.3
* Add ability to set custom theme

= 2.4.5 =
* Apply cf7dp_is_valid_date filter for date validation (Kudos to Viktor Szepe)

= 2.4.4 =
* Compatible with CF7 3.7.2

= 2.4.3 =
* Compatible with CF7 3.6

= 2.4.2 =
* Fix min/max hour/minute/second
* Update timepicker addon
* Update jqueryui version number

= 2.4.1 =
* Fixed inline date/time pickers

= 2.4 =
* Fixed minDate and maxDate attributes for date and datetime fields
* Added noWeekend to date and datetime fields
* Added stepHour, stepMinutes and stepSeconds attributes to time and datetime fields
* Make use of CF7's new Shortcode Tag API

= 2.3.2 =
* Fix year-range issues

= 2.3.1 =
* Fix date field generator not showing up anymore
* Validate dd/mm/yy dates correctly

= 2.3 =
* Made it work with CF7 3.4
* Use full jquery-ui version on asset paths
* Add Date Range feature (thanks @dollardad)

= 2.2.1 =
* Added fix for watermark on field update

= 2.2 =
* Added basic date validation
* Fixed watermark

= 2.1 =
* Added inline option
* Fixed tag generator
* Fixed date format when localized

= 2.0 =
* Complete rewrite, using jquery-ui's datepicker

= 0.7.4 =
* Bugfix:
	- datepicker shortcode works now if contact form 7 is not installed

= 0.7.3 =
* Bugfix:
	- fixed Janaury typo

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

= 2.4 =
Added time support via datetimepicker
You can now use the [date], [time] and [datetime] shortcodes into your contact
forms.

= 2.0 =

The plugin has been completely rewritten and the older version is no longer
supported. This version uses jQueryUI's datepicker.
You will have to regenerate all of your date fields.

= 0.7.1 =

If you installed 0.7, do upgrade quick! This will fix all issues!
