<?php get_header();?>
<div class="container">
  <div class="row">
  	<div class="col-md-10 offset-md-1">
  		<h1>Project Type Taxonomy</h1>
  	<?php
 	$slug = get_query_var( 'term' );
	$taxonomy = get_query_var('taxonomy');
    $args = array(
        'post_type' => 'projects',
        'post_status' => 'publish',
        'tax_query' =>array(
            array(
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => $slug
            ),
        ),
    );
   
    $query = new WP_Query($args);
    if ($query->have_posts()):
        while ($query->have_posts()):
	        $query->the_post();
	        the_post_thumbnail('thumbnail');
	        the_title("<h2>", "</h2>");
	        the_excerpt();
    	endwhile;
        wp_reset_query();
    else:
        $data = 'There is No Project Related To '.$slug;
    endif;
        ?>
  	</div>
  </div>
</div>
<?php get_footer();?>