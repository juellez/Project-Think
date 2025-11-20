<?php
/**
 * Plugin Name: Project Think
 * Description: A custom plugin to manage 'Project' post types and their custom fields and administrative display.
 * Version: 1.0.0
 * Author: jewel mlnarik
 * Text Domain: project-think
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define Plugin Constants
 */
define( 'PROJECT_THINK_DIR', plugin_dir_path( __FILE__ ) );
define( 'PROJECT_THINK_URL', plugin_dir_url( __FILE__ ) );

/**
 * Require necessary files
 */
// require_once PROJECT_THINK_DIR . 'inc-post-types.php';
// require_once PROJECT_THINK_DIR . 'inc-custom-fields.php';
// require_once PROJECT_THINK_DIR . 'inc-widgets.php';

/**
 * Rename the default 'Post' ==> 'Project'.
 */
function project_think_rename_default_post_type() {
    // Get the standard post type object
    $get_post_type = get_post_type_object( 'post' );

    // Define the new labels
    $labels = array(
        'name'                  => _x( 'Projects', 'post type general name', 'project-think' ),
        'singular_name'         => _x( 'Project', 'post type singular name', 'project-think' ),
        'menu_name'             => _x( 'Projects', 'admin menu', 'project-think' ),
        'name_admin_bar'        => _x( 'Project', 'add new on admin bar', 'project-think' ),
        'add_new'               => _x( 'Add New Project', 'post', 'project-think' ),
        'add_new_item'          => __( 'Add New Project', 'project-think' ),
        'edit_item'             => __( 'Edit Project', 'project-think' ),
        'new_item'              => __( 'New Project', 'project-think' ),
        'view_item'             => __( 'View Project', 'project-think' ),
        'all_items'             => __( 'All Projects', 'project-think' ),
        'search_items'          => __( 'Search Projects', 'project-think' ),
        'parent_item_colon'     => __( 'Parent Project:', 'project-think' ),
        'not_found'             => __( 'No projects found.', 'project-think' ),
        'not_found_in_trash'    => __( 'No projects found in Trash.', 'project-think' ),
        'featured_image'        => __( 'Project Image', 'project-think' ),
        'set_featured_image'    => __( 'Set project image', 'project-think' ),
        'remove_featured_image' => __( 'Remove project image', 'project-think' ),
        'use_featured_image'    => __( 'Use as project image', 'project-think' ),
        'archives'              => __( 'Project Archives', 'project-think' ),
    );

    // Apply the new labels
    $get_post_type->labels = (object) $labels;
    // Update menu label
    $get_post_type->menu_name = __( 'Projects', 'project-think' );
    // Update the singular label
    $get_post_type->label = __( 'Projects', 'project-think' );
}
add_action( 'init', 'project_think_rename_default_post_type' );


/**
 * Renames the default 'category' taxonomy ==> 'Subject' 
*/
function project_think_rename_category_taxonomy() {
    // Get the current arguments for the built-in 'category' taxonomy
    $taxonomy_object = get_taxonomy( 'category' );
    
    // Check if the taxonomy object exists
    if ( ! $taxonomy_object ) {
        return;
    }

    // $oldLabels = $taxonomy_object->labels;

    // Define the new labels
    $labels = array(
        'name'                       => _x( 'Subjects', 'taxonomy general name', 'project-think' ), 
        'singular_name'              => _x( 'Subject', 'taxonomy singular name', 'project-think' ),
        'search_items'               => __( 'Search Subjects', 'project-think' ),
        'popular_items'              => __( 'Popular Subjects', 'project-think' ),
        'all_items'                  => __( 'All Subjects', 'project-think' ),
        'parent_item'                => __( 'Parent Subject', 'project-think' ),
        'parent_item_colon'          => __( 'Parent Subject:', 'project-think' ),

        // 'name_field_description'	 => $oldLabels->name_field_description,
        // 'slug_field_description'	 => $oldLabels->slug_field_description,
        // 'name_field_description'	 => $oldLabels->name_field_description,
        // 'desc_field_description'	 => $oldLabels->desc_field_description,

        'edit_item'                  => __( 'Edit Subject', 'project-think' ),
        'view_item'                  => __( 'View Subject', 'project-think' ),
        'update_item'                => __( 'Update Subject', 'project-think' ),
        'add_new_item'               => __( 'Add New Subject', 'project-think' ),
        'new_item_name'              => __( 'New Subject Name', 'project-think' ),

        // 'separate_items_with_commas' => $oldLabels->separate_items_with_commas,
        // 'add_or_remove_items'	 	 => $oldLabels->add_or_remove_items,
        // 'choose_from_most_used'	 	 => $oldLabels->choose_from_most_used,	

        'not_found'                  => __( 'No subjects found.', 'project-think' ),
        'no_terms'                   => __( 'No subjects', 'project-think' ),

        'filter_by_item'			 => __( 'Filter by subject', 'project-think' ),
        'items_list_navigation'      => __( 'Subjects list navigation', 'project-think' ),
        'items_list'                 => __( 'Subjects list', 'project-think' ),
        'most_used'                  => __( 'Most Used', 'project-think' ),
        'back_to_items'              => __( '← Back to Subjects', 'project-think' ),

        'item_link'              	 => __( 'Subject Link', 'project-think' ),
        'item_link_description'      => __( 'A link to a subject', 'project-think' ),

        'menu_name'                  => __( 'Subjects', 'project-think' ),
        'name_admin_bar'             => __( 'category', 'project-think' ),
        'template_name'              => __( 'Subject Archives', 'project-think' )
    );

    // Apply the new labels to the taxonomy object
    $taxonomy_object->labels = (object) $labels;
    $taxonomy_object->label = __( 'Subjects', 'project-think' );
    
    // Ensure the menu item is updated
    global $wp_post_types;
    if ( isset( $wp_post_types['post'] ) ) {
        if ( isset( $wp_post_types['post']->labels->item_link ) ) {
            $wp_post_types['post']->labels->item_link = '%s Subject';
        }
        if ( isset( $wp_post_types['post']->labels->item_link_with_parent ) ) {
            $wp_post_types['post']->labels->item_link_with_parent = '%s Parent Subject';
        }
    }
}
// Use a high priority (99) to ensure this runs after WordPress has registered the taxonomy
add_action( 'init', 'project_think_rename_category_taxonomy', 99 );


/*
|--------------------------------------------------------------------------
| Customize Admin Table View
|--------------------------------------------------------------------------
*/

/**
 * Add custom columns and thumbnail to the 'post' (Project) admin table
 */
function project_think_set_custom_columns( $columns ) {
    $date = $columns['date'];
    // Remove default columns we don't need
    unset( $columns['date'], $columns['author'], $columns['comments'] ); 

    // Add the Featured Image column right after the Title
    $new_columns = array(
        'post-thumb' => __( 'Thumbnail', 'project-think' ), // Use 'post-thumb' key
        'overview'   => __( 'Intro/Summary', 'project-think' ),
    );
    
    // Merge the new columns into the list, keeping 'title' first
    $columns = array_slice( $columns, 0, 1, true ) + $new_columns + array_slice( $columns, 1, null, true );
    
    // Re-add 'Date' at the end
    $columns['date'] = $date;

    return $columns;
}
add_filter( 'manage_post_posts_columns', 'project_think_set_custom_columns' );

/**
 * Populate the custom columns with data
 */
function project_think_custom_column_data( $column, $post_id ) {
    switch ( $column ) {

        case 'post-thumb' :
            if ( has_post_thumbnail( $post_id ) ) {
                // Use a small size for the admin view
                echo get_the_post_thumbnail( $post_id, array( 50, 50 ) ); 
                echo '';
            } else {
                echo '—';
            }
            break;

        case 'overview' :
            // Display only a short snippet of the overview
            $overview = get_post_meta( $post_id, 'project_summary', true ); // acf-field_69095b44948fe
            echo esc_html( wp_trim_words( $overview, 10, '...' ) );
            break;
    }
    // WordPress automatically handles the 'post-thumb' column
}
add_action( 'manage_post_posts_custom_column' , 'project_think_custom_column_data', 10, 2 );

/**
 * Make the custom columns sortable
 */
function project_think_sortable_columns( $columns ) {
    // $columns['unit_name'] = 'unit_name';
    // Overview is typically a long text field and not ideal for sorting, so we'll skip it.
    return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'project_think_sortable_columns' );

// Add any activation/deactivation hooks here if needed

/*
|--------------------------------------------------------------------------
| Register Custom Blocks
|--------------------------------------------------------------------------
*/

/**
 * Register block editor scripts
 */
function project_think_register_block_scripts() {
    // Register Category Filter block editor script
    wp_register_script(
        'project-think-category-filter-editor',
        PROJECT_THINK_URL . 'blocks/category-filter/index.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
        '1.0.0',
        false
    );
}
add_action( 'init', 'project_think_register_block_scripts' );

/**
 * Register the Category Filter block
 */
function project_think_register_blocks() {
    // Register Category Filter Block with explicit script handle
    register_block_type( PROJECT_THINK_DIR . 'blocks/category-filter', array(
        'editor_script' => 'project-think-category-filter-editor',
    ) );

    // Register existing Project Field block if not already registered
    if ( file_exists( PROJECT_THINK_DIR . 'blocks/project-field/block.json' ) ) {
        register_block_type( PROJECT_THINK_DIR . 'blocks/project-field' );
    }

    // Register existing Download Button block if not already registered
    if ( file_exists( PROJECT_THINK_DIR . 'blocks/download-button/block.json' ) ) {
        register_block_type( PROJECT_THINK_DIR . 'blocks/download-button' );
    }
}
add_action( 'init', 'project_think_register_blocks' );

/**
 * Enqueue frontend assets for category filter
 */
function project_think_enqueue_frontend_assets() {
    // Only enqueue on pages where the category filter block is present
    if ( has_block( 'project-think/category-filter' ) || is_archive() || is_search() ) {
        // Enqueue CSS
        wp_enqueue_style(
            'project-think-category-filter',
            PROJECT_THINK_URL . 'assets/css/category-filter.css',
            array(),
            '1.0.0'
        );

        // Enqueue JavaScript with dependencies
        wp_enqueue_script(
            'project-think-category-filter',
            PROJECT_THINK_URL . 'assets/js/category-filter.js',
            array(),
            '1.0.0',
            true
        );

        // Pass data to JavaScript
        wp_localize_script(
            'project-think-category-filter',
            'projectThinkFilter',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'restUrl' => rest_url(),
                'nonce'   => wp_create_nonce( 'wp_rest' )
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'project_think_enqueue_frontend_assets' );

/**
 * Enqueue block editor assets
 */
function project_think_enqueue_editor_assets() {
    // Editor styles for category filter block
    wp_enqueue_style(
        'project-think-category-filter-editor',
        PROJECT_THINK_URL . 'assets/css/category-filter.css',
        array(),
        '1.0.0'
    );
}
add_action( 'enqueue_block_editor_assets', 'project_think_enqueue_editor_assets' );