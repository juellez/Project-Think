<?php
// templates/single-project-details.php

// --- Setup ---
$google_doc_link     = get_post_meta( get_the_ID(), '_project_think_google_doc_link', true );
$overview            = get_post_meta( get_the_ID(), '_project_think_overview', true );
$culminates          = get_post_meta( get_the_ID(), '_project_think_culminates', true );

// --- Start HTML and Header ---
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <div class="wp-site-blocks">
        
        <?php block_header_area(); ?> 

        <main id="site-content" class="wp-block-group">
            <div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
            <article <?php post_class(); ?>>

                <?php the_title( '<h1 class="wp-block-post-title">', '</h1>' ); ?>

                <div class="wp-block-group">
                    <p class="has-small-font-size"><?php the_category(', '); ?></p>
                </div>
                <div class="wp-block-columns">
                    
                    <div class="wp-block-column" style="flex-basis:72%">
                        <div class="wp-block-post-content entry-content"><?php the_content(); ?></div>
                        <p class="has-small-font-size"><?php the_tags(); ?></p>
                    </div>
                    <div class="wp-block-column sticky has-tertiary-background-color has-background has-small-font-size" 
                         style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--50);flex-basis:25%">
                        
                        <?php if ( $overview ) : ?>
                            <p><strong><?php esc_html_e('Overview:', 'project-think'); ?></strong> <?php echo nl2br( esc_html( $overview ) ); ?></p>
                        <?php endif; ?>
                        
                        <?php if ( $culminates ) : ?>
                            <p><strong><?php esc_html_e('Culminates:', 'project-think'); ?></strong> <?php echo nl2br( esc_html( $culminates ) ); ?></p>
                        <?php endif; ?>
                        
                        <p><strong>Note: </strong><?php esc_html_e('This is the final project from a unit. If you would like access to the lessons, assessments, and other activities from the unit,', 'project-think'); ?> <a href="#contact"><?php esc_html_e('contact us', 'project-think'); ?></a>.&nbsp;</p>
                        
                        <?php if ( $google_doc_link ) : ?>
                            <div class="wp-block-buttons">
                                <div class="wp-block-button is-style-outline">
                                    <a class="wp-block-button__link has-background-color has-primary-background-color has-text-color has-background has-link-color wp-element-button" 
                                       href="<?php echo esc_url( $google_doc_link ); ?>" 
                                       target="_blank" rel="noopener noreferrer">
                                       <?php esc_html_e('Download Project Guide', 'project-think'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <p><strong><?php esc_html_e('This project is free for public use.', 'project-think'); ?></strong> <?php esc_html_e('We just have one ask:', 'project-think'); ?> Please let us know how you plan to use the material by commenting below or reaching out directly...</p>
                    </div>
                    </div>
                </article>
            </main>
        
        <?php block_footer_area(); ?>

    </div>
    <?php wp_footer(); ?>
</body>
</html>