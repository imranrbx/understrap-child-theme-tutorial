<?php

class ElementorWPCF7Widget extends \Elementor\Widget_Base {

	/*
			return widget name
		*/
	public function get_name() {
		return 'wpdev_wpcf7';
	}
	/*
			return title for widgets as label
		*/
	public function get_title() {
		return 'Contact Form 7';
	}
	/*
			simple return icon as icon above label
		*/
	public function get_icon() {
		return 'fa fa-envelope';
	}
	public function get_categories() {
		return array( 'general' );
	}
	protected function _register_controls() {
		$args = array(
			'post_type' => 'wpcf7_contact_form',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'nopaging' => true,
		);
		$query = new WP_Query( $args );
		$options = array();
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				$title = get_the_ID() . '_' . get_the_title();
				$options[ $title ] = get_the_title();
		endwhile;
		endif;
		$this->start_controls_section(
			'content_section',
			array(
				'label' => 'Cotact Form 7',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'cf7form',
			array(
				'label' => 'Select a Contact Form 7',
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $options,
			)
		);
		$this->end_controls_section();
	}
	protected function render() {
		$atts = $this->get_settings_for_display();
		$form = $atts['cf7form'];
		$cf7 = explode( '_', $form );
		echo do_shortcode( "[contact-form-7 id='{$cf7[0]}' title='{$cf7[1]}']" );
	}
}
