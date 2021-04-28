<?php
/**
 * * A base module for the following types of tags:
 * *      [password] and [password*]              # Single-line password
 **/

// Activate Language Files for WordPress 3.7 or lator
load_plugin_textdomain('cf7-add-password-field');

function wpdev_wpcf7_add_form_tag_k_password() {
	$features = array('name-attr' => true);
	$features = apply_filters('cf7-add-password-field-features', $features);
	wpcf7_add_form_tag(
		array('password', 'password*'),
		'wpdev_wpcf7_k_password_form_tag_handler',
		$features
	);
}

function wpdev_wpcf7_k_password_form_tag_handler($tag) {
	if (empty($tag->name)) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error($tag->name);

	$class = wpcf7_form_controls_class($tag->type, 'wpcf7-text');

	$class .= ' wpcf7-validates-as-password';

	if ($validation_error) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['size'] = $tag->get_size_option('40');
	$atts['maxlength'] = $tag->get_maxlength_option();
	$atts['minlength'] = $tag->get_minlength_option();

	if ($atts['maxlength'] && $atts['minlength']
		&& $atts['maxlength'] < $atts['minlength']) {
		unset($atts['maxlength'], $atts['minlength']);
	}

	$atts['class'] = $tag->get_class_option($class);
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);

	$atts['autocomplete'] = $tag->get_option(
		'autocomplete',
		'[-0-9a-zA-Z]+',
		true
	);

	$atts['password_strength'] = (int) $tag->get_option('password_strength', 'signed_int', true);
	$atts['password_min'] = (int) $tag->get_option('password_min', 'signed_int', true);

	if ($tag->is_required()) {
		$atts['aria-required'] = 'true';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$value = (string) reset($tag->values);

	// Support placeholder. Reference: modules/date.php in the contact form 7 plugin.
	if ($tag->has_option('placeholder')
		or $tag->has_option('watermark')) {
		$atts['placeholder'] = $value;
		$value = '';
	}

	$value = $tag->get_default_option($value);

	$value = wpcf7_get_hangover($tag->name, $value);

	$atts['value'] = $value;

	if (wpcf7_support_html5()) {
		$atts['type'] = $tag->basetype;
	} else {
		$atts['type'] = 'password';
	}

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts($atts);

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
		sanitize_html_class($tag->name),
		$atts,
		$validation_error
	);

	return $html;
}
add_filter('wpcf7_validate_password', 'wpdev_wpcf7_k_password_validation_filter', 10, 2);
add_filter('wpcf7_validate_password*', 'wpdev_wpcf7_k_password_validation_filter', 10, 2);

function wpdev_wpcf7_k_password_validation_filter($result, $tag) {
	$name = $tag->name;

	$value = isset($_POST[$name])
	? trim(wp_unslash(strtr((string) $_POST[$name], "\n", ' ')))
	: '';

	$password_strength = (int) $tag->get_option('password_strength', 'signed_int', true);
	$password_min = (int) $tag->get_option('password_min', 'signed_int', true);
	if ($password_strength < 0) {
		$password_strength = 0;
	}
	if ($password_min < 1) {
		$password_min = 1;
	}
	$pattern = preg_quote($tag->get_option('pattern'));

	if ($tag->is_required() and '' === $value) {
		$result->invalidate($tag, wpcf7_get_message('invalid_required'));
	} elseif ('' !== $value) {
		if (strlen($value) < $password_min) {
			$result->invalidate($tag, __('Please limit the number of characters to at least ' . $password_min . '.', 'cf7-add-password-field'));
		} elseif ($password_strength > 0) {
			if ($password_strength === 1) {
				if (!preg_match('/^[0-9]+$/', $value)) {
					$result->invalidate($tag, __('Please use the numbers only', 'cf7-add-password-field'));
				}
			} elseif ($password_strength === 2) {
				if (!preg_match('/([0-9].*[a-z,A-Z])|([a-z,A-Z].*[0-9])/', $value)) {
					$result->invalidate($tag, __('Please include one or more letters and numbers.', 'cf7-add-password-field'));
				}
			} elseif ($password_strength === 3) {
				if (!preg_match('/[0-9]/', $value) or
					!preg_match('/([a-z].*[A-Z])|([A-Z].*[a-z])/', $value)) {
					$result->invalidate($tag, __('Please include one or more upper and lower case letters and numbers.', 'cf7-add-password-field'));
				}
			} elseif ($password_strength === 4) {
				if (!preg_match('/[0-9]/', $value) or
					!preg_match('/([a-z].*[A-Z])|([A-Z].*[a-z])/', $value) or
					!preg_match('/([!,%,&,@,#,$,^,*,?,_,~])/', $value)) {
					$result->invalidate($tag, __('Please include one or more upper and lower case letters, numbers, and marks.', 'cf7-add-password-field'));
				}
			}
		}
	}

	return $result;
}

// Add Tag.
if (is_admin()) {
	add_action('wpcf7_admin_init', 'wpdev_wpcf7_k_password_add_tag_generator', 55);
}

function wpdev_wpcf7_k_password_add_tag_generator($contact_form, $args = '') {
	if (!class_exists('WPCF7_TagGenerator')) {
		return false;
	}
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add(
		'password',
		__('Password', 'cf7-add-password-field'),
		'wpdev_wpcf7_k_password_pane_confirm',
		array('nameless' => 1)
	);
}

function wpdev_wpcf7_k_password_pane_confirm($contact_form, $args = '') {
	$args = wp_parse_args($args, array());
	$description = __('Generate a form-tag for a password button.', 'cf7-add-password-field');?>
<div class="control-box">
	<fieldset>
		<legend><?php echo sprintf(esc_html($description), $desc_link); ?></legend>

		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'contact-form-7')); ?></legend>
						<label><input type="checkbox" name="required" /> <?php echo esc_html(__('Required field', 'contact-form-7')); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label
					for="<?php echo esc_attr($args['content'] . '-values'); ?>"><?php echo esc_html(__('Name', 'contact-form-7')); ?></label>
				</th>
				<td><input type="text" name="name" class="oneline"
						   id="<?php echo esc_attr($args['content'] . '-values'); ?>"/></td>
			</tr>

			<tr>
				<th scope="row"><label
					for="<?php echo esc_attr($args['content'] . '-id'); ?>"><?php echo esc_html(__('Id attribute', 'contact-form-7')); ?></label>
				</th>
				<td><input type="text" name="id" class="idvalue oneline option"
						   id="<?php echo esc_attr($args['content'] . '-id'); ?>"/></td>
			</tr>

			<tr>
				<th scope="row"><label
					for="<?php echo esc_attr($args['content'] . '-class'); ?>"><?php echo esc_html(__('Class attribute', 'contact-form-7')); ?></label>
				</th>
				<td><input type="text" name="class" class="classvalue oneline option"
						   id="<?php echo esc_attr($args['content'] . '-class'); ?>"/></td>
			</tr>

			<tr>
				<th scope="row"><label for="<?php echo esc_attr($args['content'] . '-values'); ?>"><?php echo esc_html(__('Default value', 'contact-form-7')); ?></label></th>
				<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr($args['content'] . '-values'); ?>" /><br />
	<label><input type="checkbox" name="placeholder" class="option" /> <?php echo esc_html(__('Use this text as the placeholder of the field', 'contact-form-7')); ?></label></td>
			</tr>
			<tr>
				<th scope="row"><label
					for="<?php echo esc_attr($args['content'] . '-password_min'); ?>"><?php echo esc_html(__('Password Length', 'contact-form-7')); ?></label>
				</th>
				<td><input type="text" name="password_min" class="classvalue oneline option"
						   id="<?php echo esc_attr($args['content'] . '-password_min'); ?>"/><br/>
						   <?php echo esc_html(__('Required more than the specified number of characters the input.', 'cf7-add-password-field')); ?></td>
			</tr>
			<tr>
				<th scope="row"><label
					for="<?php echo esc_attr($args['content'] . '-password_strength'); ?>"><?php echo esc_html(__('Password Strength', 'contact-form-7')); ?></label>
				</th>
				<td><input type="text" name="password_strength" class="classvalue oneline option"
						   id="<?php echo esc_attr($args['content'] . '-password_strength'); ?>" /><br/>
						   1 = <?php echo esc_html(__('Numbers only', 'cf7-add-password-field')); ?><br/>
						   2 = <?php echo esc_html(__('Include letters and numbers', 'cf7-add-password-field')); ?><br/>
						   3 = <?php echo esc_html(__('Include upper and lower case letters and numbers', 'cf7-add-password-field')); ?><br/>
						   4 = <?php echo esc_html(__('Include upper and lower case letters, numbers, and marks', 'cf7-add-password-field')); ?>
				</td>
			</tr>

		</tbody>
		</table>
	</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="password" class="tag code" readonly="readonly" onfocus="this.select()"/>

	<div class="submitbox">
		<input type="button" class="button button-primary insert-tag"
			   value="<?php echo esc_attr(__('Insert Tag', 'contact-form-7')); ?>"/>
	</div>
</div>
	<?php
}
