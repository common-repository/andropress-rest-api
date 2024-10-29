=== AndroPress REST API ===
Contributors: bhattaraisubash
Donate link: http://example.com/
Tags: wp-android-api, rest-api, wp-post-api
Requires at least: 4.6
Tested up to: 4.9.5
Stable tag: 1.4
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Get REST API response as JSON to android app, using token authenticated request .

== Description ==

Get REST API response as JSON to android app, using token authenticated request .

AndroPress is a secure way of requesting and receiving JSON data from your website to android app using REST service .
You can request for :

*   categories : https://yoursite.com/wp-admin/admin-post.php?action=andropress_rest_api&key=YOUR_APP_KEY&title=category
*   latest posts : https://yoursite.com/wp-admin/admin-post.php?action=andropress_rest_api&key=YOUR_APP_KEY&title=post
*   category posts : https://yoursite.com/wp-admin/admin-post.php?action=andropress_rest_api&key=YOUR_APP_KEY&title=post&category_name=CATEGORY_NAME
*   search result : https://yoursite.com/wp-admin/admin-post.php?action=andropress_rest_api&key=YOUR_APP_KEY&title=post&s=SEARCH_QUERY
*   posts with offset : https://yoursite.com/wp-admin/admin-post.php?action=andropress_rest_api&key=YOUR_APP_KEY&title=post&offset=OFFSET_VALUE
*   posts with posts_per_page : https://yoursite.com/wp-admin/admin-post.php?action=andropress_rest_api&key=YOUR_APP_KEY&title=post&posts_per_page=NO_OF_POSTS

Note : You can request with multiple parameters. For example you can pass 'category_name' as well as 'posts_per_page' in the url.

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'andropress-rest-api'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `andropress-rest-api` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

### CONFIGURATION

1. Log in to your WordPress Dashboard
2. Navigate to the 'AndroPress Rest' in the Dashboard
3. On the 'Generate New' tab , enter your 'App Name' and generate 'App Key' and click 'Submit'
4. Click on the 'My Apps' tab to view your Apps
5. Click on the App Name to view APP KEY
6. Copy the App Key
7. Use this key while requesting the API

== Screenshots ==

1. Create App
2. View Apps
3. View App Key

== Changelog ==

= 1.0 =
* initial stable release

= 1.1 =
* dialog modification

= 1.2 =
* woocommerce product rest api

= 1.3 =
* category_count

= 1.4 =
* posts on category

== Upgrade Notice ==

= 1.0 =
Initial Release

= 1.1 =
fixes bootstrap conflict with other plugins and dialog integrated to view app key

= 1.2 =
implements woocommerce product rest api as for post

= 1.3 =
now categories can be queried with number of category as provided using key count

= 1.4 =
categories can be queried with or without posts using posts=true or false
