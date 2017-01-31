<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if(!class_exists('WP_Upwork_Settings'))
{
    class WP_Upwork_Settings{
        /**
         * Construct the plugin object
         */
        protected $action       = 'wp_upwork';
        protected $option_name  = 'wp_upwork';

        public function __construct()
        {
            // register actions
            // Register plugin settings
            add_action('admin_init', array(&$this, 'admin_init'));
            // Add settings page to menu
            add_action('admin_menu', array(&$this, 'add_menu'));
        } // END public function __construct
        
        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init(){
            // register your plugin's settings
            register_setting( 'wp_upwork-group', 'wp_upwork_api_key' );
            register_setting( 'wp_upwork-group', 'wp_upwork_api_secret' );
            // add your settings section
            add_settings_section(
                'wp_upwork-section', 
                __('Upwork API Settings'), 
                array( &$this, 'settings_section_wp_upwork'), 
                'wp_upwork_settings'
            );
            // add your setting's fields
            add_settings_field(
                'wp_upwork-api_key', 
                __('API Key'), 
                array(&$this, 'settings_field_input_text'), 
                'wp_upwork_settings', 
                'wp_upwork-section',
                array(
                    'field' => 'wp_upwork_api_key'
                )
            );
            // add your setting's fields
            add_settings_field(
                'wp_upwork-wp_upwork_api_secret', 
                __('API Secret'), 
                array(&$this, 'settings_field_input_text'), 
                'wp_upwork_settings', 
                'wp_upwork-section',
                array(
                    'field' => 'wp_upwork_api_secret'
                )
            );
        }

        public function settings_section_wp_upwork()
        {
            // Think of this as help text for the section.
            _e('Upwork API Setting:','wp_upwork');
        }
        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args)
        {
            // Get the field name from the $args array
            $field = esc_attr($args['field']);
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
        } // END public function settings_field_input_text($args)
        
        /**
         * add a menu
         */     
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
            add_options_page(
                __('WP Upwork Settings'), 
                __('WP Upwork Settings'), 
                'manage_options', 
                'wp_upwork', 
                array(&$this, 'plugin_settings_page')
            );
        } // END public function add_menu()

        /**
         * Menu Callback
         */     
        public function plugin_settings_page()
        {
            if(!current_user_can('manage_options'))
            {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            // Render the settings template
            ?>
            <div class="wrap">
                <h2><?php _e('Upwork API Settings','wp_upwork'); ?></h2>
                <p><?php _e('You\'ll need to go to the <a href="https://developers.upwork.com">Upwork Developer Page</a> to setup your project and setup the values below.'); ?></p>
                <form action="options-general.php?page=wp_upwork" method="POST">
                    <?php settings_fields( 'wp_upwork-group' ); ?>
                    <?php do_settings_sections( 'wp_upwork_settings' ); ?>
                    <?php submit_button(__('Save Keys')); ?>
                </form>
            </div>
            <?php
        } // END public function plugin_settings_page()
    } // END class WP_Upwork_Settings
} // END if(!class_exists('WP_Upwork_Settings'))
