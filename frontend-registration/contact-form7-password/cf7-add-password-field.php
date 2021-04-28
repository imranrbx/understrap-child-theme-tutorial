<?php

/**
 * Add Password Field
 *
 * Add Password field for contact form 7
 *
 * @since [1.0]
 *
 * @package contactform7
 * @subpackage contactform7-password
 */
require_once dirname(__FILE__) . '/modules/password.php';

// Check whether the functions in modules/password.php exists or not.
if (!function_exists('wpdev_wpcf7_add_form_tag_k_password') ||
	!function_exists('wpdev_wpcf7_k_password_validation_filter')) {
	return;
}

// Set a password field (password, password*) to Contact form 7 handler.
add_action('wpcf7_init', 'wpdev_wpcf7_add_form_tag_k_password');

// Validate a password field (required or optional).
add_filter('wpcf7_validate_password', 'wpdev_wpcf7_k_password_validation_filter', 10, 2);
add_filter('wpcf7_validate_password*', 'wpdev_wpcf7_k_password_validation_filter', 10, 2);
