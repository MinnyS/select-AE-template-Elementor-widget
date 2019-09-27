<?php

/*
Plugin Name: MS Elementor Template Selector Widget
Description: Adds Elementor widget for selecting and displayig Anywhere Elementor templates
Version: 0.1.0
Author: MinnyS
Author URI: minnystuff.com
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*code based upon examples on developers.elementor.com
this page: https://developers.elementor.com/creating-an-extension-for-elementor/
*/

class MS_Select_Template_Widget_Init {

	const VERSION = '0.0.1'; //plugin version
	
	//minimum versions required
	
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0'; //minimum Elementor version
	
	//minimum PHP version; must be >= to Elementor PHP min. requirement
	const MINIMUM_PHP_VERSION = '7.0';
	
	//so only one instance of the class will be loaded
	
	private static $_instance = null;
	
	public static function instance() {
		
		if ( is_null( self::$_instance) ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
		
	}
	
	//add plugin files
	
	//add functions
	public function __construct() {
		add_action( 'init', [ $this, 'MS_text_domain' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}
	
	public function MS_text_domain() {
		load_plugin_textdomain( 'wts_ae' ); //use Anywhere Elementor plugin's text domain
	}
	
	public function init() {
	
		//check if Elementor plugin is installed and active on site
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'MS_admin_notice_missing_elementor_plugin'] );
			return;
		}
		
		//check if Elementor plugin version is adequate
		if ( ! version_compare ( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=') ) {
			add_action( 'admin_notices', [ $this, 'MS_admin_notice_minimum_elementor_version'] );
			return;
		}
		
		//check if site running on adequate PHP version
		
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'MS_admin_notice_minimum_php_version' ] );
			return;
		}
		
		//check to make sure Elementor class Widget_Base exists
		if ( class_exists('Elementor\Widget_Base') ) {
			add_action( 'admin_notices', [$this, 'MS_admin_notice_missing_Elementor_class'] );
			return;
		}
		
		//If passed all conditions, add plugin-registering actions
		add_action( 'elementor/widgets/widgets_registered', [$this, 'MS_init_widgets'] );
		
	}
	
	//notifies admin of missing required plugin
	public function MS_admin_notice_missing_elementor_plugin() {
		
		if ( isset ( $_GET['activate'] ) ) {
			unset( $_GET['activate'] ); //unset $_GET['activate']
		}
		
		$message = sprintf (
			esc_html__( '"%MS Select Template Widget$s" requires "%Elementor$s" to be installed and activated!', 'wts_ae' ),
			'<strong>' . esc_html__( 'MS Select Template Widget', 'wts_ae' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'wts_ae' ) . '</strong>'
		);
		
		printf( '<div class="notice notice-warning is-dismissable"><p>%MS Select Template Widget$s</p></div>', $message );
		
	}
	
	//notifies admin of not adequately high Elementor plugin version
	public function MS_admin_notice_minimum_elementor_version() {
	
		if ( isset ( $_GET['activate'] ) ) {
			unset( $_GET['activate'] ); //unset $_GET['activate']
		}
		
		$message = sprintf (
			esc_html__( '"%MS Select Template Widget$s" requires "%Elementor$s" plugin version "%2.0.0$s" or greater.', 'wts_ae' ),
			'<strong>' . esc_html__( 'MS Select Template Widget', 'wts_ae' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'wts_ae' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		
		printf( '<div class="notice notice-warning is-dismissable"><p>%MS Select Template Widget$s</p></div>', $message );
		
	}
	
	//notifies admin of not adequately high version of PDP
	public function MS_admin_notice_minimum_php_version() {
	
		if ( isset ( $_GET['activate'] ) ) {
			unset( $_GET['activate'] ); //unset $_GET['activate']
		}
		
		$message = sprintf (
			esc_html__( '"%MS Select Template Widget$s" requires PHP version "%2.0.0$s" or greater.', 'wts_ae' ),
			'<strong>' . esc_html__( 'MS Select Template Widget', 'wts_ae' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'wts_ae' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		
		printf( '<div class="notice notice-warning is-dismissable"><p>%MS Select Template Widget$s</p></div>', $message );
		
	}
	
	public function MS_admin_notice_missing_Elementor_class() {
		
		if ( isset ( $_GET['activate'] ) ) {
			unset( $_GET['activate'] ); //unset $_GET['activate']
		}
		
		$message = sprintf (
			esc_html__( '"%MS Select Template Widget$s" requires the availability of Elementor class "%Widget_Base$s"', 'wts_ae' ),
			'<strong>' . esc_html__( 'MS Select Template Widget', 'wts_ae' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'wts_ae' ) . '</strong>',
		);
		
		printf( '<div class="notice notice-warning is-dismissable"><p>%MS Select Template Widget$s</p></div>', $message );
		
	}
	
	//register plugin widgets
	public function MS_init_widgets() {
		
		//include select template widget file
		require_once( plugin_dir_path(__FILE__) . 'ms-select-template-widget.php' );
		
		//register select template widget file
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \MS_Select_Template_Widget() );
	}
	
}

MS_Select_Template_Widget_Init::instance();
