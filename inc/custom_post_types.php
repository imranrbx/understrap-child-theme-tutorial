<?php
function cptui_register_my_cpts_projects() {

	/**
	 * Post Type: Projects.
	 */

	$labels = array(
		'name' => __('Projects', 'custom-post-type-ui'),
		'singular_name' => __('Project', 'custom-post-type-ui'),
		'menu_name' => __('Projects', 'custom-post-type-ui'),
		'all_items' => __('All Projects', 'custom-post-type-ui'),
		'add_new' => __('Add New', 'custom-post-type-ui'),
		'add_new_item' => __('Add New Project', 'custom-post-type-ui'),
		'edit_item' => __('Edit Project', 'custom-post-type-ui'),
		'new_item' => __('New Project', 'custom-post-type-ui'),
		'view_item' => __('View Project', 'custom-post-type-ui'),
		'view_items' => __('View Projects', 'custom-post-type-ui'),
		'search_items' => __('Search Projects', 'custom-post-type-ui'),
		'not_found' => __('No Projects Found', 'custom-post-type-ui'),
		'not_found_in_trash' => __('No Projects Found in Trash', 'custom-post-type-ui'),
		'parent' => __('Parent Project', 'custom-post-type-ui'),
		'featured_image' => __('Featured Image for This Project', 'custom-post-type-ui'),
		'set_featured_image' => __('Set Featured Image for Project', 'custom-post-type-ui'),
		'remove_featured_image' => __('Remove Featured Image for Project', 'custom-post-type-ui'),
		'use_featured_image' => __('Use as Featured Image for Project', 'custom-post-type-ui'),
		'archives' => __('Project Archives', 'custom-post-type-ui'),
		'insert_into_item' => __('Insert Into Project', 'custom-post-type-ui'),
		'uploaded_to_this_item' => __('Uploaded to this Project', 'custom-post-type-ui'),
		'parent_item_colon' => __('Parent Project', 'custom-post-type-ui'),
	);

	$args = array(
		'label' => __('Projects', 'custom-post-type-ui'),
		'labels' => $labels,
		'description' => '',
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_rest' => false,
		'rest_base' => '',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'has_archive' => false,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'delete_with_user' => false,
		'exclude_from_search' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array(
			'slug' => 'projects',
			'with_front' => true,
		),
		'query_var' => true,
		'menu_icon' => 'dashicons-format-aside',
		'supports' => array('title', 'thumbnail', 'excerpt', 'author', 'custom-fields'),
		'show_in_graphql' => false,
	);

	register_post_type('projects', $args);
}

add_action('init', 'cptui_register_my_cpts_projects');

function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Project Types.
	 */

	$labels = array(
		'name' => __('Project Types', 'custom-post-type-ui'),
		'singular_name' => __('Project Type', 'custom-post-type-ui'),
	);

	$args = array(
		'label' => __('Project Types', 'custom-post-type-ui'),
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'query_var' => true,
		'rewrite' => array(
			'slug' => 'project_type',
			'with_front' => true,
		),
		'show_admin_column' => false,
		'show_in_rest' => true,
		'rest_base' => 'project_type',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_quick_edit' => false,
		'show_in_graphql' => false,

	);

	register_taxonomy('project_type', array('projects'), $args);
}
add_action('init', 'cptui_register_my_taxes');
