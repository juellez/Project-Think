<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
|--------------------------------------------------------------------------
| 1. Custom Fields (Meta Boxes)
|--------------------------------------------------------------------------
*/

/**
 * Add the meta box container
 */
function project_think_add_meta_box() {
    add_meta_box(
        'project_think_details',
        __( 'Custom Project Details', 'project-think' ),
        'project_think_meta_box_callback',
        'post', 
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'project_think_add_meta_box' );

/**
 * Register custom post meta fields for REST API and Block Editor support.
 */
function project_think_register_meta() {
    $fields_to_register = [
        '_project_think_unit_name'          => 'string',
        '_project_think_google_doc_link'    => 'string', // URLs are usually strings
        '_project_think_example_link'       => 'string',
        '_project_think_unit_outline_link'  => 'string',
        '_project_think_overview'           => 'string',
        '_project_think_culminates'         => 'string',
    ];

    foreach ( $fields_to_register as $meta_key => $type ) {
        register_post_meta( 
            'post', // <-- The post type we are targeting (your 'Project')
            $meta_key, 
            array(
                'show_in_rest' => true,      // Essential for Gutenberg and REST API
                'single'       => true,       // Field stores a single value
                'type'         => $type,      // Define data type
                'sanitize_callback' => 'sanitize_text_field', // Good practice for security
            )
        );
    }
}
add_action( 'init', 'project_think_register_meta' );

/**
 * Create a Block for the custom field(s)
 */
function project_think_register_block_patterns() {
    register_block_pattern(
        'mytheme/custom-meta-pattern', // Unique name (namespace/slug)
        array(
            'title'         => __( 'My Custom Meta Field Pattern', 'textdomain' ),
            'description'   => _x( 'A pattern that displays a custom meta field value.', 'Block pattern description', 'textdomain' ),
            'content'       => '<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"post-meta","key":"my_custom_meta_field"}}}} -->
<p>This is placeholder content. It will be replaced by the meta field value.</p>
<!-- /wp:paragraph -->',
            'categories'    => array( 'text' ), // Add to an existing category
            'keywords'      => array( 'meta', 'custom field' ),
            'viewportWidth' => 800,
        )
    );
}
add_action( 'init', 'project_think_register_block_patterns' );


/**
 * Meta box HTML content (form fields)
 */
function project_think_meta_box_callback( $post ) {
    // Add a nonce field so we can check it later for security
    wp_nonce_field( 'project_think_save_meta', 'project_think_meta_nonce' );

    // Get current values for all 6 fields
    $unit_name           = get_post_meta( $post->ID, '_project_think_unit_name', true );
    $google_doc_link     = get_post_meta( $post->ID, '_project_think_google_doc_link', true );
    $example_link        = get_post_meta( $post->ID, '_project_think_example_link', true );
    $unit_outline_link   = get_post_meta( $post->ID, '_project_think_unit_outline_link', true );
    $overview            = get_post_meta( $post->ID, '_project_think_overview', true );
    $culminates          = get_post_meta( $post->ID, '_project_think_culminates', true );

    // --- Unit Name (short text) ---
    echo '<h3>' . esc_html__( 'Unit Naming', 'project-think' ) . '</h3>';
    echo '<p>';
    echo '<label for="project_think_unit_name">' . esc_html__( 'Unit Name (Short):', 'project-think' ) . '</label>';
    echo '<input type="text" id="project_think_unit_name" name="project_think_unit_name" value="' . esc_attr( $unit_name ) . '" class="large-text" placeholder="e.g., Math and Art Ratios" />';
    echo '</p>';

    // --- Overview (brief text/textarea) ---
    echo '<h3>' . esc_html__( 'Core Concepts', 'project-think' ) . '</h3>';
    echo '<p>';
    echo '<label for="project_think_overview">' . esc_html__( 'Overview (Snapshot of the unit):', 'project-think' ) . '</label>';
    echo '<textarea id="project_think_overview" name="project_think_overview" rows="3" class="large-text" placeholder="A brief summary of what this unit project is about.">' . esc_textarea( $overview ) . '</textarea>';
    echo '</p>';

    // --- Culminates (brief text/textarea) ---
    echo '<p>';
    echo '<label for="project_think_culminates">' . esc_html__( 'Culminates (Aims and Concepts Learned):', 'project-think' ) . '</label>';
    echo '<textarea id="project_think_culminates" name="project_think_culminates" rows="3" class="large-text" placeholder="What the project aims to do and the core concepts students will master.">' . esc_textarea( $culminates ) . '</textarea>';
    echo '</p>';

    // --- Links ---
    echo '<h3>' . esc_html__( 'Reference Links', 'project-think' ) . '</h3>';
    
    // Project Google Doc Link (URL)
    echo '<p>';
    echo '<label for="project_think_google_doc_link">' . esc_html__( 'Project Google Doc Link:', 'project-think' ) . '</label>';
    echo '<input type="url" id="project_think_google_doc_link" name="project_think_google_doc_link" value="' . esc_url( $google_doc_link ) . '" class="large-text" placeholder="https://docs.google.com/..." />';
    echo '</p>';
    
    // Example Link (URL)
    echo '<p>';
    echo '<label for="project_think_example_link">' . esc_html__( 'Example Link (Model Project):', 'project-think' ) . '</label>';
    echo '<input type="url" id="project_think_example_link" name="project_think_example_link" value="' . esc_url( $example_link ) . '" class="large-text" placeholder="https://client-site.com/example-project" />';
    echo '</p>';

    // Unit Outline Link (URL)
    echo '<p>';
    echo '<label for="project_think_unit_outline_link">' . esc_html__( 'Unit Outline Link:', 'project-think' ) . '</label>';
    echo '<input type="url" id="project_think_unit_outline_link" name="project_think_unit_outline_link" value="' . esc_url( $unit_outline_link ) . '" class="large-text" placeholder="https://docs.google.com/..." />';
    echo '</p>';
}

/**
 * Save the custom field data
 */
function project_think_save_meta_box_data( $post_id ) {
    // Check if our nonce is set and valid
    if ( ! isset( $_POST['project_think_meta_nonce'] ) || ! wp_verify_nonce( $_POST['project_think_meta_nonce'], 'project_think_save_meta' ) ) {
        return;
    }

    // Check the user's permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 1. Sanitize and save Unit Name (Text Field)
    if ( isset( $_POST['project_think_unit_name'] ) ) {
        $unit_name = sanitize_text_field( $_POST['project_think_unit_name'] );
        update_post_meta( $post_id, '_project_think_unit_name', $unit_name );
    }

    // 2. Sanitize and save Overview (Textarea)
    if ( isset( $_POST['project_think_overview'] ) ) {
        // Use sanitize_textarea_field for multiline input
        $overview = sanitize_textarea_field( $_POST['project_think_overview'] ); 
        update_post_meta( $post_id, '_project_think_overview', $overview );
    }
    
    // 3. Sanitize and save Culminates (Textarea)
    if ( isset( $_POST['project_think_culminates'] ) ) {
        // Use sanitize_textarea_field for multiline input
        $culminates = sanitize_textarea_field( $_POST['project_think_culminates'] );
        update_post_meta( $post_id, '_project_think_culminates', $culminates );
    }

    // 4. Sanitize and save Google Doc Link (URL Field)
    if ( isset( $_POST['project_think_google_doc_link'] ) ) {
        // Use esc_url_raw() for saving URLs
        $google_doc_link = esc_url_raw( $_POST['project_think_google_doc_link'] ); 
        update_post_meta( $post_id, '_project_think_google_doc_link', $google_doc_link );
    }

    // 5. Sanitize and save Example Link (URL Field)
    if ( isset( $_POST['project_think_example_link'] ) ) {
        $example_link = esc_url_raw( $_POST['project_think_example_link'] );
        update_post_meta( $post_id, '_project_think_example_link', $example_link );
    }
    
    // 6. Sanitize and save Unit Outline Link (URL Field)
    if ( isset( $_POST['project_think_unit_outline_link'] ) ) {
        $unit_outline_link = esc_url_raw( $_POST['project_think_unit_outline_link'] );
        update_post_meta( $post_id, '_project_think_unit_outline_link', $unit_outline_link );
    }
}
add_action( 'save_post', 'project_think_save_meta_box_data' );


/*
|--------------------------------------------------------------------------
| 2. Allow front-end theming and site editor to access custom fields
|--------------------------------------------------------------------------
*/

// see inc-widgets

/*
|--------------------------------------------------------------------------
| 3. Customize Admin Table View
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
            $overview = get_post_meta( $post_id, '_project_think_overview', true );
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

