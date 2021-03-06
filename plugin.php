<?php
/*
Plugin Name: wpGrade Shortcodes
Plugin URI: http://pixelgrade.com
Description: Adds shortcodes to your wordpress editor
Version: 1.0
Author: Pixelgrade Media
Author URI: http://pixelgrade.com
Author Email: contact@pixelgrade.com
License:

  Copyright 2013 contact@pixelgrade.com

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

if (!defined('ABSPATH')) die('-1');

class WpGradeShortcodes {

    protected static $plugin_dir;

    function __construct() {

        $this->plugin_dir = dirname( plugin_basename( __FILE__ ) );
		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.

	    add_action( 'init', array( $this, 'add_wpgrade_shortcodes_button' ) );
        add_action( 'init', array( $this, 'create_wpgrade_shortcodes' ) );

        // ajax load for modal
        if ( is_admin() ) {
            add_action('wp_ajax_wpgrade_get_shortcodes_modal', array($this, 'wpgrade_get_shortcodes_modal'));
        }

	} // end constructor

	public function plugin_textdomain() {
		$domain = 'wpGrade_txt';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	} // end plugin_textdomain

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
//        wp_register_style('slider-ui', 'http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_style( 'wpgrade-shortcodes-reveal-styles', plugins_url( 'wpgrade-shortcodes/css/base.css' ), array(  ) );
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

    } // end register_admin_scripts

	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
	} // end register_plugin_styles

	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
	} // end register_plugin_scripts

	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

	function add_wpgrade_shortcodes_button() {
        if ( current_user_can('edit_posts') ) {
            add_filter('mce_external_plugins', array( $this, 'addto_mce_wpgrade_shortcodes') );
            add_filter('mce_buttons', array( $this, 'register_wpgrade_shortcodes_button') );
        }
	} // end action_method_name

	function register_wpgrade_shortcodes_button($buttons) {
        array_push($buttons, "wpgrade");
        return $buttons;
	} // end filter_method_name

    function addto_mce_wpgrade_shortcodes($plugin_array) {
        $plugin_array['wpgrade'] = plugins_url( 'wpgrade-shortcodes/js/add_shortcode.js' , dirname(__FILE__) ) ;
        return $plugin_array;
    }

    public function wpgrade_get_shortcodes_modal(){
        ob_start();
        include('views/shortcodes-modal.php');
        echo json_encode(ob_get_clean());
        die();
    }

    public function create_wpgrade_shortcodes(){
        include_once('shortcodes.php');
    }

} // end class

$WpGradeShortcodes = new WpGradeShortcodes();
