<?php
/**
 * template name: Template for Projects
 */
get_header();?>
<div class="container">
	<div class="row">
		<div class="col-md-10 offset-md-1">
		<h1>Our Projects</h1>
		<?php
$args = array(
	'hide_empty' => false,
	'taxonomy' => 'project_type',
);
$taxonomies = get_terms($args);
if (!empty($taxonomies)):
	echo "<a href='javascript:;' data-slug='all' class='btn btn-success btn-sm filter'>All Project</a>";
	foreach ($taxonomies as $tax) {
		echo "<a href='javascript:;' data-slug='{$tax->slug}' class='btn btn-primary btn-sm filter'>{$tax->name}</a>";
	}
endif;

?>
<!--
    1--- the_meta()
    2--- get_post_custom()
    3--- get_post_meta()
    4--- get_post_custom_values()
    5--- get_post_custom_keys()
 -->
<div id="result" class="col-md-10 offset-md-1">
	<?php

$args = array(
	'post_type' => 'projects',
	'posts_per_page' => -1,
	'post_status' => 'publish',
);
$query = new WP_Query($args);
if ($query->have_posts()): ?>
<?php
while ($query->have_posts()):
	$query->the_post();
	$bgColor = !empty(get_post_meta(get_the_ID(), 'bg_color', true)) ? get_post_meta(get_the_ID(), 'bg_color', true) : 'inherit';
	echo "<div style='background-color: {$bgColor}'>";
	the_post_thumbnail('thumbnail');
	echo "<p>";
	the_terms(get_the_ID(), 'project_type');
	echo "</p>";
	the_title("<h2>", "</h2>");
	the_excerpt();
	echo "</div>";
endwhile;
wp_reset_query();
endif;
?>

</div>

		</div>
	</div>
</div>
<?php get_footer();?>