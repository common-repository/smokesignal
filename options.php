<?php
class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    private $recipientInputTypes = array();

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );

        $this->recipientInputTypes = array(
            'select' => __('Select from users', 'smokesignal'),
            'input' =>  __('Write username', 'smokesignal'),
        );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'SmokeSignal',
            'manage_options',
            'my-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('smokesignal_options');
        ?>
        <div class="wrap">
            <h2><?= __('SmokeSignal Settings', 'smokesignal') ?></h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'my_option_group', // Option group
            'smokesignal_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            __('General settings', 'smokesignal'), // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'email_excerpt', // ID
            __('Email notifications', 'smokesignal'), // Title
            array( $this, 'email_excerpt_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );

        add_settings_field(
            'recipient_input_type',
            __('Recipient input type', 'smokesignal'),
            array( $this, 'recipient_input_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['email_excerpt'] ) )
            $new_input['email_excerpt'] = boolval( $input['email_excerpt'] );

        if( isset( $input['recipient_input_type'] ) )
            $new_input['recipient_input_type'] = sanitize_text_field( $input['recipient_input_type'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
//        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function email_excerpt_callback()
    {
        echo
            '<input type="checkbox" id="email_excerpt" name="smokesignal_options[email_excerpt]"
                    ' . checked(1 == $this->options['email_excerpt'], true, false) . '/>'
        ;
        echo __('send only excerpt of message', 'smokesignal');
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function recipient_input_callback()
    {
        echo "<select name='smokesignal_options[recipient_input_type]' id='recipient_input_type'>";
        foreach($this->recipientInputTypes as $inputType => $text) {
            echo "<option value='$inputType'" . selected($this->options['recipient_input_type'] == $inputType, true, false) . ">$text</option>";
        }
        echo "</select>";
    }
}

if( is_admin() )
    $my_settings_page = new MySettingsPage();