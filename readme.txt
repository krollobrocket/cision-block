=== Cision Block ===
Contributors: cyclonecode
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VUK8LYLAN2DA6
Tags: cision, feed, cision feed, shortcode, widget, content
Requires at least: 3.1.0
Tested up to: 5.0
Requires PHP: 5.3
Stable tag: 1.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds a shortcode and a widget that can be used for pulling and displaying pressreleases from cision.

== Description ==

This plugin is developed by [Cyclonecode](https://profiles.wordpress.org/cyclonecode) and can be used to load and expose press releases made by [Cision](http://www.cision.se/).

To start pulling feed items from cision you first need to add the unique identifier for you json feed at the configuration page for the plugin.
You can also change how many feed items to pull, type of feed items, enable pagination, configure caching and much more.

If you have questions you can also try [slack](https://join.slack.com/t/cyclonecode/shared_invite/enQtNDk0OTY0ODg5ODU5LTc5YzEyOWUyNjE1ODZjYTM1MDY0NDExOTc3ZDgzYmExNzMyZmJkNmYyYzI2Y2ZiMTFhYTRlOWI3OTA5MTg3NWM).


= Widget =

You can display a feed in any sidebar by adding and configure a widget.

= Shortcode =

The shortcode **[cision-block]** can either be used by adding it to the content field of any post or by using the **do_shortcode** function in one of your templates.

Shortcode attributes:

- id
Assign a specific name for a block.

- source_uid
A unique feed identifier.

- language
The language code for each feed item. For example 'en' for english.

- date_format
The date format to use.

- readmore
The readmore button text.

- count
The maximum number of items to include in the feed.

- regulatory
When this is set to true then only regulatory press releases will be included in the feed.

- start
Sets the start date for the feed items. The format to use is 2016-12-31.

- end
Sets the end date for the feed items. The format to use is 2016-12-31.

- items_per_page
Sets the number of feed items to display on each page.

- tags
Only press releases with the specified tag will be included. Multiple tags can be separated with a comma.

- categories
Only press releases that has one or more of the specified categories will be included. Multiple categories can be separated with a comma.

- types
Only press releases of the specified types will be included.

- image_style
The image style to use:

 - DownloadUrl
 - UrlTo100x100ArResized
 - UrlTo200x200ArResized
 - UrlTo400x400ArResized
 - UrlTo800x800ArResized
 - UrlTo100x100Thumbnail
 - UrlTo200x200Thumbnail

- flush
Clears the cache for the block.

Here is an example using all of the above attributes:

`[cision-block
  id=example_block
  source_uid=A275C0BF733048FFAE9126ACA64DD08F
  language=sv
  date_format=m-d-Y
  readmore="Read more"
  regulatory=true
  count=6
  items_per_page=2
  tags="cision,company"
  categories="New Year,Summer camp"
  types="PRM, RDV"
  start=2016-01-12
  end=2017-01-12
  image_style=UrlTo400x400ArResized
  flush=true
]`

**Notice** that all shortcode attributes are optional.
Default values is taken from the plugins settings page.

Here is a complete list of the different kind of pressreleases:

* KMK - Annual Financial statement
* RDV - Annual Report
* PRM - Company Announcement
* RPT - Interim Report
* INB - Invitation
* NBR - Newsletter

= Template =

The template used to render the feed is located under the **cision-block/templates/**, you can override
this template by copying it to either the root or under a **templates** folder in your theme.

= Fields =

By default only the following fields are collected for each feed item:

* Title
* Intro
* Body
* PublishDate
* CisionWireUrl
* IsRegulatory
* Images[0]
 * DownloadUrl
 * Description

= Filters =

Add more fields to each feed item:

    add_filter('cision_map_source_item', function($item, $data) {
      $item['Header'] = sanitize_text_field($data->Header);
      $item['LogoUrl'] = esc_url_raw($data->LogoUrl);
      $item['SocialMediaPitch'] = sanitize_text_field($data->SocialMediaPitch);

      return $item;
    }, 10, 2);

Customize the sorting of the feed items:

    add_filter('cision_block_sort', function($items) {
      usort($items, function($a, $b) {
        return $a->PublishDate > $b->PublishDate;
      });

      return $items;
    });

Add custom attributes to the pager:

    add_filter('cision_block_pager_attributes', function(array $attributes) {
      return array_merge(
        $attributes,
        array(
          'class' => 'custom-class',
          'id' => 'custom-id',
        )
      );
    });

Set a custom class for active pager item:

    add_filter('cision_block_pager_active_class', function($class) {
      return 'custom-class';
    });

To add attributes to the section wrapper in the template:

    add_filter('cision_block_wrapper_attributes', function(array $attributes) {
      return array(
        'class' => array(
          'custom-class',
        ),
      );
    });

To add attributes to the article wrapper in the template:

    add_filter('cision_block_media_attributes', function(array $attributes) {
      return array(
        'class' => array(
          'custom-class',
        ),
      );
    });

Add a prefix that will be displayed at the start of the wrapper:

    add_filter('cision_block_prefix', function($prefix) {
      return '<h1>Prefix</h1>';
    });

Add a suffix that will be displayed at the end of the wrapper:

    add_filter('cision_block_suffix', function($suffix) {
      return '<h1>Suffix</h1>';
    });


= Resources =

A complete list of fields can be found at: [https://websolutions.ne.cision.com/documents/P2_Feed.pdf](https://websolutions.ne.cision.com/documents/P2_Feed.pdf)

The following Feed identifier can be used for testing: **A275C0BF733048FFAE9126ACA64DD08F**

== Frequently Asked Questions ==

== Support ==

If you run into any trouble, don’t hesitate to add a new topic under the support section:
[https://wordpress.org/support/plugin/cision-block](https://wordpress.org/support/plugin/cision-block)

You can also try contacting me on [slack](https://join.slack.com/t/cyclonecode/shared_invite/enQtNDk0OTY0ODg5ODU5LTc5YzEyOWUyNjE1ODZjYTM1MDY0NDExOTc3ZDgzYmExNzMyZmJkNmYyYzI2Y2ZiMTFhYTRlOWI3OTA5MTg3NWM).


== Installation ==

1. Upload cision block to the **/wp-content/plugins/** directory,
2. Activate the plugin through the **Plugins** menu in WordPress.

== Frequently Asked Questions ==

= Possible to have more than 1 identifier? =

There is possible to use separate feed identifiers for different blocks by using the
**source_uid** attribute in the shortcode as in this example:

    [cision-block source_uid=A275C0BF733048FFAE9126ACA64DD08F]

= Possible to create multiple cision blocks? =

Yes it is possible to have multiple blocks by simply adding shortcode arguments for each block.

== Upgrade Notice ==

= 1.4.4 =
Fixed a bug that made the plugin throw an error if no attributes was used in the shortcode.

= 1.4.8 =
Fixed a bug where preview mode was not working correctly.

= 1.4.9.1 =
Fixed a bug where source id from widget was never used.

== Screenshots ==

1. A feed from cision with a pager at the bottom.
2. Settings form.

== Changelog ==

= dev
- Fix deprecation warnings.
- Fix php5.3 error message.

= 1.5.1 =
- Add support to filter feed based on categories.

= 1.5.0 =
- Major update of code base.
- Use safe version of wp_remote_request().
- Add correct filter when sanitizing settings.
- Add link to settings page under plugin page.
- Add textarea holding settings in json format.

= 1.4.9.1 =
- Fixed a bug where source id from widget was never used.

= 1.4.9 =
- Removed duplicate language field from widget.
- Removed classes from select elements in widget form.
- Add date_format to shortcode arguments.
- Add date_format to widget settings.

= 1.4.8 =
- Fixed a bug where pagination was not working in preview.
- Add User-Agent request header.

= 1.4.7 =
- Improve filtering of variables and post data.
- Add separate cache key for widget.
- Add support to change text or remove the 'Read more' button.
- Corrected call to register_activation_hook().

= 1.4.6 =
- Add separate method for clearing transient cache.
- Remove unused namespace.
- Add source_uid to shortcode attributes.
- Add language form field to widget.

= 1.4.5 =
- Fixed a bugg where settings gets overwritten by the use of a widget or another block.
- Add code to remove any cision_block_widgets from the sidebars during removal.
- Change member visibility.
- Add constants for cision specific query arguments.
- Change description for start and end date as suggested in https://wordpress.org/support/topic/suggestion-regarding-date-format/.
- Major update of readme.
- Added image style to widget settings.
- Added image style to shortcode attributes.
- Added filters to set attributes of wrappers in template.
- Added id shortcode parameter used to name a specific block.
- Added filters for suffix and prefix.
- Added filters to set attributes and active class for pager.
- Added support to filter items based on language.

= 1.4.4 =
- Fixed a bugg triggered when no shortcort argument was set.

= 1.4.3 =
- Allow people to change what capability is required to use this plugin.
- Improve filter validation.
- Refactor code to conform with PSR2 standard.
- Add support to disable images.

= 1.4.2 =
- Add date format option.

= 1.4.1 =
- All images can now be handled over https.
- Add support to select size of image.

= 1.4.0 =
- Add start and end date to widget.
- Add separate class for backend specific code.

= 1.3.2 =
- Add support to choose start and end date for collected feed items.

= 1.3.1 =
- Add support to override settings from widget.

= 1.3.0 =
- Add settings field for cache lifetime.
- Remove transient cache when settings are saved.

= 1.2.9 =
- Add support to filter feed based on IsRegulatory field.

= 1.2.8 =
- Add tags setting that can be used to filter a feed on keywords.
- Improved error checking for remote request.

= 1.2.7 =
- Place settings page under general options.

= 1.2.6 =
- Add swedish translation.

= 1.2.5 =
- Add support to sort feed through the cision_block_sort filter.
- Return template output in order to get correct placement of the shortcode in post content.

= 1.2.4 =
- Add separate cache key for each post containing a cision block shortcode.

= 1.2.3 =
- Add support to override which type of pressreleases to display in shortcode arguments.

= 1.2.2 =
- Add shortcode arguments that can be used to override block settings.
- Switched to using wp_remote_request() instead of file_get_contents().

= 1.2.1 =
- Updated readme with widget tag.
- Added new icons.

= 1.2.0 =
- Added a cision block widget.

= 1.1.6 =
- Rename template to match module name.
- Add variable section in template.
- Use version_compare() to check version.
- Corrected call to load_plugin_textdomain().

= 1.1.5 =
- Fixed invalid pagination.
- Added support to select which feed types to display.

= 1.1.4 =
- Added correct order for filter parameters.
- Check so feed items are set before pagination.
- Added types setting variable.

= 1.1.3 =
- Verify version in settings on load.
- Added missing delete() function.

= 1.1.2 =
- Added active class for pager.
- Store version string in settings.
- Added separate Settings class.
- Added activation hook.


= 1.1.1 =
- Added pager.

= 1.1 =
- Added a filter, cision_map_source_item, that can be used to extract more data for each feed item.

= 1.0 =
- Initial commit.
