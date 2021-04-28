<?php
/**
 *
 * @access      public
 * @since       1.1
 * @return      $content
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'WPDEV_FRCF7_VERSION', '1.0' );

define( 'WPDEV_FRCF7_REQUIRED_WP_VERSION', '4.0' );

define( 'WPDEV_FRCF7_PLUGIN', __FILE__ );

define( 'WPDEV_FRCF7_PLUGIN_BASENAME', basename( WPDEV_FRCF7_PLUGIN ) );

define( 'WPDEV_FRCF7_PLUGIN_NAME', trim( dirname( WPDEV_FRCF7_PLUGIN_BASENAME ), '/' ) );

define( 'WPDEV_FRCF7_PLUGIN_DIR', untrailingslashit( dirname( WPDEV_FRCF7_PLUGIN ) ) );

define( 'WPDEV_FRCF7_PLUGIN_CSS_DIR', WPDEV_FRCF7_PLUGIN_DIR . '/css' );

require_once dirname( __FILE__ ) . '/frontend-registration-opt-cf7.php';
require_once dirname( __FILE__ ) . '/contact-form7-password/cf7-add-password-field.php';
function wpdev_cf7fr_editor_panels_reg( $panels ) {
	$new_page = array(
		'Error' => array(
			'title' => __( 'Registration Settings', 'contact-form-7' ),
			'callback' => 'wpdev_cf7fr_admin_reg_additional_settings',
		),
	);

	$panels = array_merge( $panels, $new_page );

	return $panels;
}
add_filter( 'wpcf7_editor_panels', 'wpdev_cf7fr_editor_panels_reg' );

add_filter( 'plugin_row_meta', 'wpdev_my_register_plugins_link', 10, 2 );
function wpdev_my_register_plugins_link( $links, $file ) {
	$base = plugin_basename( __FILE__ );
	if ( $file == $base ) {
		$links[] = '<a href="http://www.wpbuilderweb.com/frontend-registration-contact-form-7/">' . __( 'PRO Version' ) . '</a>';
		$links[] = '<a href="http://www.wpbuilderweb.com/shop">' . __( 'More Plugins by David Pokorny' ) . '</a>';
		// $links[] = '<a href="http://www.wpbuilderweb.com/payment/">' . __('Donate') . '</a>';
	}
	return $links;
}
function wpdev_cf7fr_admin_reg_additional_settings( $cf7 ) {
	$post_id = sanitize_text_field( $_GET['post'] );
	$tags = $cf7->scan_form_tags();
	$cf7frenable = get_post_meta( $post_id, '_cf7fr_enable_registration', true );
	$cf7frlogin = get_post_meta( $post_id, '_cf7fr_enable_login', true );
	$cf7fru = get_post_meta( $post_id, '_cf7fru_', true );
	$cf7fre = get_post_meta( $post_id, '_cf7fre_', true );
	$cf7frr = get_post_meta( $post_id, '_cf7frr_', true );
	$cf7frpass = get_post_meta( $post_id, '_cf7frpass_', true );

	$enablemail = get_post_meta( $post_id, '_cf7fr_enablemail_registration', true );
	$autologinfield = get_post_meta( $post_id, '_cf7fr_autologinfield_reg', true );
	$selectedrole = $cf7frr;
	if ( ! $selectedrole ) {
		$selectedrole = 'subscriber';
	}
	if ( $cf7frenable == '1' ) {
		$cf7frechecked = 'CHECKED';
	} else {
		$cf7frechecked = '';
	}
	if ( $cf7frlogin == '1' ) {
		$cf7freloginchecked = 'CHECKED';
	} else {
		$cf7freloginchecked = '';
	}
	if ( $enablemail == '1' ) {
		$checkedmail = 'CHECKED';
	} else {
		$checkedmail = '';
	}
	if ( $autologinfield == '1' ) {
		$autologinfield = 'CHECKED';
	} else {
		$autologinfield = '';
	}

	$selected = '';
	$admin_cm_output = '';
	if ( $cf7freloginchecked ) {
		$cf7frenable = '';
		$autologinfield = '';
	}
	$admin_cm_output .= "<div id='additional_settings-sortables' class='meta-box'><div id='additionalsettingsdiv'>";
	$admin_cm_output .= "<h2 class='hndle ui-sortable-handle'><span>Frontend Registration Settings:</span></h2>";
	$admin_cm_output .= "<div class='inside'>";
	if ( ! $cf7freloginchecked ) {
		$admin_cm_output .= "<div class='mail-field pretty p-switch p-fill'>";
		$admin_cm_output .= "<input name='cf7frenable' value='1' type='checkbox' $cf7frechecked>";
		$admin_cm_output .= "<div class='state'><label>Enable Registration on this form</label></div>";
		$admin_cm_output .= '</div>';
	}
	$admin_cm_output .= "<div class='mail-field pretty p-switch p-fill'>";
	$admin_cm_output .= "<input name='cf7frlogin' value='1' type='checkbox' $cf7freloginchecked>";
	$admin_cm_output .= "<div class='state'><label>Enable User Login on this form</label></div>";
	$admin_cm_output .= '</div>';

	$admin_cm_output .= "<div class='mail-field pretty p-switch p-fill'>";
	$admin_cm_output .= "<input name='enablemail' value='' type='checkbox' $checkedmail>";
	$admin_cm_output .= "<div class='state'><label>Skip Contact Form 7 Mails ?</label></div>";
	$admin_cm_output .= '</div>';
	if ( ! $cf7freloginchecked ) {
		$admin_cm_output .= "<div class='mail-field pretty p-switch p-fill'>";
		$admin_cm_output .= "<input name='autologinfield' value='' type='checkbox' $autologinfield>";
		$admin_cm_output .= "<div class='state'><label>Enable auto login after registration? </label></div>";
		$admin_cm_output .= '</div>';
	}
	$admin_cm_output .= '<table>';

	$admin_cm_output .= "<div class='handlediv' title='Click to toggle'><br></div><h2 class='hndle ui-sortable-handle'><span>Frontend Fields Settings:</span></h2>";

	$admin_cm_output .= '<tr><td>Selected Field Name For User Name :</td></tr>';
	$admin_cm_output .= "<tr><td><select name='_cf7fru_'>";
	$admin_cm_output .= "<option value=''>Select Field</option>";
	foreach ( $tags as $key => $value ) {
		if ( $cf7fru == $value['name'] ) {
			$selected = 'selected=selected';
		} else {
			$selected = '';
		}
		$admin_cm_output .= '<option ' . $selected . " value='" . $value['name'] . "'>" . $value['name'] . '</option>';
	}
	$admin_cm_output .= '</select>';
	$admin_cm_output .= '</td></tr>';
	if ( ! $cf7freloginchecked ) {
		$admin_cm_output .= '<tr><td>Selected Field Name For Email :</td></tr>';
		$admin_cm_output .= "<tr><td><select name='_cf7fre_'>";
		$admin_cm_output .= "<option value=''>Select Field</option>";
		foreach ( $tags as $key => $value ) {
			if ( $cf7fre == $value['name'] ) {
				$selected = 'selected=selected';
			} else {
				$selected = '';
			}
			$admin_cm_output .= '<option ' . $selected . " value='" . $value['name'] . "'>" . $value['name'] . '</option>';
		}
		$admin_cm_output .= '</select>';
	}
	$admin_cm_output .= '</td></tr><tr><td>';
	$admin_cm_output .= '<tr><td>Selected Field Name For Password :</td></tr>';
	$admin_cm_output .= "<tr><td><select name='_cf7frpass_'>";
	$admin_cm_output .= "<option value=''>Select Field</option>";
	foreach ( $tags as $key => $value ) {
		if ( $cf7frpass == $value['name'] ) {
			$selected = 'selected=selected';
		} else {
			$selected = '';
		}
		$admin_cm_output .= '<option ' . $selected . " value='" . $value['name'] . "'>" . $value['name'] . '</option>';
	}
	$admin_cm_output .= '</select>';
	$admin_cm_output .= '</td></tr><tr><td>';
	$admin_cm_output .= "<input type='hidden' name='email' value='2'>";
	$admin_cm_output .= "<input type='hidden' name='post' value='$post_id'>";
	$admin_cm_output .= '</td></tr>';
	if ( ! $cf7freloginchecked ) {
		$admin_cm_output .= '<tr><td>Selected User Role:</td></tr>';
		$admin_cm_output .= '<tr><td>';
		$admin_cm_output .= "<select name='_cf7frr_'>";
		$editable_roles = get_editable_roles();
		foreach ( $editable_roles as $role => $details ) {
			$name = translate_user_role( $details['name'] );
			if ( $selectedrole == $role ) { // preselect specified role
				$admin_cm_output .= "<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
			} else {
				$admin_cm_output .= "<option value='" . esc_attr( $role ) . "'>$name</option>";
			}
		}
		$admin_cm_output .= '</select>';
		$admin_cm_output .= '</td></tr>';
	}
	$admin_cm_output .= '</table>';
	$admin_cm_output .= '</div>';
	$admin_cm_output .= '</div>';
	$admin_cm_output .= '</div>';

	echo $admin_cm_output;
}
// hook into contact form 7 admin form save
add_action( 'wpcf7_save_contact_form', 'wpdev_cf7_save_reg_contact_form' );

function wpdev_cf7_save_reg_contact_form( $cf7 ) {
	$tags = $cf7->scan_form_tags();

	$post_id = sanitize_text_field( $_POST['post_ID'] );

	if ( ! empty( $_POST['cf7frenable'] ) ) {
		$enable = sanitize_text_field( $_POST['cf7frenable'] );
		update_post_meta( $post_id, '_cf7fr_enable_registration', $enable );
	} else {
		update_post_meta( $post_id, '_cf7fr_enable_registration', 0 );
	}
	if ( ! empty( $_POST['cf7frlogin'] ) ) {
		$enable = sanitize_text_field( $_POST['cf7frlogin'] );
		update_post_meta( $post_id, '_cf7fr_enable_login', $enable );
	} else {
		update_post_meta( $post_id, '_cf7fr_enable_login', 0 );
	}
	if ( isset( $_POST['enablemail'] ) ) {
		update_post_meta( $post_id, '_cf7fr_enablemail_registration', 1 );
	} else {
		update_post_meta( $post_id, '_cf7fr_enablemail_registration', 0 );
	}

	if ( isset( $_POST['autologinfield'] ) ) {
		update_post_meta( $post_id, '_cf7fr_autologinfield_reg', 1 );
	} else {
		update_post_meta( $post_id, '_cf7fr_autologinfield_reg', 0 );
	}

	$key = '_cf7fru_';
	$vals = sanitize_text_field( $_POST[ $key ] );
	update_post_meta( $post_id, $key, $vals );

	$key = '_cf7fre_';
	$vals = sanitize_text_field( $_POST[ $key ] );
	update_post_meta( $post_id, $key, $vals );

	$key = '_cf7frpass_';
	$vals = sanitize_text_field( $_POST[ $key ] );
	update_post_meta( $post_id, $key, $vals );

	$key = '_cf7frr_';
	$vals = sanitize_text_field( $_POST[ $key ] );
	update_post_meta( $post_id, $key, $vals );
}
add_action(
	'wp_footer',
	function () {
		?>
	<script>
		document.addEventListener('wpcf7submit', function(event){
			const aborted = event.detail.status;
			if(aborted == 'aborted'){
				return;
			}
			window.location.reload();
		});
	</script>
		<?php
	}
)
?>
