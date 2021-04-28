<?php

class ElementorPostsWidget extends \Elementor\Widget_Base
{

    /*
            return widget name
        */
    public function get_name()
    {
        return 'wpdev_posts';
    }
    /*
            return title for widgets as label
        */
    public function get_title()
    {
        return 'posts with Thumbnail';
    }
    /*
            simple return icon as icon above label
        */
    public function get_icon()
    {
        return 'eicon-post-list';
    }
    public function get_categories()
    {
        return array( 'general' );
    }
    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            array(
                'label' => 'Content',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );
        $this->add_control(
            'title',
            array(
                'label' => 'Enter Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => 'Type Your Title Here',
                'default' => 'Default Title',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'content_section',
            array(
                'label' => 'Style',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );
        $this->add_control(
            'fcolor',
            array(
                'label' => 'Change Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'input_type' => 'color',
                'default' => '#fff',
            )
        );
        $this->end_controls_section();
    }
    protected function render()
    {
        // $atts = $this->get_settings_for_display();
        // $title = $atts['title'];
        // $color = $atts['fcolor'];
        // echo "<h1 style='color: {$color}'>{$title}</h1>";
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 3,
            'ignore_sticky_posts' => true,
        );
        $q = new WP_Query($args);
        if ($q->have_posts()) :
            while ($q->have_posts()) :
                $q->the_post();
        echo "<div style='display:flex;align-items:center;margin:0.5em' class='elementor__posts'>";
        echo "<figure style='margin-right:1em;' class='img-fluid'>";
        the_post_thumbnail();
        echo '</figure>';
        the_title('<p>', '</p>');
        echo '</div>';
        endwhile;
        endif;
        wp_reset_query();
    }
}
