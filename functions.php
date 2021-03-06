<?php
define('WPDEV_ACF_PATH', get_stylesheet_directory() . '/inc/acf-lite/');
define('WPDEV_ACF_URL', get_stylesheet_directory_uri() . '/inc/acf-lite/');

require_once __DIR__ . '/frontend-registration/frontend-registration-form.php';
require_once __DIR__ . '/inc/custom_post_types.php';
require_once __DIR__ . '/inc/custom_meta_boxes.php';
require_once WPDEV_ACF_PATH . 'acf.php';
require_once __DIR__ . '/inc/required-plugins.php';
add_filter('acf/settings/url', 'wpdev_acf_settings_url');
function wpdev_acf_settings_url($url) {
    return WPDEV_ACF_URL;
}
add_action(
    'init',
    function () {
        if (did_action('elementor/loaded')) {
            require_once __DIR__ . '/inc/elementor_posts_widget.php';
            require_once __DIR__ . '/inc/elementor_wpcf7_widget.php';
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new ElementorPostsWidget());
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new ElementorWPCF7Widget());
        }

    }
);
add_action('wp_enqueue_scripts', 'wpdev_cf7_enqueue_scripts', 11);

function wpdev_cf7_enqueue_scripts() {
    wp_dequeue_style('understrap-styles');
    wp_deregister_style('understrap-styles');
    wp_enqueue_style('understrap-child-styles', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), false, 'all');
    wp_enqueue_style('mdb-bootstrap', get_stylesheet_directory_uri() . '/assets/css/mdb.min.css', array('understrap-child-styles'), false, 'all');
    wp_enqueue_style('understrap-child', get_stylesheet_directory_uri() . '/style.css', array(), false, 'all');
    // wp_enqueue_script('mdb-js', get_stylesheet_directory_uri() . '/assets/js/mdb.min.js', array('jquery'), '4.19.2', true);
    wp_enqueue_script('cf7-main', get_stylesheet_directory_uri() . '/assets/js/main.js', array(), '1.0', true);
    wp_localize_script('cf7-main', 'ajax_obj', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'wpnonce' => wp_create_nonce('aquickborwnfoxjumpsoverthelazydog'),
    ));
}

// add_filter('login_url', 'wpdev_my_login_page', 10, 3);
function wpdev_my_login_page($login_url, $redirect, $force_reauth) {
    $url = wp_parse_url($login_url, -1);
    $login_page = home_url('/login');
    if (isset($url['path']) && $url['path'] == '/wp-login.php' && isset($_GET['action']) && $_GET['action'] == 'login') {
        wp_safe_redirect('/login', 302, 'WordPress');
    }
    if ($url['path'] == '/wp-login.php' && isset($_GET['action']) && $_GET['action'] == 'register') {
        wp_safe_redirect('/register', 302, 'WordPress');
    }
    $login_url = add_query_arg('redirect_to', $redirect, $login_page);
    return $login_url;
}
add_action('wp_head', 'wpdev_redirect_if_logged_in', 1);
function wpdev_redirect_if_logged_in() {
    if ((get_the_ID() === 1813 || get_the_ID() === 1801) && is_user_logged_in()) {
        wp_safe_redirect('/wp-admin', 302, 'WordPress');
    }
}
add_action('wp_ajax_nopriv_get_all_projects', 'wpdev_filter_projects');
add_action('wp_ajax_get_all_projects', 'wpdev_filter_projects');
function wpdev_filter_projects() {
    check_ajax_referer('aquickborwnfoxjumpsoverthelazydog', 'wpnonce');
    $slug = $_POST['value'];
    $data = '';
    $args = array(
        'post_type' => 'projects',
        'post_status' => 'publish',
    );
    if ($slug != 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'project_type',
                'field' => 'slug',
                'terms' => $slug,
            ),
        );
    }
    $query = new WP_Query($args);
    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();
            $data .= "<figure>" . get_the_post_thumbnail(get_the_ID(), 'thumbnail') . "</figure>";
            $data .= "<p>" . get_the_term_list(get_the_ID(), 'project_type') . "</p>";
            $data .= "<h2>" . get_the_title() . "</h2>";
            $data .= "<p>" . get_the_excerpt() . "</p>";
            $data .= "<a href='" . get_the_permalink() . "' class='btn btn-secondary understrap-read-more-link waves-effect waves-light'> Read More</a>";
        endwhile;
        wp_reset_query();else:
        $data = 'There is No Project Related To ' . $slug;
    endif;
    wp_send_json_success($data, 200);
    wp_die();
}

add_filter('manage_projects_posts_columns', 'wpdev_custom_columns_for_projects');
add_action('manage_projects_posts_custom_column', 'wpdev_custom_columns_func_for_projects', 10, 2);

function wpdev_custom_columns_for_projects($columns) {
    $columns = array(
        'cb' => $columns['cb'],
        'title' => __('Title'),
        'shortcode' => __('Shortcode'),
        'taxonomy' => __('Taxonomy'),
        'thumbnail' => __('Thumbnail'),
        'author' => __('Author'),
        'date' => __('Date'),
    );
    return $columns;
}
function wpdev_custom_columns_func_for_projects($column, $post_id) {
    switch ($column) {
    case 'taxonomy':
        echo get_the_term_list($post_id, 'project_type', '', ' - ', '');
        break;
    case 'thumbnail':
        echo get_the_post_thumbnail($post_id, array(32, 32));
        break;
    case 'shortcode':
        echo "[show-project id='{$post_id}' title='" . get_the_title($post_id) . "']";
    }
}
add_action('init', 'shortcode_for_projets');

function shortcode_for_projets() {
    add_shortcode('show-project', function ($atts) {
        $atts = shortcode_atts(array(
            'id' => 1,
            'title' => 'default Title',
        ), $atts, 'show-project');
        $post = get_post($atts['id'], OBJECT, 'raw');
        // return json_encode($post);
        $data = "<figure>" . get_the_post_thumbnail($post->ID, 'thumbnail') . "</figure>";
        $data .= "<p>" . get_the_term_list($atts['id'], 'project_type', '', ' - ', '') . "</p>";
        $data .= "<h2>" . $post->post_title . "</h2>";
        $data .= "<p>" . $post->post_excerpt . "</p>";
        $data .= "<a href='" . get_the_permalink($post->ID) . "' class='btn btn-secondary understrap-read-more-link waves-effect waves-light'> Read More...</a>";
        wp_reset_query();
        return $data;
    });
}
// add_filter('acf/settings/show_admin', 'wpdev_acf_settings_show_admin');
function wpdev_acf_settings_show_admin($show_admin) {
    return false;
}
if (function_exists('acf_add_local_field_group')):

    acf_add_local_field_group(array(
        'key' => 'group_6099f9a198159',
        'title' => 'Project Options',
        'fields' => array(
            array(
                'key' => 'field_6099fa4787a7b',
                'label' => 'Background Color',
                'name' => 'background_color',
                'type' => 'color_picker',
                'instructions' => 'Use the color picker to choose a background for a specific project type.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '100',
                    'class' => 'form-control',
                    'id' => '',
                ),
                'default_value' => '#FFF',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'projects',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

endif;
function wpdev_add_extra_menu_item_fields($item_id) {
    wp_nonce_field(-1, '_wpnonce', true, true);
    $checked = get_post_meta($item_id, "_conditional_menu_$item_id", true) ?? '';
    ?>
    <p class="description description-wide"><label for="conditional-menu-item"><input type="checkbox" name="conditional_menu_<?=$item_id?>" id="conditional-menu-item" <?=$checked?>/>Conditional Menu</label></p>
    <?php
}
function wpdev_update_nav_menu_item($menu_id, $item_id) {
    if (!wp_verify_nonce($_POST['_wpnonce'], -1)) {
        return $menu_id;
    }
    if (isset($_POST["conditional_menu_$item_id"])) {
        update_post_meta($item_id, "_conditional_menu_$item_id", 'checked');
    } else {
        delete_post_meta($item_id, "_conditional_menu_$item_id");
    }
}

add_action('wp_nav_menu_item_custom_fields', 'wpdev_add_extra_menu_item_fields', 10);
add_action('wp_update_nav_menu_item', 'wpdev_update_nav_menu_item', 10, 2);

add_action('after_setup_theme', 'wpdev_register_secondary_menu');
function wpdev_register_secondary_menu() {
    register_nav_menu('secondary', 'Menu for LoggedIn Users');

}
add_filter('wp_nav_menu_args', 'wpdev_create_dynamic_menu');
function wpdev_create_dynamic_menu($location) {
    // require_once get_stylesheet_directory() . '/inc/Understrap_Child_Walker_Nav_Menu.php';
    require_once get_stylesheet_directory() . '/inc/class-wp-megamenu-walkernav.php';
    // $location['theme_location'] = is_user_logged_in() ? 'secondary' : 'primary';
    $location['walker'] = new Wpdev_Megamenu_WalkerNav();

    // $location['menu_class'] = "snip1490";
    return $location;
}
// add_filter('nav_menu_link_attributes', 'wpdev_nav_menu_link_attributes', 10, 2);
// add_filter('nav_menu_css_class', 'wpdev_nav_menu_css_class', 10, 2);
// add_filter('nav_menu_item_args', 'wpdev_nav_menu_item_args', 10, 2);
function wpdev_nav_menu_css_class($classes, $item) {
    $condition = get_post_meta($item->ID, "_conditional_menu_$item->ID", true);
    if ($condition) {
        $classes[] = 'd-none';
    }
    return $classes;
}
function wpdev_nav_menu_item_args($args, $item) {
    $condition = get_post_meta($item->ID, "_conditional_menu_$item->ID", true);
    if ($condition) {
        $item = [];
        $args = [];
    }
    return $args;
}
// 'show_user_profile',
// 'edit_user_profile',
// 'personal_options_update',
// 'edit_user_profile_update',
add_action( 'show_user_profile', 'wpdev_add_extra_user_fields', 10 );
add_action( 'edit_user_profile', 'wpdev_add_extra_user_fields', 10 );
function wpdev_add_extra_user_fields($user){
    ?>
    <h3>User Birthday</h3>
    <table class="form-table">
        <tbody>
            <tr>
                 <th>User Birthday</th>
                 <td><input type="date" name="user_birthday" class="regular-text" pattern="(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])"value="<?= get_user_meta( $user->ID, '_user_birthday', true ); ?>" id=""></td>
            </tr>
           
        </tbody>
    </table>
    <?php
}
add_action( 'personal_options_update', 'wpdev_update_user_profile', 10 );
add_action( 'edit_user_profile_update', 'wpdev_update_user_profile' , 10 );
function wpdev_update_user_profile($user_id){
   update_user_meta( $user_id, '_user_birthday', $_POST['user_birthday'] );
}




