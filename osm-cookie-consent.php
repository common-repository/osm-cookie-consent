<?php
/*
Plugin Name: OSM Cookie Consent
Plugin URI:  https://www.desarrollowp.com/blog/plugins/osm-cookie-consent/
Description: This plugin allows you to inform your users about the UE Cookies Law. It is a lightweight JavaScript plugin for alerting users about the use of cookies on your website. Fully responsive and 100% customizable. WPML compatible.
Version:     1.3
Author:      Pablo López
Author URI:  https://www.desarrollowp.com/
License:     GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: osm-cookie-consent
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class OSM_Cookie_Consent
{
    public function __construct()
    {
        // Internationalization
        add_action( 'init', array($this, 'osmcc_textdomain') );

        // Add Options to Customizer
        add_action( 'customize_register', array($this, 'osmcc_customize_register') );

        // Settings Link
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'osmcc_settings_link') );

        // Donate Link :)
        add_filter( 'plugin_row_meta', array($this, 'osmcc_donate'), 10, 2 );

        // CSS & JS
        add_action( 'wp_enqueue_scripts', array($this, 'osmcc_cssjs') );

        // OSM Cookie Consent
        add_action( 'wp_head', array($this, 'osm_cookie_consent') );
    }

    // Internationalization
    public function osmcc_textdomain()
    {
        load_plugin_textdomain( 'osm-cookie-consent', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    // Plugin Options
    public function osmcc_customize_register( $wp_customize )
    {
        // If you use WPML
        if (function_exists('icl_get_languages')) {
            $wpml_langs = icl_get_languages('skip_missing=N');
            $langs = array();
            foreach ($wpml_langs as $key => $wpml_lang) {
                $langs[] = $wpml_lang["language_code"];
            }
        }

        // If you use Polylang
        if (function_exists('pll_languages_list')) {
            $polylang_langs = pll_languages_list();
            $langs = array();
            foreach ($polylang_langs as $key => $polylang_lang) {
                $langs[] = $polylang_lang;
            }
        }

        // If no multi-language
        if (empty($langs)) { $langs = array("es");}

        // COOKIE CONSENT
        $wp_customize->add_panel( 'osmcc_panel', array(
            'title' => __( 'Cookie Law Information', 'osm-cookie-consent' ),
            'description' => '',
            'priority' => 200,
        ));

        foreach ($langs as $key => $lang) {
            $wp_customize->add_section( 'osmcc_section_' . $lang , array(
                'title' => __( 'Cookie Notice', 'osm-cookie-consent' ) . " " . strtoupper($lang),
                'description' => '',
                'panel' => 'osmcc_panel',
            ));

            // Message
            $wp_customize->add_setting( 'osm_cookie_consent_message_' . $lang, array(
                'type' => 'option',
                'capability' => 'edit_theme_options',
            ) );
     
            $wp_customize->add_control('osm_cookie_consent_message_' . $lang, array(
                'label' => __( 'Message', 'osm-cookie-consent' ),
                'section' => 'osmcc_section_' . $lang,
                'type' => 'textarea',
            ));

            // More Info Text
            $wp_customize->add_setting( 'osm_cookie_consent_moreinfotext_' . $lang, array(
                'type' => 'option',
                'capability' => 'edit_theme_options',
            ) );
     
            $wp_customize->add_control('osm_cookie_consent_moreinfotext_' . $lang, array(
                'label' => __( 'More Info button text', 'osm-cookie-consent' ),
                'section' => 'osmcc_section_' . $lang,
                'type' => 'text',
            ));

            // More Info Link
            $wp_customize->add_setting( 'osm_cookie_consent_moreinfolink_' . $lang, array(
                'type' => 'option',
                'capability' => 'edit_theme_options',
            ) );
     
            $wp_customize->add_control('osm_cookie_consent_moreinfolink_' . $lang, array(
                'label' => __( 'More Info button link', 'osm-cookie-consent' ),
                'section' => 'osmcc_section_' . $lang,
                'type' => 'text',
            ));

            // More Info Accept Button Text
            $wp_customize->add_setting( 'osm_cookie_consent_acceptbuttontext_' . $lang, array(
                'type' => 'option',
                'capability' => 'edit_theme_options',
            ) );
     
            $wp_customize->add_control('osm_cookie_consent_acceptbuttontext_' . $lang, array(
                'label' => __( 'Accept button text', 'osm-cookie-consent' ),
                'section' => 'osmcc_section_' . $lang,
                'type' => 'text',
            ));
        }

        // Appearance Settings
        $wp_customize->add_section( 'osmcc_appearance', array(
            'title' => __( 'Appearance Settings', 'osm-cookie-consent' ),
            'description' => '',
            'panel' => 'osmcc_panel',
        ));

        // Font Size
        $wp_customize->add_setting( 'osm_cookie_consent_font_size', array(
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'default' => '4'
        ) );
 
        $wp_customize->add_control('osm_cookie_consent_font_size', array(
            'label' => __( 'Font Size', 'osm-cookie-consent' ),
            'section' => 'osmcc_appearance',
            'type' => 'select',
            'choices' => array(
                '10' => '10px',
                '11' => '11px',
                '12' => '12px',
                '13' => '13px',
                '14' => '14px',
                '15' => '15px',
                '16' => '16px'
            )
        ));

        // Wrapper Background Color
        $wp_customize->add_setting( 'osm_cookie_consent_wrapper_background_color', array(
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'default' => '#272727'
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 
            'osm_cookie_consent_wrapper_background_color', 
            array(
                'label' => __( 'Wrapper background color', 'osm-cookie-consent' ),
                'section' => 'osmcc_appearance',
            )
        ));

        // Text Color
        $wp_customize->add_setting( 'osm_cookie_consent_text_color', array(
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'default' => '#ffffff'
        ) );
 
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 
            'osm_cookie_consent_text_color', 
            array(
                'label' => __( 'Text color', 'osm-cookie-consent' ),
                'section' => 'osmcc_appearance',
            )
        ));

        // More Info Link Color
        $wp_customize->add_setting( 'osm_cookie_consent_moreinfolink_color', array(
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'default' => '#31a8f0'
        ) );
 
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 
            'osm_cookie_consent_moreinfolink_color', 
            array(
                'label' => __( 'More info link color', 'osm-cookie-consent' ),
                'section' => 'osmcc_appearance',
            )
        ));

        // Accept Button Color
        $wp_customize->add_setting( 'osm_cookie_consent_acceptbutton_color', array(
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'default' => '#ffffff'
        ) );
 
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 
            'osm_cookie_consent_acceptbutton_color', 
            array(
                'label' => __( 'Accept button text color', 'osm-cookie-consent' ),
                'section' => 'osmcc_appearance',
            )
        ));

        // Accept Button Background Color
        $wp_customize->add_setting( 'osm_cookie_consent_acceptbutton_background_color', array(
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'default' => '#dc0105'
        ) );
 
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 
            'osm_cookie_consent_acceptbutton_background_color', 
            array(
                'label' => __( 'Accept button background color', 'osm-cookie-consent' ),
                'section' => 'osmcc_appearance',
            )
        ));
    }

    // Settings Link
    public function osmcc_settings_link ( $links )
    {
        $settings_link = array(
            '<a href="' . admin_url( 'customize.php?return=%2Fwp-admin%2Fplugins.php' ) . '">' . __('Settings', 'osm-cookie-consent') . '</a>'
        );
        return array_merge( $links, $settings_link );
    }

    // Donate Link
    public function osmcc_donate($links, $file)
    {
        if($file == plugin_basename(__FILE__)) {
            $links[] = '<a href="https://www.paypal.me/PabloLopezMestre" target="_blank">' . __('Donate', 'osm-cookie-consent') . '</a>';
        }
        return $links;
    }

    public function osmcc_cssjs()
    {
        $lang = substr( get_bloginfo("language"), 0, 2);

        if (get_option('osm_cookie_consent_message_'.$lang)) {
            // Enqueue styles for the front end.
            wp_enqueue_style( 'osm-cookie-consent-style', plugin_dir_url(__FILE__) . 'css/osm-cookie-consent.min.css' );

            $osm_cookie_consent_font_size = get_option('osm_cookie_consent_font_size') ? get_option('osm_cookie_consent_font_size') : "16";
            $osm_cookie_consent_wrapper_background_color = get_option('osm_cookie_consent_wrapper_background_color') ? get_option('osm_cookie_consent_wrapper_background_color') : "#272727";
            $osm_cookie_consent_text_color = get_option('osm_cookie_consent_text_color') ? get_option('osm_cookie_consent_text_color') : "#ffffff";
            $osm_cookie_consent_moreinfolink_color = get_option('osm_cookie_consent_moreinfolink_color') ? get_option('osm_cookie_consent_moreinfolink_color') : "#31a8f0";
            $osm_cookie_consent_acceptbutton_color = get_option('osm_cookie_consent_acceptbutton_color') ? get_option('osm_cookie_consent_acceptbutton_color') : "#ffffff";
            $osm_cookie_consent_acceptbutton_background_color = get_option('osm_cookie_consent_acceptbutton_background_color') ? get_option('osm_cookie_consent_acceptbutton_background_color') : "#dc0105";

            $custom_css = ".cc_container {font-size:" . $osm_cookie_consent_font_size . "px !important; color:" . $osm_cookie_consent_text_color . " !important; background-color:" . $osm_cookie_consent_wrapper_background_color . " !important;}\n";
            $custom_css .= ".cc_more_info {color:" . $osm_cookie_consent_moreinfolink_color . " !important;}\n";
            $custom_css .= ".cc_btn_accept_all {color:" . $osm_cookie_consent_acceptbutton_color . " !important; background-color:" . $osm_cookie_consent_acceptbutton_background_color . " !important;}\n";

            wp_add_inline_style( 'osm-cookie-consent-style', $custom_css );

            // Enqueue scripts for the front end.
            wp_enqueue_script( 'osm-cookie-consent-script', plugin_dir_url(__FILE__) . 'js/osm-cookie-consent.min.js', '', '', true );
        }
    }

    // Cookie Consent
    public function osm_cookie_consent()
    {
        $lang = substr( get_bloginfo("language"), 0, 2);

        if (get_option('osm_cookie_consent_message_'.$lang)) {
            $cookiconsent = '<!-- Begin Cookie Consent -->' . "\n";
            $cookiconsent .= '<script type="text/javascript">' . "\n";
            $cookiconsent .= 'window.cookieconsent_options = {' . "\n";
            $cookiconsent .= '"message": "' . get_option('osm_cookie_consent_message_'.$lang) . '",' . "\n";
            $cookiconsent .= '"dismiss": "' . get_option('osm_cookie_consent_acceptbuttontext_'.$lang) . '",' . "\n";
            $cookiconsent .= '"learnMore": "' . get_option('osm_cookie_consent_moreinfotext_'.$lang) . '",' . "\n";
            $cookiconsent .= '"link": "' . get_option('osm_cookie_consent_moreinfolink_'.$lang) . '",' . "\n";
            $cookiconsent .= '"theme": "",' . "\n";
            $cookiconsent .= '};' . "\n";
            $cookiconsent .= '</script>' . "\n";
            $cookiconsent .= '<!-- End Cookie Consent -->' . "\n";

            echo $cookiconsent;
        }
    }
}

new OSM_Cookie_Consent();
