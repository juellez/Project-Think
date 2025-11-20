<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the custom widget class with WordPress
 */
function project_think_register_field_widget() {
    register_widget( 'Project_Think_Field_Widget' );
}
add_action( 'widgets_init', 'project_think_register_field_widget' );


/**
 * Class definition for the Project Think Field Widget
 */
class Project_Think_Field_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'project_think_field_widget', // Base ID
            __( 'Project Field Display (Easy)', 'project-think' ), // Name of the widget for the UI
            array( 'description' => __( 'Displays a single Project meta field (e.g., Overview, Link) for the current post.', 'project-think' ) ) // Args
        );
    }

    // Front-end display of widget (runs the PHP logic)
    public function widget( $args, $instance ) {
        
        // Ensure we are inside a single post context
        if ( ! is_singular('post') ) {
            return;
        }

        $meta_key = ! empty( $instance['meta_key'] ) ? $instance['meta_key'] : '_project_think_unit_name';
        $post_id = get_the_ID();
        $meta_value = get_post_meta( $post_id, $meta_key, true );

        if ( empty( $meta_value ) ) {
            return;
        }
        
        // Define all link fields and labels (using logic from your old render.php)
        $link_fields = [
            '_project_think_google_doc_link', 
            '_project_think_example_link', 
            '_project_think_unit_outline_link'
        ];
        $labels = [
            '_project_think_unit_name'          => 'Unit Name',
            '_project_think_google_doc_link'    => 'Project Google Doc',
            '_project_think_example_link'       => 'Example Project',
            '_project_think_unit_outline_link'  => 'Unit Outline',
            '_project_think_overview'           => 'Overview',
            '_project_think_culminates'         => 'Culminates In',
        ];
        $label = $labels[ $meta_key ] ?? 'Custom Field';

        // Start Widget Output
        echo $args['before_widget'];
        
        // Output Logic
        echo '<div class="project-think-widget project-think-' . esc_attr( str_replace('_project_think_', '', $meta_key) ) . '">';
        
        if ( in_array( $meta_key, $link_fields ) ) {
            // Handle URL fields
            $link_text = 'Click to View ' . esc_html( $label ); 
            echo '<p><strong>' . esc_html( $label ) . ':</strong> ';
            echo '<a href="' . esc_url( $meta_value ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $link_text ) . '</a></p>';
        } else {
            // Handle Text/Textarea fields
            echo '<h3>' . esc_html( $label ) . '</h3>';
            echo '<p>' . nl2br( esc_html( $meta_value ) ) . '</p>';
        }
        
        echo '</div>';
        
        echo $args['after_widget'];
    }

    // Admin form for configuring the widget
    public function form( $instance ) {
        $meta_key = $instance['meta_key'] ?? '_project_think_unit_name';
        
        $options = [
            '_project_think_unit_name'          => 'Unit Name (short text)',
            '_project_think_overview'           => 'Overview (brief text)',
            '_project_think_culminates'         => 'Culminates (brief text)',
            '_project_think_google_doc_link'    => 'Project Google Doc Link',
            '_project_think_example_link'       => 'Example Link',
            '_project_think_unit_outline_link'  => 'Unit Outline Link',
        ];

        echo '<p>';
        echo '<label for="' . esc_attr( $this->get_field_id( 'meta_key' ) ) . '">' . esc_html__( 'Select Project Field:', 'project-think' ) . '</label>';
        echo '<select class="widefat" id="' . esc_attr( $this->get_field_id( 'meta_key' ) ) . '" name="' . esc_attr( $this->get_field_name( 'meta_key' ) ) . '">';
        foreach ( $options as $value => $label ) {
            echo '<option value="' . esc_attr( $value ) . '" ' . selected( $meta_key, $value, false ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '</p>';
    }

    // Sanitize widget form values as they are saved
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['meta_key'] = ( ! empty( $new_instance['meta_key'] ) ) ? sanitize_text_field( $new_instance['meta_key'] ) : '';
        return $instance;
    }
}


/**
 * Ensures the custom widget block is available for the 'post' type.
 * This fixes widget blocks not appearing in the editor for specific post types.
 */
function project_think_enable_widget_block_for_post_type( $allowed_block_types, $editor_context ) {
    
    // Check if we are editing the 'post' type (your 'Project')
    if ( ! empty( $editor_context->post_type ) && $editor_context->post_type === 'post' ) {
        
        // The block name for a widget is always 'core/widget-{widget_base_id}'
        $widget_block_name = 'core/widget-project_think_field_widget';

        // Check if $allowed_block_types is an array (it can be 'true' if all blocks are allowed)
        if ( is_array( $allowed_block_types ) ) {
            // Add our widget block name to the list of allowed blocks
            $allowed_block_types[] = $widget_block_name;
        } 
        // If $allowed_block_types is 'true', no action is needed as all blocks are already allowed.
    }
    
    return $allowed_block_types;
}
// This filter runs when the editor loads
add_filter( 'allowed_block_types_all', 'project_think_enable_widget_block_for_post_type', 10, 2 );

