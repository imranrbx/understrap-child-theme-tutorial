<?php
/*
 @access      public
 * @since       1.1
 * @return      $content
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action( 'admin_enqueue_scripts', 'wpdev_callback_frcf7_setting_up_scripts' );
function wpdev_callback_frcf7_setting_up_scripts() {
	wp_enqueue_style( 'frcf7css', WPDEV_FRCF7_PLUGIN_url( '/frontend-registration/css/style.css' ), array(), WPDEV_FRCF7_VERSION, 'all' );
}
add_filter(
	'wpcf7_skip_mail',
	function ( $skip_mail, $contact_form ) {
		$post_id = sanitize_text_field( $_POST['_wpcf7'] );
		$enablemail = get_post_meta( $post_id, '_cf7fr_enablemail_registration' );
		if ( $enablemail[0] == 1 ) {
			$skip_mail = true;
		}
		return $skip_mail;
	},
	10,
	2
);
function WPDEV_FRCF7_PLUGIN_url( $path = '' ) {
	$url = get_stylesheet_directory_uri() . $path;

	if ( is_ssl()
		and 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}
function wpdev_create_user_from_registration( $cfdata ) {
	// $cmtagobj = new WPCF7_Shortcode( $tag );
	$post_id = sanitize_text_field( $_POST['_wpcf7'] );
	$cf7fru = get_post_meta( $post_id, '_cf7fru_', true );
	$cf7fre = get_post_meta( $post_id, '_cf7fre_', true );
	$cf7frr = get_post_meta( $post_id, '_cf7frr_', true );
	$cf7frpass = get_post_meta( $post_id, '_cf7frpass_', true );
	$autologinfield = get_post_meta( $post_id, '_cf7fr_autologinfield_reg', true );
	$loginfield = get_post_meta( $post_id, '_cf7fr_enable_login', true );
	$enable = get_post_meta( $post_id, '_cf7fr_enable_registration' );

	if ( $enable[0] != 0 ) {
		if ( ! isset( $cfdata->posted_data ) && class_exists( 'WPCF7_Submission' ) ) {
			$submission = WPCF7_Submission::get_instance();
			if ( $submission ) {
				$formdata = $submission->get_posted_data();
			}
		} elseif ( isset( $cfdata->posted_data ) ) {
			$formdata = $cfdata->posted_data;
		}
		// $password = wp_generate_password(12, false);
		$password = $formdata[ '' . $cf7frpass . '' ];
		$email = $formdata[ '' . $cf7fre . '' ];
		$name = $formdata[ '' . $cf7fru . '' ];
		// Construct a username from the user's name
		$username = strtolower( str_replace( ' ', '', $name ) );
		$name_parts = explode( ' ', $name );
		if ( ! email_exists( $email ) ) {
			// Find an unused username
			$username_tocheck = $username;
			$i = 1;
			while ( username_exists( $username_tocheck ) ) {
				$username_tocheck = $username . $i++;
			}
			$username = $username_tocheck;
			// Create the user
			$userdata = array(
				'user_login' => $username,
				'user_pass' => $password,
				'user_email' => $email,
				'nickname' => reset( $name_parts ),
				'display_name' => $name,
				'first_name' => reset( $name_parts ),
				'last_name' => end( $name_parts ),
				'role' => $cf7frr,

			);
			$user_id = wp_insert_user( $userdata );
			if ( ! is_wp_error( $user_id ) ) {
				// Email login details to user
				$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				$message = 'Welcome! Your login details are as follows:' . "\r\n";
				$message .= sprintf( __( 'Username: %s' ), $username ) . "\r\n";
				$message .= sprintf( __( 'Password: %s' ), $password ) . "\r\n";
				$message .= wp_login_url() . "\r\n";
				wp_mail( $email, sprintf( __( '[%s] Your username and password' ), $blogname ), $message );
			}
			if ( $autologinfield == '1' && ! is_wp_error( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );

				if ( $user ) {
					wp_set_current_user( $user_id, $user->user_login );
					wp_set_auth_cookie( $user_id );
					do_action( 'wp_login', $user->user_login );
				}
			}
		}
	}
	if ( $loginfield ) {
		$form_data = WPCF7_Submission::get_instance();
		$data = $form_data->get_posted_data();
		$user = wp_authenticate( $data['your-user'], $data['your-password'] );
		$contact_form = WPCF7_ContactForm::get_current();
		$abort = true;
		if ( $user instanceof WP_Error ) {
			$form_data->set_response(
				$contact_form->filter_message(
					__( 'Error in username or Password.', 'contact-form-7' )
				)
			);
			add_action( 'wpcf7_before_send_mail', 'wpdev_abort_the_form', 10, 3 );
		}
		if ( $user instanceof WP_User ) {
			wp_set_current_user( $user->ID, $user->user_login );
			wp_set_auth_cookie( $user->ID );
			do_action( 'wp_login', $user->user_login );
		}
	}
	return $cfdata;
}
function wpdev_abort_the_form( $contact_form, &$abort, $submission ) {
	$abort = true;
}
add_action( 'wpcf7_before_send_mail', 'wpdev_create_user_from_registration', 1, 2 );
