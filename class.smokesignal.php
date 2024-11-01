<?php

class SmokeSignal {

	const TABLE_MESSAGES = 'smokesignal_messages';

	const STATE_NEW = 0;
	const STATE_READ = 1;
    const STATE_REMOVED = 2;


	private static $initiated = false;


	public static function init() {
		if (!self::$initiated) {
            self::init_hooks();
            self::enqueue();
		}
	}

	public static function table_messages() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_MESSAGES;
	}

	// -------------------------------------------
	// HOOKS
	// -------------------------------------------
	public static function init_hooks() {
		self::$initiated = true;

		add_action('admin_menu', array('SmokeSignal', 'admin_menu'));

		add_action('wp_loaded', array('SmokeSignal', 'process_insert_message_form'));
        add_action('wp_loaded', array('SmokeSignal', 'process_insert_group_message_form'));
        add_action('wp_loaded', array('SmokeSignalGroup', 'process_new_group_form'));
        add_action('wp_loaded', array('SmokeSignalGroup', 'process_edit_group_form'));

        add_action('wp_before_admin_bar_render', array('SmokeSignal', 'admin_toolbar_notification'), 50);
//        add_action('admin_menu', array('SmokeSignal', 'admin_toolbar_notification'), 99);
//        add_action('admin_bar_menu', array('SmokeSignal', 'admin_toolbar_test'), 99);

        if(!empty($_GET['view']) && $_GET['view'] == 'reply') {
            add_action('admin_footer', array('SmokeSignal', 'js_ajax_load_more_messages'));
        }

        if(!empty($_GET['page']) && $_GET['page'] == 'smokesignal') {
            add_action('admin_head', array('SmokeSignal', 'wpss_admin_js'));
        }
        
        add_action('wp_ajax_smokesignal_load_more_messages', array('SmokeSignal', 'ajax_load_more_messages'));
        add_action('wp_ajax_smokesignal_remove_message', array('SmokeSignal', 'ajax_remove_message'));
	}


    public static function enqueue() {
        wp_enqueue_style( 'thickbox' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'media-upload' );
    }

    // load script to admin
    public static function wpss_admin_js() {
        $url = site_url() . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/js/script.js';
        echo "<script type='text/javascript' src='$url'></script>";
        echo "
            <style type='text/css'>
                .removeMessage {
                    display:none;
                }
                .message:hover .removeMessage {
                    display:inline;
                }
            </style>
        ";
    }


    public static function admin_menu() {
		add_users_page(
            __('User Messages', 'smokesignal'), __('Messages', 'smokesignal'), 'read', 'smokesignal', array('SmokeSignal', 'display')
		);
        if(current_user_can('manage_options')) {
            add_users_page(
                __('User Groups', 'smokesignal'), __('Groups', 'smokesignal'), 'read', 'smokesignalgroup', array('SmokeSignalGroup', 'display')
            );
        }
	}

    public static function admin_toolbar_notification($wp_admin_bar) {
        $new_messages = self::get_new_messages();

        if(!empty($new_messages)) {
            global $wp_admin_bar;

            $args = array(
                'id' => 'wp_plugin_smokesignal',
                'href' => admin_url('/users.php?page=smokesignal', 'http'),
            );

            $display = '<span class="mbe-ab-text-active">'
                . __('You have new messages', 'smokesignal') . ' (' . count($new_messages) . ')</span>';

            $args['title'] = $display;
            $wp_admin_bar->add_node($args);
        }
    }


    // -------------------------------------------
    // JAVASCRIPT
    // -------------------------------------------
    public static function js_ajax_load_more_messages() {
        ?>
        <script type="text/javascript" >
            // Load first messages
            jQuery(document).ready(function($) {
                loadMore();
            });

            /**
             * Load messages
             */
            function loadMore() {
                jQuery("#loading").show();

                var lastId = 0;
                if(jQuery('.last-id').length > 0) {
                    lastId = jQuery('.last-id').last().val();
                }

                var data = {
                    'action': 'smokesignal_load_more_messages',
                    'lastId': lastId,
                    'userId': <?= isset($_GET['user_id']) ? $_GET['user_id'] : 0 ?>
                };

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(ajaxurl, data, function (response) {
                    jQuery('#loading').hide();
                    jQuery('#messages').append(response);

                    // Hide button, if all messages are displayed
                    if(jQuery("#no-more-messages").length > 0) {
                        jQuery('#load-more-button').hide();
                        jQuery('#no-more-messages-caption').show();
                    }
                });
            }

            function removeMessage(id) {

                var data = {
                    'action': 'smokesignal_remove_message',
                    'messageId': id
                };

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(ajaxurl, data, function (response) {
                    jQuery('#message_' + id).fadeOut(1000).slideUp();
                });
            }
        </script>
        <?php
    }

    // -------------------------------------------
    // AJAX
    // -------------------------------------------
    public static function ajax_load_more_messages() {
        $lastId = $_POST['lastId'];
        $userId = $_POST['userId'];

        $query = array(
            'fields' => array('id', 'user_nicename'),
            'include' => array($userId),
            ''
        );
        $user = new WP_User_Query($query);

        $args = array(
            'user' => $user->results[0],
            'messages' => self::get_communication($userId, $lastId),
        );

        echo self::view('single_message', $args);


        wp_die(); // this is required to terminate immediately and return a proper response
    }

    public static function ajax_remove_message() {
        $messageId = $_POST['messageId'];


        global $wpdb;

        $table_name = self::table_messages();

        $wpdb->update(
            $table_name,
            array(
                'state' => self::STATE_REMOVED,
            ),
            array(
                'id' => $messageId,
            ),
            array(
                '%d',
            ),
            array(
                '%d',
            )
        );


        wp_die(); // this is required to terminate immediately and return a proper response
    }

	// -------------------------------------------
	// DISPLAY
	// -------------------------------------------
	public static function display() {
        if(!empty($_GET['view'])) {
            switch($_GET['view']) {
                case 'new-message':
                    self::display_new_message();
                    break;
                case 'group-message';
                    self::display_new_group_message();
                    break;
                case 'reply':
                    if (!empty($_GET['user_id'])) {
                        self::display_reply($_GET['user_id']);
                    } else {
                        self::display_my_messages();
                    }
                    break;
                default:
                    self::display_my_messages();
            }
        } else {
            self::display_my_messages();
        }
	}

	public static function display_my_messages() {
		$new_messages = self::get_new_messages();
		$exclude_users = array(0);
		foreach($new_messages as $message) {
			$exclude_users[] = $message->user_id;
		}
		$read_messages = self::get_read_messages($exclude_users);

		$args = array(
			'new_messages' => $new_messages,
			'read_messages' => $read_messages
		);
		self::view('list', $args);
	}

	public static function display_new_message() {
		$query = array(
			'fields' => array('id', 'user_nicename'),
			'orderby' => 'user_nicename',
			'exclude' => array(get_current_user_id())
		);
		$users = new WP_User_Query($query);

		$args = array(
			'users' => $users->results,
		);
		self::view('new', $args);
	}

	public static function display_new_group_message() {
        $args = array(
            'groups' => SmokeSignalGroup::get_all_groups(),
        );

		self::view('group', $args);
	}

	public static function display_reply($user_id) {
		self::set_messages_from_user_as_read($user_id);

		$query = array(
			'fields' => array('id', 'user_nicename'),
			'include' => array($user_id),
		);
		$user = new WP_User_Query($query);

		$args = array(
			'user' => $user->results[0],
			'messages' => self::get_communication($user_id),
		);
		self::view('reply', $args);
	}

	public static function view($view, $args = array()) {
        if(!empty($path)) {
            $path .= '/';
        }
		$file = SMOKESIGNAL_PLUGIN_DIR . "views/$view.php";
		include($file);
	}

    // -------------------------------------------
    // SEND EMAIL
    // -------------------------------------------
    public static function send_notification_about_new_message($user_id, $message) {
        $user = get_user_by('id', $user_id);
        /*
        $user_result = new WP_User_Query(array(
            'search'         => intval($user_id),
	        'search_columns' => array('ID'),
        ));
        $user = $user_result->results[0];
die('aaa');
*/
        $current_user = wp_get_current_user();

        $options = get_option('smokesignal_options');
        $emailExcerpt = false;
        if(!empty($options['email_excerpt']) && $options['email_excerpt'] == true) {
            $emailExcerpt = true;
        }

        if($emailExcerpt) {
            $message = substr( $message, 0, strrpos( substr( $message, 0, 50), ' ' ) );
            $message .= '...';
        }

        if(!empty($user)) {
            $subject = get_bloginfo('name') . ": " . __('You have new messages', 'smokesignal');

            $body = $current_user->display_name . " " . __('has wrote you: ', 'smokesignal');
            $body .= "\n\n";
            $body .= $message;
            $body .= "\n\n";
            $body .= __('You can reply in your user section at', 'smokesignal') . " " . get_bloginfo('url');
            $body .= "\n\n";
            $body .= __('... and have a nice day :)', 'smokesignal');

            wp_mail($user->user_email, $subject, $body);
        }
    }


	// -------------------------------------------
	// DATABASE OPERATIONS
	// -------------------------------------------
	public static function process_insert_message_form() {
		if(isset($_POST['submit_new_message'])) {
			$success = false;
			if(isset($_POST['to_name'])) {
			    $user = get_user_by('login', $_POST['to_name']);
			    $_POST['to_id'] = $user->ID;
			}
			if($_POST['to_id'])
			    $success = self::insert_message($_POST['to_id'], $_POST['message'], self::STATE_NEW);
            if($success) {
                self::send_notification_about_new_message($_POST['to_id'], $_POST['message']);
            }


			$params = array('page' => 'smokesignal', 'view' => 'reply', 'user_id' => $_POST['to_id']);
			wp_redirect(add_query_arg($params));
		}
	}

    public static function process_insert_group_message_form() {
        if(isset($_POST['submit_new_group_message'])) {
            $group_id = intval($_POST['group_id']);

            // Get users in the group
            $users = SmokeSignalGroup::get_users_from_group($group_id);

            foreach($users as $user) {
                if($user->user_id != get_current_user_id()) {
                    $success = self::insert_message($user->user_id, $_POST['message'], self::STATE_NEW, $group_id);
                    if($success) {
                        self::send_notification_about_new_message($user->user_id, $_POST['message']);
                    }
                }
            }

            $params = array('page' => 'smokesignal', 'view' => '');
            wp_redirect(add_query_arg($params));
        }
    }

	/**
	 *
	 * @global type $wpdb
	 * @param type $from
	 * @param type $to
	 * @param type $message
	 */
	public static function insert_message($to, $message, $state, $group_id = 0) {
		global $wpdb;

		$table_name = self::table_messages();

		return $wpdb->insert(
			$table_name,
			array(
				'from_id' => get_current_user_id(),
				'to_id' => $to,
				'message' => $message,
				'state' => $state,
				'created' => current_time('mysql'),
                'group_id' => $group_id,
			),
            array(
                '%d',
                '%d',
                '%s',
                '%d',
                '%s',
                '%d',
            )
		);
	}

	public static function set_messages_from_user_as_read($user_id) {
		global $wpdb;

		$table_name = self::table_messages();

		$wpdb->update(
			$table_name,
			array(
				'state' => self::STATE_READ,
			),
			array(
				'from_id' => $user_id,
				'state' => self::STATE_NEW,
			),
			array(
				'%d',
			),
			array(
				'%d',
				'%d',
			)
		);
	}

	public static function get_new_messages() {
		global $wpdb;
		$messages_table_name = self::table_messages();
		$current_user_id = get_current_user_id();

		$querystr = "
			SELECT $wpdb->users.user_nicename,
				$wpdb->users.id as user_id,
				$messages_table_name.message,
				$messages_table_name.created,
				count(wp_smokesignal_messages.id) as count_new
			FROM $wpdb->users
				LEFT JOIN $messages_table_name ON $messages_table_name.from_id = $wpdb->users.id
			WHERE
				$messages_table_name.to_id = $current_user_id
					AND $messages_table_name.state = " . self::STATE_NEW . "
			GROUP BY $wpdb->users.id
			ORDER BY $messages_table_name.created DESC
		";
		return $wpdb->get_results($querystr, OBJECT);
	}

	public static function get_read_messages($exclude_users) {
		global $wpdb;
		$messages_table_name = self::table_messages();
		$current_user_id = get_current_user_id();

		$querystr = "
			SELECT $wpdb->users.user_nicename,
				$wpdb->users.id as user_id,
				$messages_table_name.message,
				$messages_table_name.created,
				0 as count_unread
			FROM $wpdb->users
				LEFT JOIN $messages_table_name ON $messages_table_name.from_id = $wpdb->users.id
			WHERE
				$messages_table_name.to_id = $current_user_id
					AND $wpdb->users.id NOT IN (" . implode(',', $exclude_users) . ")
			GROUP BY $wpdb->users.id
			ORDER BY $messages_table_name.created DESC
		";

		$querystr = "
SELECT * FROM
(
(
SELECT
  (SELECT user_nicename FROM $wpdb->users WHERE id = m1.from_id) as user_nicename,
  m1.from_id as user_id,
  m1.message,
  m1.created,
  0 as count_unread,
  1 as direction,
  m1.from_id as grp_id
FROM $messages_table_name m1 LEFT JOIN $messages_table_name m2
  ON (m1.from_id = m2.from_id AND m1.created < m2.created)
WHERE m2.id IS NULL
  AND m1.to_id = $current_user_id
  AND m1.from_id NOT IN (" . implode(',', $exclude_users) . ")
)
UNION
(
SELECT
  (SELECT user_nicename FROM $wpdb->users WHERE id = m1.to_id) as user_nicename,
  m1.to_id as user_id,
  m1.message,
  m1.created,
  0 as count_unread,
  0 as direction,
  m1.to_id as grp_id
FROM $messages_table_name m1 LEFT JOIN $messages_table_name m2
  ON (m1.to_id = m2.to_id AND m1.created < m2.created)
WHERE m2.id IS NULL
  AND m1.from_id = $current_user_id
)
) xxx
ORDER BY created DESC
";
		$res = $wpdb->get_results($querystr, OBJECT);
	}	

	public static function get_communication($user_id, $lastId = 0) {
		global $wpdb;
		$table_messages = self::table_messages();
        $table_groups = SmokeSignalGroup::table_groups();
		$current_user_id = get_current_user_id();

		$querystr = "
			SELECT
				m.from_id,
				m.message,
				m.created,
				m.id,
				g.name AS group_name
			FROM $table_messages m
			LEFT JOIN $table_groups g ON g.id = m.group_id
			WHERE
				(
				    (m.to_id = $current_user_id
                    AND	m.from_id = %d)
                    OR
                    (m.to_id = %d
                    AND	m.from_id = $current_user_id)
                )
                AND m.state != " . self::STATE_REMOVED . "
		";

        if(!empty($lastId)) {
            $querystr .= "
                AND m.id < $lastId
            ";
        }

        $querystr .= "
            ORDER BY m.created DESC
			LIMIT 5
        ";

		return $wpdb->get_results($wpdb->prepare($querystr, $user_id, $user_id), OBJECT);

	}


    public static function create_links($text) {
        return preg_replace('/((http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/',
            '<a href="\1" target="_blank">\1</a>',
            htmlspecialchars($text));
    }


}
