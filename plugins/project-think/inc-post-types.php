<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the 'project' custom post type
 */
function project_think_register_cpt() {
    $labels = array(
        'name'               => _x( 'Projects', 'Post Type General Name', 'project-think' ),
        'singular_name'      => _x( 'Project', 'Post Type Singular Name', 'project-think' ),
        'menu_name'          => __( 'Projects', 'project-think' ),
        'parent_item_colon'  => __( 'Parent Project:', 'project-think' ),
        'all_items'          => __( 'All Projects', 'project-think' ),
        'view_item'          => __( 'View Project', 'project-think' ),
        'add_new_item'       => __( 'Add New Project', 'project-think' ),
        'add_new'            => __( 'Add New', 'project-think' ),
        'edit_item'          => __( 'Edit Project', 'project-think' ),
        'update_item'        => __( 'Update Project', 'project-think' ),
        'search_items'       => __( 'Search Projects', 'project-think' ),
        'not_found'          => __( 'Not found', 'project-think' ),
        'not_found_in_trash' => __( 'Not found in Trash', 'project-think' ),
    );
    $args = array(
        'label'               => __( 'project', 'project-think' ),
        'description'         => __( 'units and their descriptions', 'project-think' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-portfolio', // Change to a suitable Dashicon
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'rewrite'             => array( 'slug' => 'projects' ), // Custom slug for URLs
        'show_in_rest'        => true, // Enable for Gutenberg/REST API
    );
    register_post_type( 'project', $args );
}
add_action( 'init', 'project_think_register_cpt', 0 );