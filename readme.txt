=== SmokeSignal ===
Contributors: .dan
Tags: internal communication, users, messages, IM, messenger, simple, files
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TXQAVEQ8EZDPW
Requires at least: 3.0.1
Tested up to: 4.8.1
Stable tag: 1.2.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send internal messages between registered users in admin section.

== Description ==
This plugin allows you to communicate with other registered users of you wordpress blog/website/portal easily inside admin interface. There is possibility to create groups, assign users to them and communicate only with users from the selected group.

== Installation ==
You can download and install SmokeSignal using the built in WordPress plugin installer. If you download SmokeSignal manually, make sure it is uploaded to /wp-content/plugins/smokesignal/.

Activate SmokeSignal in the Plugins admin panel using the Activate link.

== Screenshots ==
1. You will get notification, when somebody writes you new message.
2. You can see the entire communication with one user on single page.

== Changelog ==
= 1.2.7 =
* (Bugfix) possible XSS vulnerability

= 1.2.6 =
* New minor version, typo

= 1.2.5 =
* Added options for sending only excerpt of message and input type for recipients
* Added possibility to remove message

= 1.2.4 =
* Czech translation
* (Bugfix) Fixed database tables creation
* (Bugfix) Fixed notifications about new messages

= 1.2.3 =
* (Bugfix) Email notifications for group messages were not sent.

= 1.2.2 =
* (Bugfix) Editation of articles and pages was broken. Sry. 

= 1.2.1 =
* Common users can\'t insert files

= 1.2.0 =
* You can upload file and insert link into messages automatically
* Hyperlinks are clickable (active) in the message 

= 1.1.1 =
* (Bugfix) Name of group was not inserted properly for some version of PHP 

= 1.1.0 =
* Possibility to create groups, assign users to the groups and send messages only to the users within groups.

= 1.0.2 =
* Messages in feed are loading after Load more button is clicked

= 1.0.1 =
* (Bugfix) Sending of notification emails

= 1.0.0 =
* First version with basic messaging and notifications.

== Upgrade Notice ==
Possibility to upload files and automatically insert into message