<?php
/**
 * @package SmokeSignal
 */

/**
 * Plugin Name: SmokeSignal
 * Plugin URI: http://www.danielhrenak.sk
 * Description: A brief description of the plugin.
 * Version: 1.2.7
 * Author: Daniel Hrenak
 * Author URI: http://www.danielhrenak.sk
 * Text Domain: smokesignal
 * License: A short license name. Example: GPL2
 */

/*  Copyright 2015  Daniel Hrenak  (email : daniel.hrenak@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Constants
define('SMOKESIGNAL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SMOKESIGNAL_PLUGIN_DIR', plugin_dir_path(__FILE__));


// Translation
add_action('plugins_loaded', 'smokesignal_load_translation_file');

defined('ABSPATH') or die("No script kiddies please!");
global $smokesignal_db_version;
$smokesignal_db_version = '2.0';
//add_option( "smokesignal_db_version", $smokesignal_db_version );

// Installation files
register_activation_hook(__FILE__, 'smokesignal_install');
register_deactivation_hook(__FILE__, 'smokesignal_uninstall');


if (true || is_admin()) {
    require_once(SMOKESIGNAL_PLUGIN_DIR . 'class.smokesignal.php');
    require_once(SMOKESIGNAL_PLUGIN_DIR . 'class.smokesignalgroup.php');
    require_once(SMOKESIGNAL_PLUGIN_DIR . 'options.php');

    add_action('init', array('SmokeSignal', 'init'));
    add_action('plugins_loaded', 'smokesignal_update_db_check');

}


/**
 * @global $wpdb
 */
function smokesignal_install()
{
    global $wpdb;
    global $smokesignal_db_version;
    $installed_db_version = get_option( "smokesignal_db_version" );

    $charset_collate = $wpdb->get_charset_collate();
    $table_messages = $wpdb->prefix . "smokesignal_messages";
    $table_groups = $wpdb->prefix . "smokesignal_groups";
    $table_users_groups = $wpdb->prefix . "smokesignal_users_groups";

    if ($installed_db_version != $smokesignal_db_version ) {

        $sql = "

        CREATE TABLE $table_messages (
            id INT(11) NOT NULL AUTO_INCREMENT,
            from_id INT(11) NOT NULL COMMENT 'ID of message sender',
            to_id INT(11) NOT NULL COMMENT 'ID of message receiver',
            group_id INT(11) NULL COMMENT 'ID of group (if it was set)',
            message TEXT NOT NULL COMMENT 'message body',
            state TINYINT(1) DEFAULT 0 COMMENT 'state of message: 0 - created, 1 - seen',
            created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        )$charset_collate;

        CREATE TABLE $table_groups (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL COMMENT 'Name of the group',
            created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        )$charset_collate;

        CREATE TABLE $table_users_groups (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL COMMENT 'ID of user from wp table users',
            group_id INT NOT NULL COMMENT 'ID of group from table smokesignal_groups',
            created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        )$charset_collate;
        ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('smokesignal_db_version', $smokesignal_db_version);
    }
}

function smokesignal_uninstall()
{
    global $wpdb;
    $table_messages = $wpdb->prefix . "smokesignal_messages";
    $table_groups = $wpdb->prefix . "smokesignal_groups";
    $table_users_groups = $wpdb->prefix . "smokesignal_users_groups";

    $sql = "DROP TABLE IF EXISTS $table_messages;";
    $wpdb->query($sql);

    $sql = "DROP TABLE IF EXISTS $table_users_groups;";
    $wpdb->query($sql);

    $sql = "DROP TABLE IF EXISTS $table_groups;";
    $wpdb->query($sql);

    delete_option('smokesignal_db_version');
}

function smokesignal_load_translation_file()
{
    // relative path to WP_PLUGIN_DIR where the translation files will sit:
    $plugin_path = plugin_basename(dirname(__FILE__) . '/translations');
    $bool = load_plugin_textdomain('smokesignal', false, $plugin_path);
}

function smokesignal_update_db_check() {
    global $smokesignal_db_version;
    if(get_site_option('smokesignal_db_version') != $smokesignal_db_version) {
        smokesignal_install();
    }
}
