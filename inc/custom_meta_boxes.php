<?php
add_action('add_meta_boxes', 'wpdev_add_keywords_in_head');
function wpdev_add_keywords_in_head() {
	$screens = ['post', 'page', 'projects'];
	foreach ($screens as $screen) {
		add_meta_box('wpdev_meta_box', 'SEO Keywords', 'wpdev_render_seo_block', $screen, 'advanced', 'default', null);
	}
}
function wpdev_render_seo_block($post) {
	echo "<input type='text' name='wpdev_seo_keyword_text' class='form-control' />";
}