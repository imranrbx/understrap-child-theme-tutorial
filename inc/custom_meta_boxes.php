<?php
add_action('add_meta_boxes', 'wpdev_add_keywords_in_head');
add_action('save_post', 'wpdev_seo_keywords_postdata');
add_action('wp_head', 'wpdev_insert_meta_keyword_in_head');
function wpdev_add_keywords_in_head() {
    $screens = ['post', 'page', 'projects'];
    foreach ($screens as $screen) {
        add_meta_box('wpdev_meta_box', 'SEO Keywords', 'wpdev_render_seo_block', $screen, 'advanced', 'default', null);
    }
}
function wpdev_render_seo_block($post) {
    $value = get_post_meta($post->ID, '_seo_keyword_text', true);
    echo "<input type='text' name='wpdev_seo_keyword_text' class='form-control' value='{$value}' />";
}
function wpdev_seo_keywords_postdata($post_id) {
    if (array_key_exists('wpdev_seo_keyword_text', $_POST)) {
        update_post_meta($post_id, '_seo_keyword_text', $_POST['wpdev_seo_keyword_text']);
    }
}
function wpdev_insert_meta_keyword_in_head() {
    if (is_single() || is_page()):
        $keywords = get_post_meta(get_the_ID(), '_seo_keyword_text', true);
        echo "<meta title='keywords' description='{$keywords}' />";
    endif;
}
