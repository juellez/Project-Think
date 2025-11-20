<?php
/**
 * Dynamic Block: Project Think Field - Render.php
 * Renders the chosen custom field data for the project custom type
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id = get_the_ID();
$meta_key = isset( $attributes['metaField'] ) ? sanitize_key( $attributes['metaField'] ) : '_project_think_unit_name';
$meta_value = get_post_meta( $post_id, $meta_key, true );
$output = '';

// Define the keys for all link fields
$link_fields = [
    '_project_think_google_doc_link', 
    '_project_think_example_link', 
    '_project_think_unit_outline_link'
];

// Define all labels for consistent display
$labels = [
    '_project_think_unit_name'          => 'Unit Name',
    '_project_think_google_doc_link'    => 'Project Google Doc',
    '_project_think_example_link'       => 'Example Project',
    '_project_think_unit_outline_link'  => 'Unit Outline',
    '_project_think_overview'           => 'Overview',
    '_project_think_culminates'         => 'Culminates In',
];

$label = isset( $labels[ $meta_key ] ) ? $labels[ $meta_key ] : 'Custom Field';

// Only render if we have a value and we are on a 'project' post
if ( $meta_value && get_post_type() === 'post' ) {
    
    // --- Output Logic ---
    
    // 1. Handle URL fields
    if ( in_array( $meta_key, $link_fields ) ) {
        // Use a consistent display link text
        $link_text = 'Click to View ' . esc_html( $label ); 
        
        $output .= '<div class="project-think-meta project-think-link">';
        $output .= '<p><strong>' . esc_html( $label ) . ':</strong> ';
        // Render as an HTML link with target="_blank"
        $output .= '<a href="' . esc_url( $meta_value ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $link_text ) . '</a></p>';
        $output .= '</div>';
        
    // 2. Handle Text/Textarea fields
    } else {
        $output .= '<div class="project-think-meta project-think-' . esc_attr( str_replace('_project_think_', '', $meta_key) ) . '">';
        $output .= '<h3>' . esc_html( $label ) . '</h3>';
        // Use nl2br for multiline text areas (Overview/Culminates)
        $output .= '<p>' . nl2br( esc_html( $meta_value ) ) . '</p>';
        $output .= '</div>';
    }
}

echo $output;