<?php
/**
 * Render callback for the Category Filter block
 *
 * @param array    $attributes Block attributes
 * @param string   $content    Block content
 * @param WP_Block $block      Block instance
 */

// Get attributes with defaults
$show_all = isset($attributes['showAllOption']) ? $attributes['showAllOption'] : true;
$display_style = isset($attributes['displayStyle']) ? $attributes['displayStyle'] : 'buttons';
$taxonomy = isset($attributes['taxonomy']) ? $attributes['taxonomy'] : 'category';

// Get ALL categories/subjects - show all regardless of post count or current query
$terms = get_terms(array(
    'taxonomy' => $taxonomy,
    'hide_empty' => false, // Show all categories even if they have no posts
    'orderby' => 'name',
    'order' => 'ASC'
));

// If no terms exist at all, show a helpful message
if (empty($terms) || is_wp_error($terms)) {
    // In editor, show helpful message
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return '<div class="project-category-filter-notice" style="padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;">
            <strong>No categories/subjects found.</strong><br>
            Please create at least one to use this filter.
        </div>';
    }
    // On frontend, show a message too (helpful for debugging)
    return '<div class="project-category-filter-notice" style="padding: 15px; background: #f0f0f0; border-radius: 4px;">
    </div>';
}

// Get current term if on taxonomy archive
$current_term_id = 0;
if (is_tax($taxonomy) || is_category()) {
    $current_term = get_queried_object();
    $current_term_id = $current_term ? $current_term->term_id : 0;
}

// Check if we're filtering via URL parameter
$url_term_id = isset($_GET['filter_category']) ? intval($_GET['filter_category']) : 0;
if ($url_term_id) {
    $current_term_id = $url_term_id;
}

// Get wrapper classes
$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'project-category-filter center wp-block-group alignfull project-category-filter--' . esc_attr($display_style),
    'data-taxonomy' => esc_attr($taxonomy),
    'data-display-style' => esc_attr($display_style)
));

// Build the output HTML
ob_start();
?>

<div <?php echo $wrapper_attributes; ?>>
    <!-- <div class="project-category-filter__label">
        <span><?php echo esc_html__('Filter by Subject:', 'project-think'); ?></span>
    </div> -->

    <?php if ($display_style === 'dropdown'): ?>
        <select class="project-category-filter__select" data-filter-control>
            <?php if ($show_all): ?>
                <option value="" <?php selected($current_term_id, 0); ?>>
                    <?php echo esc_html__('All Projects', 'project-think'); ?>
                </option>
            <?php endif; ?>

            <?php foreach ($terms as $term): ?>
                <option
                    value="<?php echo esc_attr($term->term_id); ?>"
                    data-url="<?php echo esc_url(get_term_link($term)); ?>"
                    <?php selected($current_term_id, $term->term_id); ?>
                >
                    <?php echo esc_html($term->name); ?> 
                    <!-- (<?php echo $term->count; ?>) -->
                </option>
            <?php endforeach; ?>
        </select>

    <?php else: ?>
        <ul class="project-category-filter__list">
            <?php if ($show_all): ?>
                <li class="project-category-filter__item">
                    <a
                        href="<?php echo esc_url(get_post_type_archive_link('post')); ?>"
                        class="project-category-filter__link <?php echo $current_term_id === 0 ? 'is-active' : ''; ?>"
                        data-filter-link
                        data-term-id="0"
                    >
                        <?php echo esc_html__('All Projects', 'project-think'); ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php foreach ($terms as $term): ?>
                <li class="project-category-filter__item">
                    <a
                        href="<?php echo esc_url(get_term_link($term)); ?>"
                        class="project-category-filter__link <?php echo $current_term_id === $term->term_id ? 'is-active' : ''; ?>"
                        data-filter-link
                        data-term-id="<?php echo esc_attr($term->term_id); ?>"
                        data-term-slug="<?php echo esc_attr($term->slug); ?>"
                    >
                        <?php echo esc_html($term->name); ?>
                        <!-- <span class="project-category-filter__count">
                        (<?php echo $term->count; ?>)</span> -->
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="project-category-filter__loading" style="display: none;">
        <span class="project-category-filter__spinner"></span>
        <span><?php echo esc_html__('Filtering...', 'project-think'); ?></span>
    </div>
</div>

<?php
echo ob_get_clean();
