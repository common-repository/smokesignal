<?php

class SmokeSignalGroup {

	const TABLE_GROUPS = 'smokesignal_groups';
    const TABLE_USERS_GROUPS = 'smokesignal_users_groups';

	public static function table_groups() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_GROUPS;
	}

    public static function table_users_groups() {
        global $wpdb;
        return $wpdb->prefix . self::TABLE_USERS_GROUPS;
    }

    public static function table_users() {
        global $wpdb;
        return $wpdb->prefix . "users";
    }

    // -------------------------------------------
    // DISPLAY
    // -------------------------------------------
    public static function display() {
        if(!empty($_GET['view'])) {
            switch($_GET['view']) {
                case 'new-group':
                    self::display_new_group();
                    break;
                case 'edit-group':
                    self::display_edit_group();
                    break;
                default:
                    self::display_all_groups();
            }
        } else {
            self::display_all_groups();
        }
    }


    public static function display_all_groups() {
        $groups = self::get_all_groups();

        $args = array(
            'groups' => $groups,
        );
        SmokeSignal::view('groups/list', $args);
    }

    public static function display_new_group() {
        $args = array();
        SmokeSignal::view('groups/new', $args);
    }

    public static function display_edit_group() {
        $args = array(
            'group' => self::get_group(intval($_GET['group_id'])),
            'users' => self::get_users_groups(intval($_GET['group_id'])),
        );
        SmokeSignal::view('groups/edit', $args);
    }


    // -------------------------------------------
    // DATABASE OPERATIONS
    // -------------------------------------------
    public static function process_new_group_form() {
        if(isset($_POST['submit_new_group'])) {
            $success = self::insert_group($_POST['group_name']);

            $params = array('page' => 'smokesignalgroup', 'view' => 'list');
            wp_redirect(add_query_arg($params));
        }
    }

    public static function process_edit_group_form() {
        if(isset($_POST['submit_edit_group'])) {
            $success = self::update_group($_POST['group_id'], $_POST['group_name']);
            $success = self::update_users_groups($_POST['group_id'], $_POST['user']);

            $params = array('page' => 'smokesignalgroup', 'view' => 'list');
            wp_redirect(add_query_arg($params));
        }
    }

    public static function insert_group($name) {
        global $wpdb;

        $table_name = self::table_groups();

        return $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'created' => current_time('mysql'),
            ),
            array(
                '%s',
                '%s',
            )
        );
    }

    public static function update_group($id, $name) {
        global $wpdb;

        $table_name = self::table_groups();

        return $wpdb->update(
            $table_name,
            array(
                'name' => $name,
            ),
            array(
                'id' => $id,
            ),
            array(
                '%s',
            )
        );
    }

    public static function update_users_groups($group_id, $users) {
        global $wpdb;
        $table_users_groups = self::table_users_groups();

        $wpdb->delete(
            $table_users_groups,
            array(
                'group_id' => $group_id
            )
        );

        foreach($users as $user_id => $ok) {
            $wpdb->insert(
                $table_users_groups,
                array(
                    'group_id' => $group_id,
                    'user_id' => $user_id,
                ),
                array(
                    '%d',
                    '%d',
                )
            );
        }
    }

    public static function get_all_groups() {
        global $wpdb;
        $table_groups = self::table_groups();
        $table_users_groups = self::table_users_groups();
        $table_users = self::table_users();

        $querystr = "
			SELECT g.id, g.name, GROUP_CONCAT(u.display_name SEPARATOR ', ') as user_names
			FROM $table_groups g
			LEFT JOIN $table_users_groups ug ON g.id = ug.group_id
			LEFT JOIN $table_users u ON u.id = ug.user_id
			GROUP BY g.id
			ORDER BY name ASC
		";
        return $wpdb->get_results($querystr, OBJECT);
    }

    public static function get_group($id) {
        global $wpdb;
        $table_groups = self::table_groups();

        $querystr = "
			SELECT *
			FROM $table_groups
			WHERE id = $id
		";
        return $wpdb->get_row($querystr, OBJECT);
    }

    public static function get_users_groups($group_id) {
        global $wpdb;
        $table_groups = self::table_groups();
        $table_users_groups = self::table_users_groups();
        $table_users = self::table_users();

        $querystr = "
			SELECT u.display_name, u.id AS user_id,
			IF(u.id IN (SELECT user_id FROM $table_users_groups WHERE group_id = $group_id), $group_id, 0)AS group_id
			FROM $table_users u
			GROUP BY user_id
			ORDER BY u.display_name
		";
        return $wpdb->get_results($querystr, OBJECT);
    }

    public static function get_users_from_group($group_id) {
        global $wpdb;
        $table_users_groups = self::table_users_groups();

        $querystr = "
			SELECT user_id
			FROM $table_users_groups
			WHERE group_id = $group_id
		";
        return $wpdb->get_results($querystr, OBJECT);
    }
}
