<?php
/*
 * Plugin Name: WP Upwork 
 * Version: 1 
 * Plugin URI: http://advchaweb.com/
 * Description: A plugin to connect to upwork API
 * Author: Satria Faestha
 * Author URI: http://advchaweb.com/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! defined('WP_UPWORK_PLUGIN_DIR' ) ) {
    define('WP_UPWORK_PLUGIN_DIR', dirname(__FILE__));
}

require __DIR__ . '/vendor/autoload.php';

if(!class_exists('WP_Upwork'))
{
    class WP_Upwork{
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            //Initialize settings
            require_once(WP_UPWORK_PLUGIN_DIR . '/includes/settings.php');
            $WP_Upwork_Settings=new WP_Upwork_Settings();
            
            //Add Custom Page Upwork Template
            require_once(WP_UPWORK_PLUGIN_DIR . '/includes/template.php');
            //$WP_Upwork_Template=new WP_Upwork_Template();
            add_action( 'plugins_loaded', array( 'WP_Upwork_Template', 'get_instance' ) );
            
            // Add settings link to plugins page
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__) , array( &$this , 'add_settings_link' ) );
        }
        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */     
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate
        
        // Add the settings link to the plugins page
        function add_settings_link($links)
        { 
            $settings_link = '<a href="options-general.php?page=wp_upwork">Settings</a>'; 
            array_unshift($links, $settings_link); 
            return $links; 
        }
    } // END class WP_Upwork
} // END if(!class_exists('WP_Upwork'))

//Instantiate WP_Upwork class
if(class_exists('WP_Upwork')){
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('WP_Upwork', 'activate'));
    register_deactivation_hook(__FILE__, array('WP_Upwork', 'deactivate'));

    // instantiate the plugin class
    $wp_upwork = new WP_Upwork();
}
