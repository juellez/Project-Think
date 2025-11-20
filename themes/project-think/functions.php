<?php
/**
 * Project Think functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Project-Think
 * @since Project-Think 1.0
 */

// Enqueue parent and child theme styles
function my_child_theme_enqueue_styles() {
    $parent_style = 'twentytwentytwo-style'; // Replace with your parent theme's main style handle
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_child_theme_enqueue_styles' );
