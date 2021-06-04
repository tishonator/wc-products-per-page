<?php
/*
Plugin Name: Products per Page for WooCommerce
Description: This plugin adds a Customizer section to change number of products displayed in WooCommerce Shop page.
Author: tishonator
Version: 1.0.0
Author URI: http://tishonator.com/
Contributors: tishonator
Text Domain: wc-products-per-page
*/

if ( !class_exists('wc_products_per_page') ) :

    /**
     * Register the plugin.
     *
     */
    class wc_products_per_page {
        
    	/**
    	 * Instance object
    	 *
    	 * @var object
    	 * @see get_instance()
    	 */
    	protected static $instance = NULL;


        /**
         * Constructor
         */
        public function __construct() {}

        /**
         * Setup
         */
        public function setup() {

            add_action('customize_register', array(&$this, 'customize_register') );

            add_filter( 'loop_shop_per_page', array(&$this, 'woocommerce_products_per_page' ), 20 );
        }

        public function customize_register( $wp_customize ) {

            wc_products_per_page::wc_customize_register_woocommerce_settings( $wp_customize );
        }

        public static function customizer_add_section( $wp_customize, $sectionId, $sectionTitle ) {

            $wp_customize->add_section(
                $sectionId,
                array(
                    'title'       => $sectionTitle,
                    'capability'  => 'edit_theme_options',
                )
            );
        }

        private static function customizer_add_customize_control( $wp_customize, $sectionId, $controlId, $controlLabel, $controlDefaultVar, $sanitizeCallback, $type ) {

            if ($controlDefaultVar) {

                $wp_customize->add_setting( $controlId, array(
                                            'sanitize_callback' => $sanitizeCallback,
                                            'default'           => $controlDefaultVar,
                        ) );
            } else {

                $wp_customize->add_setting( $controlId, array(
                                            'sanitize_callback' => $sanitizeCallback,
                        ) );
            }

            $wp_customize->add_control( new WP_Customize_Control( $wp_customize, $controlId,
                array(
                    'label'          => $controlLabel,
                    'section'        => $sectionId,
                    'settings'       => $controlId,
                    'type'           => $type,
                    )
                )
            );
        }

        private static function customizer_add_number_control( $wp_customize, $sectionId, $controlId, $controlLabel, $controlDefaultVar ) {

            wc_products_per_page::customizer_add_customize_control( $wp_customize, $sectionId, $controlId,
                $controlLabel, $controlDefaultVar, 'absint', 'number' );
        }

        public static function wc_customize_register_woocommerce_settings( $wp_customize ) {

            // Add WooCommerce Settings Section
            wc_products_per_page::customizer_add_section( $wp_customize,
                'woocommerce_prodperpage_settings',
                __( 'WooCommerce Products per Page', 'wc-products-per-page' ) );

            // Add Number of Products per Page
            wc_products_per_page::customizer_add_number_control( $wp_customize,
                'woocommerce_prodperpage_settings',
                'woocommerce_productsperpage', __( 'Products per Page', 'wc-products-per-page' ), 10 );
        }

        public function woocommerce_products_per_page() {

            return wc_products_per_page::read_customizer_option('woocommerce_productsperpage', 10);
        }

        public static function read_customizer_option($name, $default) {

            return get_theme_mod($name, $default);
        }

    	/**
    	 * Used to access the instance
         *
         * @return object - class instance
    	 */
    	public static function get_instance() {

    		if ( NULL === self::$instance ) {
                self::$instance = new self();
            }

    		return self::$instance;
    	}
    }

endif; // wc_products_per_page

add_action('plugins_loaded', array( wc_products_per_page::get_instance(), 'setup' ), 10);
