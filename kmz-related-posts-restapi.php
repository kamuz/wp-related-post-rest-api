<?php
/*
Plugin Name: KMZ Related Posts REST
Description: Display links to related posts through the WP REST API
Version: 0.1
Author: Vladimir Kamuz
Author URI: https://wpdev.pp.ua
Plugin URI: https://wpdev.pp.ua/kmzrelrest
Licence: GPL2
Text Domain: kmzrelrest
*/

/**
 * Load CSS and JavaScript files
 */
function kmzrelrest_css_js() {
    if( is_single() && is_main_query() ) {
        // Get plugin styles
        wp_enqueue_style( 'kmzrelres_main_css', plugin_dir_url(__FILE__) . 'css/style.css', '0.1', 'all' );
    }
}
add_action( 'wp_enqueue_scripts', 'kmzrelrest_css_js' );

/**
 * Output HTML onto bottom of sinle post
 */
function kmzrelrest_display($content){
    if( is_single() && is_main_query() ) {
        $content  = '<a href="' . kmzrelrest_get_json_query() . '">' . kmzrelrest_get_json_query() . '</a>';
        $content .= '<section id="related-posts" class="related-posts">';
        $content .= '<a href="#" class="get-related-posts">Get related posts</a>';
        $content .= '<div class="ajax-loader"><img src="' . plugin_dir_url( __FILE__ ) . 'css/spinner.svg" width="32" height="32" /></div>';
        $content .= '</section><!-- .related-posts -->';
    }
    return $content;
}
add_filter( 'the_content', 'kmzrelrest_display' );

/**
 * Create REST API URL
 * - Get the current categories
 * - Get the category IDs
 * - Create the arguments for categories and pagination
 * - Create URL (example - /wp-json/wp/v2/posts?categories=198,4&per_page=5)
 */
function kmzrelrest_get_json_query(){
    $cats = get_the_category();
    $cat_ids = array();
    foreach( $cats as $cat ) {
        $cat_ids[] = $cat->term_id;
    }

    $args = array(
        'categories' => implode(",", $cat_ids),
        'per_page' => 5
    );

    $url = add_query_arg( $args, rest_url('wp/v2/posts') );

    return $url;
}