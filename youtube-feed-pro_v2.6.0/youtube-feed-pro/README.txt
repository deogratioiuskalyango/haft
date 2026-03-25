=== Feeds for YouTube Pro Developer ===
Author: Smash Balloon
Contributors: smashballoon, craig-at-smash-balloon
Support Website: http://smashballoon/youtube-feed/
Tags: YouTube, YouTube feed, YouTube widget, YouTube player, YouTube gallery
Requires at least: 4.1
Requires PHP: 7.4
Tested up to: 6.7
Stable tag: 2.6.0
License: Non-distributable, Not for resale

Feeds for YouTube Pro allows you to display completely customizable YouTube feeds from any channel.

== Description ==
Display **completely customizable**, **responsive** and **search engine crawlable** versions of your YouTube feed on your website. Completely match the look and feel of your site with tons of customization options!

= Features =
* **Completely Customizable** - by default inherits your theme's styles
* YouTube feed content is **crawlable by search engines** adding SEO value to your site
* **Completely responsive and mobile optimized** - works on any screen size
* Display videos from any channel in a list, gallery, carousel slider or grid
* Display videos from your **favorites list**
* Show a feed of live streaming videos on your site.
* Allow **filtering** of videos using keywords in the description or title
* Fully functional **search endpoint** for display videos from a search result
* **Combine multiple feeds** into one
* Customizable **actions when video completes** like displaying a link to a product page for example.
* Download video data into a **custom post type** to allow visitors to browse and view videos on your website.
* Display **multiple feeds** from different YouTube channels on multiple pages or widgets
* Post caching means that your YouTube feed loads **lightning fast** and minimizes YouTube API requests
* **Infinitely load more** of your YouTube videos with the 'Load More' button
* Built-in easy to use "YouTube Feed" Widget
* Fully internationalized and translatable into any language
* Display a beautiful header at the top of your YouTube gallery
* Enter your own custom CSS or JavaScript for even deeper customization

For simple step-by-step directions on how to set up the Feeds for YouTube Pro plugin please refer to our [setup guide](http://smashballoon.com/youtube-feed/docs/setup/ 'Feeds for YouTube Pro setup guide').

= Benefits =
* **Increase social engagement** between you and your users, customers, or fans
* **Save time** by using the Feeds for YouTube Pro plugin to generate dynamic, search engine crawlable content on your website
* **Get more follows** by displaying your YouTube videos directly on your site
* Display your YouTube gallery **your way** to perfectly match your website's style
* The plugin is **updated regularly** with new features, bug-fixes and YouTube API changes
* Support is quick and effective
* We're dedicated to providing the **most customizable**, **robust** and **well supported** YouTube feed plugin in the world!

== Installation ==
1. Install the Feeds for YouTube Pro plugin either via the WordPress plugin directory, or by uploading the files to your web server (in the /wp-content/plugins/ directory).
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the 'Feeds for YouTube Pro' settings page to configure your YouTube gallery.
4. Use the shortcode [youtube-feed feed=1] in your page, post or widget to display your feed.
5. You can display multiple YouTube channels with different configurations by creating additional feeds specifying the feed ID directly in the shortcode: [youtube-feed feed=2].

For simple step-by-step directions on how to set up the Feeds for YouTube Pro plugin please refer to our [setup guide](http://smashballoon.com/youtube-feed/docs/setup/ 'Feeds for YouTube Pro setup guide').

= Setting up the Free Feeds for YouTube Pro WordPress Plugin =

The Feeds for YouTube Pro plugin is brand new and so we're currently working on improving our documentation for it. If you have an issue or question please submit a support ticket and we'll get back to you as soon as we can.

1) Once you've installed the Feeds for YouTube Pro plugin click on the "Feeds for YouTube Pro" item in your WordPress menu

2) Click on the large blue YouTube login button to get your YouTube Access Token and YouTube Refresh Token. Note; if you have your own YouTube Developer Project set up then you can enter your YouTube API key by enabling the checkbox below the YouTube login button.

3) Authorize the Feeds for YouTube Pro plugin to read data about your YouTube videos and YouTube channel.

4) YouTube sends back your YouTube Access Token and YouTube Refresh Token which are then automatically saved by the Feeds for YouTube Pro plugin. This information is required in order to connect to the YouTube API.

5) Enter a YouTube channel name to display videos from.

6) Navigate to the Customize and Style pages to customize your YouTube feed.

7) Once you've customized your YouTube feed, click on the "Display Your Feed" tab for directions on how to display your YouTube feed (or multiple YouTube feeds).

8) Copy the [youtube-feed] shortcode and paste it into any page, post or widget where you want the YouTube feed to appear.

9) You can paste the [youtube-feed] shortcode directly into your page editor.

10) You can use the default WordPress 'Text' widget to display your Feeds for YouTube Pro in a sidebar or other widget area.

11) View your website to see your YouTube feed(s) in all their glory!

== Frequently Asked Questions ==

= Can I display multiple YouTube feeds on my site or on the same page? =

Yep. You can display multiple YouTube feeds by using our built-in shortcode options, for example: `[youtube-feed feed=2]`.

= How do I embed a YouTube gallery directly into a WordPress page template? =

You can embed your YouTube gallery directly into a template file by using the WordPress [do_shortcode](http://codex.wordpress.org/Function_Reference/do_shortcode) function: `<?php echo do_shortcode('[youtube-feed]'); ?>`.

= Will Feeds for YouTube Pro work with W3 Total Cache or other caching plugins? =

The Feeds for YouTube Pro plugin should work in compatibility with most, if not all, caching plugins, but you may need to tweak the settings in order to allow the YouTube feed to update successfully and display your latest posts.  If you are experiencing problems with your YouTube feed not updating then try disabling either 'Page Caching' or 'Object Caching' in W3 Total Cache (or any other similar caching plugin) to see whether that fixes the problem and the YouTube feed displays and updates successfully.

== Screenshots ==

1. By default the plugin inherits styles from your theme
2. Display multiple YouTube feeds and customize each one
3. When using the "grid" layout, videos open and play in a lightbox
4. Feeds for YouTube Pro Settings pages
5. Built in customization settings make customizing your feed easy
6. Use handy shortcode options to customize individual feeds
7. To display a feed just copy and paste the shortcode into a widget or page

== Changelog ==

= 2.6.0 =
* New: Added support for a new GDPR consent plugin (WPConsent)[https://wpconsent.com/?utm_campaign=youtube-free-readme&utm_source=changelog&utm_medium=wpconsentannouncement]
* New: Added support for GDPR Cookie Compliance by Moove Agency and Real Cookie Banner GDPR plugins.
* Fix: Resolved an issue that sometimes prevented past live streams from appearing in the feed.

= 2.5.2 =
* Fix: Fixed an issue preventing Social Wall from showing YouTube stats.
* Fix: Fixed an issue with our GDPR features and the wrong videos being linked to.

= 2.5.1 =
* Fix - Fixed an issue with a file name causing YouTube Feeds to be undetectable by Social Wall.

= 2.5 =
* Tweak - Added easy installers for our latest Smash Balloon Products. Try [ClickSocial](https://clicksocial.com/?utm_source=plugin-free&utm_campaign=sby&utm_medium=readme&utm_content=ClickSocial), [Reviews Feed](https://smashballoon.com/reviews-feed/?utm_source=plugin-free&utm_campaign=sby&utm_medium=readme&utm_content=ReviewsFeed), and [TikTok Feeds](https://smashballoon.com/tiktok-feeds/?utm_source=plugin-free&utm_campaign=sby&utm_medium=readme&utm_content=TikTok) today!
* Fix - PHP warning related to _load_textdomain_just_in_time are now resolved
* Fix - Fixed an issue with the profile picture when showing a Livestream feed.
* Fix - Improved compatibility with the latest versions of Divi and Elementor. Use our related integrations to easily add your feeds to your site!

= 2.4.2 =
* Tweak: Added support for our new [Feed Analytics](https://smashballoon.com/?utm_campaign=youtube-pro-readme&utm_source=changelog&utm_medium=feedanalyticsannouncement) product. Get insights as to how your feeds are being used by site visitors.
* Fix: Fixed an issue with video playback when Elementor was being used.
* Fix: Fixed an issue with the feed customizer when previewing the carousel layout.
* Fix: Fixed PHP deprecation warnings related to the use of dynamic properties.
* Fix: Fixed an issue with HTML rendering affecting specific hosts.
* Fix: Fixed a PHP error that would occur for some users related to listing sources.

= 2.4.1 =
* Fix: Resolved a fatal error that occurred when previewing a single video.

= 2.4 =
New: Added support for displaying video comments. Show the latest comments from your viewers in the lightbox or below the video in video lists.
Fix: Plugin hardening.
Tweak: Improved translation support for multiple languages.
Tweak: Added support for Borlabs 3.0 and our GDPR features.

= 2.3.3 =
* New: Added feature to show comments for each video.
* Fix: Subscribe button icon color inherit.
* Fix: Subscribe button color naming.

= 2.3.2 =
* Fix: Fixed an issue that caused some feed templates to not be accessible for all license tiers.

= 2.3.1 =
* Fix: Plugin hardening.
* Fix: Viewing stats were not displaying for the "Basic" tier.
* Tweak: Removed JavaScript mapping that was causing a reference to a compromised URL in a comment (there was no real security risk but it was being flagged by WordFence).

= 2.3 =
* New: Added support for displaying YouTube feeds using an Elementor widget and Divi modules.
* Fix: Fixed the text and icon alignment in the header when both the subscribers and description were hidden.
* Fix: After clicking a video thumbnail in the gallery layout, site visitors are automatically scrolled to the video player.
* Fix: JSON feed import feature has been fixed.
* Fix: Settings page link has been added to the plugins page action buttons.
* Fix: Single videos were having incorrect URLs.
* Fix: Single videos were displaying warnings on post previews.
* Fix: Fixed a conflict with WP Engine and the plugin notifications.
* Fix: Plugin hardening.

= 2.2.4 =
* Fix: Plugin hardening.

= 2.2.3 =
* Fix: Fixed an issue causing the next update schedule time found on the settings page to be inaccurate.
* New: Added a setting to show past live streams when using the live streams feed type. This is found when editing the feed type in the customizer.

= 2.2.2 =
* Fix: Plugin hardening.
* Fix: Removed unused files in the "templates" folder.
* Fix: Fixed a deprecation notice in PHP 8.1+ "strpos(): Passing null to parameter #1 ($haystack) of type string is deprecated"
* Fix: Custom text that included single quotes would show with slashes on the front-end of the feed.

= 2.2.1 =
* Fix: Fixed PHP error affecting single video posts Class 'SmashBalloon\YouTubeFeed\SBY_CPT' not found

= 2.2 =
* New: Create a new feed using a YouTube handle instead of needing the channel ID.
* Fix: Emoji in video descriptions would break single video post types when an older version of MySQL was in use.
* Fix: Fixed PHP deprecation warning "Passing null to parameter" for PHP 8.2+
* Fix: The video description would sometimes appear twice on hover.

= 2.1.1 =
* Fix: The Lightbox would appear at the bottom of pages with a YouTube feed on it when using Borlabs Cookie consent plugin or the AJAX theme workaround setting.
* Fix: Fixed a fatal PHP error occurring in some sites using PHP 7.1.
* Fix: Changed how the plugin adds an HTTP referer to affect just API calls made by YouTube Feeds.

= 2.1 =
* New: Your video players will now feature an attractive subscribe bar. Clicking the subscribe button on the bar will link your visitors to your YouTube page where they will be prompted to subscribe to your channel. Disable this by going to the customizer Videos->Video Player Experience->Subscribe Link setting
* New: Single video posts can now have the video thumbnail for the related YouTube video set as the featured image. Visit the settings page and go to the "Single Videos" tab to enable this feature.
* Tweak: Connecting your YouTube account will now take you to connect.smashballoon.com first to improve the reliability of this process.

= 2.0.7 =
* Fix: Fixed a compatibility issue with the Complianz Cookie Consent plugin integration.
* Fix: When Social Wall was active the About Us and Support links would not work.
* Fix: Clicking the link to the page that explains creating custom templates would not work.
* Fix: A PHP error would occur when the active license was checked under certain circumstances.

= 2.0.6 =
* Fix: Removed DotEnv code that was causing a conflict in some rare circumstances.
* Fix: When using two or more feeds on one page and the "AJAX theme loading fix" setting, an empty lightbox related element would display at the bottom of the page.

= 2.0.5 =
* Fix: Fixed a conflict with the All in One SEO plugin causing menu tabs to not work properly.

= 2.0.4 =
* Fix: The box shadow setting for card style videos was not applying correctly.
* Fix: Editing individual colors of elements would not be reflected on the front-end.
* Fix: The setting for the date format would not always affect the feed preview.

= 2.0.4 =
* Fix: The box shadow setting for card style videos was not applying correctly.
* Fix: Editing individual colors of elements would not be reflected on the front-end.
* Fix: The setting for the date format would not always affect the feed preview.

= 2.0.3 =
* Tweak: Improved the user experience to avoid confusion over connecting an account when creating a new feed.
* Fix: Fixed deprecation warnings when using PHP 8.0+.
* Fix: Added any API key saved in settings to the system info for easy support debugging.
* Fix: Activating the Pro version would trigger an error if the free version of YouTube Feeds was active.
* Fix: Removed extra files.

= 2.0.2 =
* Fix: The "All Feeds" page would not display for some older versions of WordPress.
* Fix: Video statistics were not updating properly in some circumstances.
* Fix: The customizer is now easier to use on devices smaller than a desktop.
* Fix: Notices from other plugins were showing when creating a feed and using the customizer.
* Fix: Pagination to edit additional feeds was not working when there were more than 20 feeds created.

= 2.0.1 =
* Fix: Fixed a PHP error that would occur when using a version of WordPress less than 5.9 and a version of PHP less than 8.0.

= 2.0 =
* Important: Minimum supported WordPress version has been raised from 3.5 to 4.1.
* New: Our biggest update ever! We've completely redesigned the plugin settings from head to toe to make it easier to create, manage, and customize your YouTube feeds.
* New: All your feeds are now displayed in one place on the "All Feeds" page. This shows a list of any existing (legacy) feeds and any new ones that you create.
* New: Easily edit individual feed settings for new feeds instead of cumbersome shortcode options.
* New: It's now much easier to create feeds. Just click "Add New", select your feed type, connect your account, and you're done!
* New: Brand new feed customizer. We've completely redesigned feed customization from the ground up, reorganizing the settings to make them easier to find.
* New: Live Feed Preview. You can now see changes you make to your feeds in real time, right in the settings page. Easily preview them on desktop, tablet, and mobile sizes.
* New: We've added a new Feed Templates feature. You can now select a feed template when creating a feed to make it much quicker and easier to get started with the type of feed you want to display. Selecting a template preconfigures the feed customization settings to match that template, saving you time and effort.
* New: Color Scheme option. It's now easier than ever to change colors across your feed without needing to adjust individual color settings. Just set a color scheme to effortlessly change colors across your entire feed.
* New: You can now change the number of columns in your feed across desktop, tablet, and mobile.
* New: Easily import and export feed settings to make it simple to move feeds across sites.
* New: Added a Post Style setting which allows you to add a boxed style to your Videos, with a background color, border radius, and box shadow.
* New: Added a new custom text header option, so you can now add custom text to the header for your feed.

= 1.4.2 =
* New: Added PHP hook "sby_wp_post_content" to modify the post content before saving video posts as a custom post type in the WordPress database.
* New: Added a shortcode setting "allowcookies" that, when set to "true", will allow YouTube cookies needed for custom end screens configured on youtube.com
* Tweak: Added "play" icon to main player for the gallery layout.
* Tweak: Added support for WordPress comments for the video posts saved as a custom post type in WordPress.
* Tweak: For video posts saved as WordPress custom post types, links found in the video description will be made into clickable HTML before being stored.
* Fix: Fixed a compatibility issue with the Complianz GDPR plugin.
* Fix: Setting the height and width of a feed using a shortcode would not work without including the unit of measure.
* Fix: The description length setting was not working properly.
* Fix: Related videos would sometimes not show up as expected when ending or pausing a video being played in the lightbox.
* Fix: Fixed PHP error when trying to load more posts and using the "on page" method of storing WP posts.

= 1.4.1 =
* Fix: Fixed several issues with GDPR Cookie Consent by Web Toffee integration.
* Fix: Displaying a YouTube Feed using Smash Balloon and displaying a YouTube video using Elementor's video widget no longer causes problems though some features for Feeds for YouTube may be limited.

= 1.4 =
* New: The locations of the YouTube feeds on your site will now be logged and listed on a single page for easier management. After this feature has been active for awhile, a "Feed Finder" link will appear next to the Feed Type setting on the plugin Settings page which allows you to see a list of all feeds on your site along with their locations.
* Tweak: Description text shown next to or below the feed will now make URLs in to clickable links.
* Tweak: Shortened description text will always end on a word instead of cutting off parts of words.
* Fix: Pro version of the Web Toffee GDPR Cookie Consent plugin was not being detected.
* Fix: Updated jQuery methods for compatibility with WordPress 5.7.
* Fix: Extra characters added to video IDs will be ignored when making a single video feed.
* Fix: Cache ID of playlist feeds changed to prevent conflicts.

= 1.3.1 =
* Fix: Fixed a problem with the order of upcoming live streams when two videos were streaming on the same day.
* Fix: When the GDPR setting was enabled and consent had not yet been given, the thumbnail for videos was not visible in the lightbox.
* Fix: Added PHP hooks for special actions after inserting or updating a custom post from a YouTube video.
* Fix: The Pro version of the Web Toffee GDPR Cookie Consent plugin was not being detected.
* Fix: Live streams that were not scheduled before streaming were not showing up in live stream feeds.

= 1.3 =
* New: Integrations with popular GDPR cookie consent solutions added. Visit the YouTube Feed settings page, Customize tab, GDPR section for more information.
* New: Exclude past live streams in live stream feeds using the setting found on the "Configure" tab or adding showpast="false" to your shortcode.
* Tweak: Live stream feeds are now ordered with currently streaming videos first, then other upcoming live streams sorted by closest upcoming live stream date, and lastly past live streams sorted by most recently streamed.
* Fix: Deleted live streams would still display in live stream feeds.
* Fix: When using page caching, changing the cache time to something other than hours would not work.
* Fix: Date that video was published would not always match what was shown on YouTube.com.

= 1.2.2 =
* Tweak: Live stream feeds will now show past live streams as well as upcoming live streams.
* Tweak: Added support for improved notices on the plugin settings page.
* Fix: Connecting an account that didn't have an associated YouTube channel would result in nothing happening. Now an empty channel is connected that can be used to create feeds.
* Fix: Iframe player now uses the video title as the iframe element title attribute.
* Fix: Call to action feature in video description was not using text for the button.
* Fix: Changed video info text color to inherit from your theme.

= 1.2.1 =
* Tweak: Added an HTML class "sby_current" to the currently playing thumbnail when using the gallery layout. Selecting a video will continue to show the hover information.
* Tweak: Centered the video title for the related videos end of action feature.

= 1.2 =
* New: Added compatibility with our new [Social Wall](http://smashballoon.com/social-wall/?utm_source=plugin-pro&utm_campaign=youtube-pro&utm_medium=readme) plugin, which allows you to combine feeds from Instagram, Facebook, Twitter, and YouTube into one social media "wall". If you are using our Smash Balloon All-Access Bundle then the Social Wall plugin is included at no additional cost. Just log into your account to download and install the plugin.

= 1.1.4 =
* Tweak: Added noticed explaining the account connection and what it can and can't be used for when connecting an account.
* Tweak: Removed refresh token from being required for a manual connection.
* Tweak: Cache name for custom search feeds changed to avoid cache conflicts.
* Fix: Caches of multiple feeds with filters would cause conflicts resulting in the wrong videos displaying in feeds.
* Fix: Improved performance of API requests for creating feeds.

= 1.1.3 =
* New: To help us improve the plugin we have added usage tracking so that we can understand what features and settings are being used, and which features matter to you the most. The plugin will send a report in the background once per week with your plugin settings and basic information about your website environment. No personal or sensitive data is collected (such as email addresses, YouTube account information, license keys, etc). You can opt-out by simply disabling the setting at: Feeds for YouTube > Customize > Advanced > Enable Usage Tracking. See [here](https://smashballoon.com/youtube-feed/docs/usage-tracking/) for more information.
* Tweak: Play icon changed to YouTube logo to comply with YouTube's terms of service.
* Tweak: More details are provided for API errors such as an API key not working after being created.
* Tweak: If an API key has not been entered, an account reconnection is required if trying to display videos from a previously unused channel and more than an hour has passed since the last account connection.
* Fix: Prevented a Fatal PHP error caused by retrieving videos without using an API key and either entering a non-existing channel ID or having allow_url_fopen disabled on the server.
* Fix: Non-latin characters were displaying incorrectly in titles and descriptions when a feed was using backup data.

= 1.1.2 =
* Tweak: Video thumbnails are cropped to a 9:16 aspect ratio to remove black bars at the top and bottom of the images.
* Tweak: Improved workarounds for video player issues caused by having YouTube iframes from other sources on the same page.
* Fix: YouTube video post type would sometimes cause issues with WordPress search feature.

= 1.1.1 =
* Tweak: Add setting to load iframes with the rest of the page to prevent conflict caused by other plugin's or theme's YouTube players being on the same page.
* Tweak: Upcoming and currently playing live streams are loaded into the feed differently allowing for up to 15 videos to display in a feed.
* Fix: Emoji in video description would cause the wrong thumbnail to be used for list layout.
* Fix: Line breaks in combination with double quotes in video descriptions would cause feed display issues.

= 1.1 =
* Tested with WordPress 5.4 update.
* New: Display video players in 9:16 ratio. To use 3:4 ratio, go to the "Customize" tab, "Video Experience" area.
* New: "Single" feed type added. Use this type to display individual YouTube videos using the video ID.
* New: Added a “Feeds for YouTube” Gutenberg block to use in the block editor, allowing you to easily add a feed to posts and pages.
* Tweak: Removed extra space between posts if there was no info for the video included in the feed.
* Tweak: Changed how live streaming video feed type works to be more reliable.
* Tweak: Non Feeds for YouTube admin notices are removed when viewing settings pages for Feeds for YouTube.
* Tweak: Slight styling changes for how end of video actions are displayed.
* Fix: Pagination for certain feed types would not work properly.
* Fix: PHP error related to using PHP version 5.3 or less.
* Fix: Background caching was not updating feed caches.

= 1.0 =
* Launched the Feeds for YouTube Pro plugin
