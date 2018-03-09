=== Plugin Name ===
Contributors: webdevmattcrom
Tags: user, users, csv, batch, export, exporter, admin
Requires at least: 4.2
Tested up to: 4.8
Stable tag: 1.1.1

Export users data and metadata to a csv file.

== Description ==

A WordPress plugin that exports user data and meta data. You can even export the users by role and registration date range. 

Export users by role and optionally set a registration date range.

Export is found in "Tools > Export", or with the "Export Users" button on the Users admin screen.

By default, it does not export user passwords as a security concern. See the [FAQ](#faq) for how to include them regardless.

= Features =

* Exports all users fields
* Exports users meta
* Exports users by role
* Exports users by date range

Issues and Pull Requests for feature requests or bug reports [are welcome at Github](https://github.com/mathetos/export-users-to-csv).

== Installation ==

For an automatic installation through WordPress:

1. Go to the 'Add New' plugins screen in your WordPress admin area
2. Search for 'Export Users to CSV'
3. Click 'Install Now' and activate the plugin
4. Go the 'Users' menu, under 'Export to CSV'


For a manual installation via FTP:

1. Upload the `export-users-to-csv` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' screen in your WordPress admin area
3. Go the 'Users' menu, under 'Export to CSV'

To upload the plugin through WordPress, instead of FTP:

1. Upload the downloaded zip file on the 'Add New' plugins screen (see the 'Upload' tab) in your WordPress admin area and activate.
2. Go the 'Users' menu, under 'Export to CSV'

== Frequently Asked Questions ==

= How to use? =

Click on the 'Export Users' button at the top of the 'Users' admin screen, or navigate to "Tools > Export." From there, choose "Users" as your export, then choose the role and the date range. Choose nothing at all if you want to export all users, then click 'Export'. That's all!

= How do I include the Passwords? =

I don't really recommend it since storing passwords in plain-text can be a real liability issue. Nevertheless, you can add this filter to your site to allow the password to be included as a column in your CSV file:

`add_filter('pp_eu_exclude_data', 'my_prefix_include_password');

 function my_prefix_include_password() {
     return array();
 }`

== Screenshots ==

1. The User export tool
2. The User Export button at the top of the Users admin page

== Changelog ==

= 1.1.1 (March 4, 2018) =
* Fixed bug that prevented the date range from working as intended. Thanks @sbskamey for [reporting the issue](https://github.com/mathetos/export-users-to-csv/issues/12).

= 1.1 (February 25, 2018) =
* Moved screen to the "Tools > Export" screen to leverage WordPress core export features. [Github Issue #2](https://github.com/mathetos/export-users-to-csv/issues/2)
* Removed local translations and updated load_textdomain to look for the localized files in the correct WordPress core folder. [Github Issue #1](https://github.com/mathetos/export-users-to-csv/issues/1)
* Add "Export Users" button to the Users admin screen for increased visibility. [Github Issue #11](https://github.com/mathetos/export-users-to-csv/issues/11)

= 1.0.1 =
* This plugin has been adopted by [Matt Cromwell](https://profiles.wordpress.org/webdevmattcrom). You can expect new features to be rolled out soon.

= 0.2 =
* First public release.
* Improved memory usage.
* Added date range selection.
* Added readme.txt.

= 0.1 =
* First release.

== Upgrade Notice ==

= 1.1 =
* The User export now uses WordPress core export features and is found at "Tools > Export".

= 1.0.1 =
* This plugin has been adopted by [Matt Cromwell](https://profiles.wordpress.org/webdevmattcrom). You can expect new features to be rolled out soon.
