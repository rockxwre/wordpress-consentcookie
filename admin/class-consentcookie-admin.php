<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/humanswitch/wordpress-consentcookie
 * @since      1.0.0
 *
 * @package    ConsentCookie
 * @subpackage ConsentCookie/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ConsentCookie
 * @subpackage ConsentCookie/admin
 * @author     Ramon Rockx <ramon@humanswitch.io>
 */
class ConsentCookie_Admin {
    
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;
    
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    
    private $adminField;
    
    private $options;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        require_once plugin_dir_path( __FILE__ ) . 'class-consentcookie-admin-field.php';
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->set_options();
        $this->adminField = new ConsentCookie_Admin_Field( $this->plugin_name, $this->version, $this->options );
        
    }
    
    private function isPluginSettingsPage() {
        $screen = get_current_screen();
        return "settings_page_" . $this->plugin_name . '-settings' === $screen->id;
    }
    
    /**
     * Register the stylesheets for the Dashboard.
     *
     * @since 		1.0.0
     */
    public function enqueue_styles( $hook_suffix ) {

        if ( $this->isPluginSettingsPage() ) {
            wp_enqueue_style( $this->plugin_name . "-app", CONSENTCOOKIE_CONFIGURATOR_PATH . 'css/app.css', array(), null, 'all' );
            wp_enqueue_style( $this->plugin_name . "-codemirror", plugins_url( 'codemirror/codemirror.css', __FILE__), array( $this->plugin_name . "-app" ) );
            wp_enqueue_style( $this->plugin_name . "-codemirror_dialog", plugins_url( 'codemirror/addon/dialog/dialog.css', __FILE__), array( $this->plugin_name . "-codemirror" ) );
            wp_enqueue_style( $this->plugin_name . "-codemirror_matchesonscrollbar", plugins_url( 'codemirror/addon/search/matchesonscrollbar.css', __FILE__), array( $this->plugin_name . "-codemirror_dialog" ) );
            wp_enqueue_style( $this->plugin_name . "-admin", plugin_dir_url( __FILE__ ) . 'css/consentcookie-admin.css', array( $this->plugin_name . "-codemirror_matchesonscrollbar" ), $this->version, 'all' );
        }

    } // enqueue_styles()
    
    /**
     * Register the JavaScript for the dashboard.
     *
     * @since 		1.0.0
     */
    public function enqueue_scripts( $hook_suffix ) {
        
        if ( $this->isPluginSettingsPage() ) {
            wp_enqueue_script( $this->plugin_name . "-manifest", CONSENTCOOKIE_CONFIGURATOR_PATH . 'js/manifest.js', array(), null);
            wp_enqueue_script( $this->plugin_name . "-vendor", CONSENTCOOKIE_CONFIGURATOR_PATH . 'js/vendor.js', array( $this->plugin_name . "-manifest" ), null);
            wp_enqueue_script( $this->plugin_name . "-app", CONSENTCOOKIE_CONFIGURATOR_PATH . 'js/app.js', array( $this->plugin_name . "-vendor" ), null );
            
            // CodeMirror
            wp_enqueue_script( $this->plugin_name . "-codemirror", plugins_url( 'codemirror/codemirror.js', __FILE__) );
            wp_enqueue_script( $this->plugin_name . "-codemirror_mode_js", plugins_url( 'codemirror/mode/javascript.js', __FILE__) );
            wp_enqueue_script( $this->plugin_name . "-codemirror_dialog", plugins_url( 'codemirror/addon/dialog/dialog.js', __FILE__) );
            wp_enqueue_script( $this->plugin_name . "-codemirror_matchbrackets", plugins_url( 'codemirror/addon/edit/matchbrackets.js', __FILE__) );
            wp_enqueue_script( $this->plugin_name . "-codemirror_search", plugins_url( 'codemirror/addon/search/search.js', __FILE__) );
            wp_enqueue_script( $this->plugin_name . "-codemirror_searchcursor", plugins_url( 'codemirror/addon/search/searchcursor.js', __FILE__) );
            wp_enqueue_script( $this->plugin_name . "-codemirror_matchhighlighter", plugins_url( 'codemirror/addon/search/match-highlighter.js', __FILE__) );
            wp_enqueue_script( $this->plugin_name . "-codemirror_annotatescrollbar", plugins_url( 'codemirror/addon/scroll/annotatescrollbar.js', __FILE__) );
            wp_enqueue_script( $this->plugin_name . "-codemirror_matchesonscrollbar", plugins_url( 'codemirror/addon/search/matchesonscrollbar.js', __FILE__) );
            
            wp_enqueue_script( $this->plugin_name . "-js", plugins_url( 'js/consentcookie-admin.js', __FILE__) );
            wp_localize_script( $this->plugin_name . "-js", $this->plugin_name . 'ObjectL10n', array( "unsavedWarning" => esc_html__( 'Your modifications will get lost if you leave this page.', 'consentcookie' )) );
        }
        
    } // enqueue_scripts()
    
    
    /**
     * Creates the options page
     *
     * @since 		1.0.0
     * @return 		void
     */
    public function page_settings() {
        include( plugin_dir_path( __FILE__ ) . 'partials/consentcookie-admin-page-settings.php' );
        
    } // page_settings()
    
    
    /**
     * Adds a settings page link to a menu
     *
     * @link 		https://codex.wordpress.org/Administration_Menus
     * @since 		1.0.0
     * @return 		void
     */
    public function add_menu() {
        
        // Top-level page
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        
        // Submenu Page
        // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
        
        add_submenu_page(
            'options-general.php',
            apply_filters( $this->plugin_name . '-settings-page-title', esc_html__( 'ConsentCookie settings', 'consentcookie' ) ),
            apply_filters( $this->plugin_name . '-settings-menu-title', esc_html__( 'ConsentCookie', 'consentcookie' ) ),
            'manage_options',
            $this->plugin_name . '-settings',
            array( $this, 'page_settings' )
            );
        
    } // add_menu()
    
    public function register_settings() {
        // register_setting( $option_group, $option_name, $sanitize_callback );
        
        register_setting(
            $this->plugin_name . '-options',
            $this->plugin_name . '-options',
            array( $this, 'validate_options' )
            );
        
    } // register_settings()
        
    /**
     * Registers settings fields with WordPress
     */
    public function register_fields() {
        
        // add_settings_field( $id, $title, $callback, $menu_slug, $section, $args );
        
        add_settings_field(
            'consentcookie-enabled',
            apply_filters( $this->plugin_name . 'label-enabled', esc_html__( 'Enabled', 'consentcookie' ) ),
            array( $this->adminField, 'field_checkbox' ),
            $this->plugin_name,
            $this->plugin_name . '-general',
            array(
                'id' 			=> 'consentcookie-enabled',
                'value' 		=> 1,
            )
            );
             
        add_settings_field(
            'consentcookie-widget-ccc',
            apply_filters( $this->plugin_name . 'label-widget-ccc', esc_html__( 'Configuration', 'consentcookie' ) ),
            array( $this->adminField, 'field_ccc' ),
            $this->plugin_name,
            $this->plugin_name . '-general',
            array(
                'id' 			=> 'consentcookie-widget-ccc'
            )
            );
        
        add_settings_field(
            'consentcookie-widget-customscript',
            apply_filters( $this->plugin_name . 'label-widget-customscript', esc_html__( 'Script', 'consentcookie' ) ),
            array( $this->adminField, 'field_textarea' ),
            $this->plugin_name,
            $this->plugin_name . '-general',
            array(
                'class'         => "large-text jstextarea",
                'description' 	=> esc_html__ ( 'Custom script' ),
                'id' 			=> 'consentcookie-widget-customscript',
                'help_url'      => 'https://www.consentcookie.nl/documentation/start-direct/wordpress-plugin/'
            )
            );
        
        
    } // register_fields()
    
    /**
     * Creates a settings section
     *
     * @since 		1.0.0
     * @param 		array 		$params 		Array of parameters for the section
     * @return 		mixed 						The settings section
     */
    public function section_general( $params ) {
        
        include( plugin_dir_path( __FILE__ ) . 'partials/consentcookie-admin-section-general.php' );
        
    } // section_general()
    
    /**
     * Registers settings sections with WordPress
     */
    public function register_sections() {
        
        // add_settings_section( $id, $title, $callback, $menu_slug );
        
        add_settings_section(
            $this->plugin_name . '-general',
            apply_filters( $this->plugin_name . 'section-title-general', esc_html__( 'ConsentCookie', 'consentcookie' ) ),
            array( $this, 'section_general' ),
            $this->plugin_name
            );
        
    } // register_sections()
    
    /**
     * Sets the class variable $options
     */
    private function set_options() {
        
        $this->options = get_option( $this->plugin_name . '-options' );
        
    } // set_options()
    
    /**
     * Returns an array of options names, fields types, and default values
     *
     * @return 		array 			An array of options
     */
    public static function get_options_list() {
        
        $options = array();
        
        $options[] = array( 'consentcookie-enabled', 'checkbox', 0);
        $options[] = array( 'consentcookie-widget-ccc', 'textarea', '' );
        $options[] = array( 'consentcookie-widget-customscript', 'textarea', '' );
        
        return $options;
        
    } // get_options_list()
    
    private function sanitizer( $type, $data ) {
        
        if ( empty( $type ) ) { return; }
        if ( empty( $data ) && $type !== "checkbox" ) { return; }
        
        $return 	= '';
        $sanitizer 	= new ConsentCookie_Sanitize();
        
        $sanitizer->set_data( $data );
        $sanitizer->set_type( $type );
        
        $return = $sanitizer->clean();
        
        unset( $sanitizer );
        
        return $return;
        
    } // sanitizer()
    
    /**
     * Validates saved options
     *
     * @since 		1.0.0
     * @param 		array 		$input 			array of submitted plugin options
     * @return 		array 						array of validated plugin options
     */
    public function validate_options( $input ) {
        
        //wp_die( print_r( $input ) );
        
        $valid 		= array();
        $options 	= $this->get_options_list();
        
        foreach ( $options as $option ) {
            
            $name = $option[0];
            $type = $option[1];
            
            $valid[$option[0]] = $this->sanitizer( $type, $input[$name] );
        }
        
        return $valid;
        
    } // validate_options()
    
}
