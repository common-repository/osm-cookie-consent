<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

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

foreach ($langs as $key => $lang) {
    delete_option( 'osm_cookie_consent_message_' . $lang );
    delete_option( 'osm_cookie_consent_moreinfotext_' . $lang );
    delete_option( 'osm_cookie_consent_moreinfolink_' . $lang );
    delete_option( 'osm_cookie_consent_acceptbuttontext_' . $lang );
}
delete_option( 'osm_cookie_consent_font_size');
delete_option( 'osm_cookie_consent_wrapper_background_color');
delete_option( 'osm_cookie_consent_text_color');
delete_option( 'osm_cookie_consent_moreinfolink_color');
delete_option( 'osm_cookie_consent_acceptbutton_color');
delete_option( 'osm_cookie_consent_acceptbutton_background_color');
