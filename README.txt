=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://github.com/MaxOrelus
Tags: custom post-type, content distribution platform, cdp
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

CDP Video Post-Type - Internal content used for CDP integration.

== Description ==

CDP Video Post-Type enables you to create video content. The plugin is not publicly accessible, so a page url will not be generated for this content. It serves only to submit video content to be index into the CDP.

== Installation ==

This section describes how to install the plugin and get it working.

== Developers ==

At the moment, the only files you should edit/modify our:

* `includes/class-cdp-video-post-type-loader.php`
* `includes/class-cdp-video-post-type-rest-controller.php`

e.g.

1. Upload `cdp-video-post-type.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

`<?php code(); // goes in backticks ?>`
