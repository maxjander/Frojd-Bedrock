<?php

namespace App\YoastSEO;

// Move yoast seo meta box to bottom of edit post page
add_filter('wpseo_metabox_prio', function() {
    return 'low';
});

/**
 * Add custom variable to be able to show hero text as fallback to content
 */
add_action('wpseo_register_extra_replacements', function() {
    wpseo_register_var_replacement(
        '%%custom_preamble%%',
        __NAMESPACE__ . '\\_wpseoRegisterCustomPreamble',
        'advanced',
        'Shows hero text if it exists, and otherwise content'
    );
});

function _wpseoRegisterCustomPreamble() {
    global $post;

    $more = apply_filters('excerpt_more', '...');
    $default = wp_trim_words($post->post_content, 20, $more);

    $heroText = get_field('hero_text', $post->ID);
    if(empty($heroText)) {
        return $default;
    }

    return wp_trim_words($heroText, 20, $more);
}

/**
 * Exclude CTPs from yoast SEO sitemap
 */
add_filter('wpseo_sitemap_exclude_post_type', function ($value, $post_type) {
    $post_type_to_exclude = ['post'];
    if(in_array($post_type, $post_type_to_exclude)) return true;
}, 10, 2 );
