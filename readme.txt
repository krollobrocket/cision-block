=== Cision Block ===
Contributors: cyclonecode
Donate link: https://www.buymeacoffee.com/cyclonecode
Tags: cision, feed, cision feed, shortcode, widget, content
Requires at least: 3.1.0
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: 2.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds a shortcode and a widget that can be used for pulling and displaying pressreleases from cision.

== Description ==

This plugin is developed by [Cyclonecode](https://profiles.wordpress.org/cyclonecode) and can be used to load and expose press releases made by [Cision](http://www.cision.se/).

To start pulling feed items from cision you first need to add the unique identifier for you json feed at the configuration page for the plugin.
You can also change how many feed items to pull, type of feed items, enable pagination, configure caching and much more.

If you have questions or perhaps some idea on things that should be added you can also try [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

= Pro Version =

There is a **PRO** version of this plugin, which includes extended features. For instance:

- Support to fetch entire feed and not only the last 100 entries.
- Custom post types. Creates a post for each item in Wordpress. This means that all news have standard Wordpress links.
- Manually created posts can be added to the feed.
- Custom taxonomies for categories and tags fetched from Cision.
- Use standard article template from your active theme.
- Support to create, update and delete posts based on PUSH events sent from Cision.
- Support to create, update and delete posts during CRON at configurable intervals.
- Subscription support.
- Basic ticker support.
- Use normal or ajax based load more button for pagination.
- Annual free support and quicker response times.
- Discount for multisite licenses.

I usually have an initial meeting where I talk about the plugin, explain the different features and answer any questions.

There is also a backlog with features yet to be implemented on github, some of the tasks in the list includes:

- Implement support to fetch and display extended ticker information.
- Event module.
- Pin feed items to top of list.
- Import and export settings between environments.
- Preview functionality.
- Disclaimer support.
- Improved SEO.
- Support for multiple templates.
- Display estimated reading time for each feed item.
- And much, much more.

To get more information about the Pro version, send me an email at [cyclonecode@gmail.com](mailto:cyclonecode@gmail.com) or give me a call at +(46)-730334088.

= Looking for help =

I am currently in the search for someone who would like to help me with something of the following:

- Create a dashboard icon which can be used in the admin menu.
- Create a banner that would be displayed on the plugins homepage at wordpress.org.
- Design a nicer and more intuitive admin interface.
- Create a solid looking icon that can be used on multiple places.

If you would like to help with anything of the above, please do not hesitate and contact me either on slack or by email.

= Developers =

I am looking for developers that would be interested in contributing to either the free or premium version of the plugin.

Would be great just to get some ideas and input from others who have some experience in Wordpress plugin development.

At this point I am pretty much on my own, which will sometimes result in me just thinking around some issues in my own way; here I think it would be awesome to have others to talk to and collaborate.

If you think this sounds interesting, please drop me an email or ping me on Slack.

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

- view
This states what kind of items to include:
1 - include both regulatory and non-regulatory items.
2 - include only regulatory items.
3 - include only non-regulatory items.

- start
Sets the start date for the feed items. The format to use is 2016-12-31.

- end
Sets the end date for the feed items. The format to use is 2016-12-31.

- mark_regulatory
Emphasis if a release if regulatory or non-regulatory.

- regulatory_text
Text to display for regulatory items.

- non_regulatory_text
Text to display for non-regulatory items.

- show_filters
Enable filtering of feed items.

- filter_all_text
Button text for 'all' filter.

- filter_regulatory_text
Button text for 'regulatory' filter.

- filter_non_regulatory_text
Button text for 'non-regulatory' filter.

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

- show_excerpt
Display excerpt for each feed item.

- template
Template file to use. If no template is set in settings and this parameter is not set then cision-block.php in either the active
theme or in the plugin will be used as a default.
You can either use the name of the template as given in the template header e.g 'Foo' or the actual filename e.g foo.php.

- flush
Clears the cache for the block.

Here is an example using all of the above attributes:

`[cision-block id=example_block source_uid=A275C0BF733048FFAE9126ACA64DD08F language=sv date_format=m-d-Y readmore="Read more" show_excerpt=0 view=1 count=6 items_per_page=2 tags="cision,company" categories="New Year,Summer camp" types="PRM, RDV" start=2016-01-12 end=2019-06-12 image_style=UrlTo400x400ArResized mark_regulatory=1 regulatory_text=Regulatory non_regulatory_text=*none* show_filters=1 filter_all_text=*none* filter_regulatory_text=Regulatory filter_non_regulatory_text=Non-regulatory template=foo.php flush=true]`

**Notice** that all shortcode attributes are optional and that they **must** be on a single line.
Default values is taken from the plugins settings page.

Here is a complete list of the different kind of pressreleases:

* KMK - Annual Financial statement
* RDV - Annual Report
* PRM - Company Announcement
* RPT - Interim Report
* INB - Invitation
* NBR - Newsletter

= More than one block in a page =

To use more than one block in a single page you will need to set a unique id for each block or else they will both share the same cache entry.

= Filter and marking feed items =

On the 'Filters' tab you can enable filtering on you feed and add a text for the different kind of filters or use the default ones.
If for some reason you would like to hide a specific filter button you can enter the special value `*none*` in the corresponding text field.

You can add a text for each feed item stating if it is regulatory or not by checking the 'Mark regulatory' checkbox in the settings page.
If you wish to hide the text for regulatory or non-regulatory releases you can use the special value `*none*` in the corresponding text field.

= Template =

The template used to render the feed is **cision-block/templates/cision-block.php**, you can override
this template by copying it to either the root or under a **templates** folder in your theme.

You can also select a specific template which will be used to render the feed under the plugins settings page.
To create a new template, you can follow the steps as described in this link: [Page Templates](https://developer.wordpress.org/themes/template-files-section/page-template-files/):

For instance adding a file with the following header comment would create a new 'Foo' template:

    <?php /* Template Name: Foo */ ?>

= Display single press releases in Wordpress =

Since version 2.0.0 it is possible to fetch and display press releases directly from within Wordpress.
The template used in this case is **cision-block/templates/cision-block-post.php**, you can override
this template by copying it to either the root or under a **templates** folder in your theme.

The `$CisionItem` feed object that is available in the template contains all raw data fetched from Cision.
Under the **Resources** section there is a link that explains all the different fields that is available.
For example if you use the `$CisionItem->HtmlBody` to display content from the feed item you might have to add custom
css since this contains pre formated html which may include inline css and so on.

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

    add_filter('cision_map_source_item', function($item, $data, $block_id) {
      $item['Header'] = sanitize_text_field($data->Header);
      $item['LogoUrl'] = esc_url_raw($data->LogoUrl);
      $item['SocialMediaPitch'] = sanitize_text_field($data->SocialMediaPitch);

      return $item;
    }, 10, 3);

Customize the sorting of the feed items:

    add_filter('cision_block_sort', function($items, $block_id) {
      usort($items, function($a, $b) {
        return $a->PublishDate > $b->PublishDate;
      });

      return $items;
    }, 10, 2);

Add custom attributes to the pager:

    add_filter('cision_block_pager_attributes', function(array $attributes, $block_id) {
      return array_merge(
        $attributes,
        array(
          'class' => 'custom-class',
          'id' => 'custom-id',
        )
      );
    }, 10, 2);

Set a custom class for active pager item:

    add_filter('cision_block_pager_active_class', function($class, $block_id) {
      return 'custom-class';
    }, 10, 2);

To add attributes to the section wrapper in the template:

    add_filter('cision_block_wrapper_attributes', function(array $attributes, $block_id) {
      return array(
        'class' => array(
          'custom-class',
        ),
      );
    }, 10, 2);

To add attributes to the article wrapper in the template:

    add_filter('cision_block_media_attributes', function(array $attributes, $block_id) {
      return array(
        'class' => array(
          'custom-class',
        ),
      );
    }, 10, 2);

Add a prefix that will be displayed at the start of the wrapper:

    add_filter('cision_block_prefix', function($prefix, $block_id) {
      return '<h1>Prefix</h1>';
    }, 10, 2);

Add a suffix that will be displayed at the end of the wrapper:

    add_filter('cision_block_suffix', function($suffix, $block_id) {
      return '<h1>Suffix</h1>';
    }, 10, 2);


= Resources =

A complete list of fields can be found at: [https://websolutions.ne.cision.com/documents/P2_Feed.pdf](https://websolutions.ne.cision.com/documents/P2_Feed.pdf)

The following Feed identifier can be used for testing: **A275C0BF733048FFAE9126ACA64DD08F**

== Improvements ==

If you have any ideas for improvements, don't hesitate to email me at cyclonecode.help@gmail.com or send me a message on [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

== Support ==

If you run into any trouble, don’t hesitate to add a new topic under the support section:
[https://wordpress.org/support/plugin/cision-block](https://wordpress.org/support/plugin/cision-block)

You can also try contacting me on [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

== Installation ==

1. Upload cision block to the **/wp-content/plugins/** directory,
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Add your feed identifier and configure the plugin at **/wp-admin/options-general.php?page=cision-block** in Wordpress.
4. You can then add either a shortcode or setup a widget in order to display the feed.

== Frequently Asked Questions ==

= Can I fetch more than the last 100 news? =

You will need to use the **Pro** version to do this.

= How do I get a unique feed identifier? =

This is something that Cision will provide you with.
You can contact them at support@cision.com.

= Possible to have more than 1 identifier? =

There is possible to use separate feed identifiers for different blocks by using the
**source_uid** attribute in the shortcode as in this example:

    [cision-block source_uid=A275C0BF733048FFAE9126ACA64DD08F]

= Possible to create multiple cision blocks? =

Yes it is possible to have multiple blocks by simply adding shortcode arguments for each block.

= Can I use normal permalinks for the news? =

This is something that is possible using the **Pro** version, since all news are imported as custom posts into Wordpress.

== Upgrade Notice ==

= 1.4.4 =
- Fixed a bug that made the plugin throw an error if no attributes was used in the shortcode.

= 1.4.8 =
- Fixed a bug where preview mode was not working correctly.

= 1.4.9.1 =
- Fixed a bug where source id from widget was never used.

= 2.1.0 =
- Fixes a bug where the plugin could not be deleted.

= 2.4.3.1 =
- Fixes a bug where notifications could not be dismissed.

== Screenshots ==

1. A feed from cision with a pager at the bottom.
2. Settings form.
3. A single press release displayed in Wordpress.

== Changelog ==

= 2.5.0
- Do not use constant for text domain.
- Use disabled to set input attribute.
- Set headers for remote request.
- Bump composer installers.
- Use PHP and Wordpress version from settings.
- Remove unused override in settings.
- Add settings to toggle excerpts.
