=== Auto Post ===
Contributors:       Michael Uno, miunosoft
Donate link:        http://en.michaeluno.jp/donate
Tags:               auto post, post, posts, automation, automatic, task scheduler, module, action, task, event, management, utility, tool
Requires at least:  3.7
Tested up to:       4.2.2
Stable tag:         1.0.0
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

Creates posts automatically serving as a Task Scheduler module.

== Description ==

Say, you have a plugin that converts a certain shortcode into generated data and you don't need to write anything else.

In that case, just use this plugin to create posts automatically with the preset text contents. Then it will make your site a content generator.

<h4>Requirements</h4>
- the [**Task Scheduler**](http://wordpress.org/plugins/task-scheduler/) plugin.


== Installation ==

= Install = 

1. Upload **`auto-post.php`** and other files compressed in the zip folder to the **`/wp-content/plugins/`** directory.,
2. Activate the plugin through the `Plugins` menu in WordPress.

= How to Use =  
1. Make sure you have installed and activated the [**Task Scheduler**](http://wordpress.org/plugins/task-scheduler/) plugin.
2. Define a `Task` via **Dashboard** -> **Task Scheduler** -> **Add New Task**
3. In the `Select Action` screen of the wizard form, select the `Auto Post` action.

== Other Notes ==

== Frequently Asked Questions ==

== Screenshots ==

1. ***Wizard Page***
2. ***Wizard Page 2***

== Changelog ==

= 1.1.0 - 07/03/2015 =
- Added the ability to set custom fields (post meta) (requires Task Scheduler 1.0.2 or above).
- Added the ability to set custom post type slug when a desired post type is not listed.
- Fixed an issue that post type could not be set.

= 1.0.0 =
- Released.