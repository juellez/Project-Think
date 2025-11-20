<?php
$post_id = get_the_ID();
$custom_field_value = get_post_meta( $post_id, '_project_think_google_doc_link', true );

if ( ! empty( $custom_field_value ) ) {
    echo '<div class="wp-block-my-custom-block-meta-display">';
    echo esc_html( $custom_field_value ); // Sanitize output
    echo '</div>';
}
?>