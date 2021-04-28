<?php
/*
Template Name: Paid memberships pro page
 */
get_header();
echo "<div class='container'><div class='d-flex justify-content-between'>";
$pmpro_level = pmpro_getLevel( $_REQUEST['level'] );
echo '<div>';
require_once WP_PLUGIN_DIR . '/paid-memberships-pro/pages/levels.php';
echo '</div><div>';
require_once WP_PLUGIN_DIR . '/paid-memberships-pro/pages/checkout.php';
require_once WP_PLUGIN_DIR . '/paid-memberships-pro/pages/billing.php';
echo '</div></div>';
get_footer();
