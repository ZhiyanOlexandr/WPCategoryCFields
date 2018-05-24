<?php
/*
Plugin Name: WordPress Category Custom Fields
Plugin URI: http://zhyiano.net/
Description: WordPress Category Custom Fields - Premium plugin which allows you to associate an image and youtube or vimeo video to the category
Version: 1.0
Author: Zhyian Oleksandr
Author URI: http://zhyiano.net/
License: GPL2 or later
Text Domain: wpccf
*/

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly.

define( 'WPCCF_VER', '1.0' );


/**
 *
 * WordPress Category Custom Fields Class
 *
 * @author Zhyian Oleksandr
 *
 */
class WP_CategoryCFields {

    //Instance of this class.
    protected static $instance = null;

    // Initialize the plugin
    private function __construct() {

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array( $this, 'wpccf_enqueue_scripts' ) );
        add_action('admin_enqueue_scripts', array( $this, 'wpccf_admin_enqueue_scripts' ) );

        // Add images and videos to categories
        add_action('category_edit_form_fields',	array( $this, 'wpccf_categories_show_custom_fields' ), 10, 1 );
        add_action('category_add_form_fields',	array( $this, 'wpccf_categories_show_custom_fields' ), 10, 1 );

        add_action('edited_category',	array( $this, 'wpccf_categories_save_custom_fields' ), 10, 1 );
        add_action('created_category',	array( $this, 'wpccf_categories_save_custom_fields' ), 10, 1 );

        // Show image and video on archive page
        add_filter('category_description', array( $this, 'wpccf_show_category_custom_fild' ) );

        // Load plugin text domain
        add_action( 'plugins_loaded', array( $this, 'wpccf_load_plugin_textdomain' ) );

    }

    // Return an instance of this class.
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;

    }

    // Load the plugin text domain for translation.
    public function wpccf_load_plugin_textdomain() {

        load_plugin_textdomain( 'wpccf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    }

    // Enqueue scripts and styles
    public function wpccf_enqueue_scripts() {

        wp_enqueue_style('wpccf-style', plugins_url('/css/style.css', __FILE__));

    }

    // Enqueue scripts and styles for admin
    public function wpccf_admin_enqueue_scripts() {

        wp_enqueue_media();
        wp_enqueue_style('wpccf-style', plugins_url('/css/style.css', __FILE__));
        wp_enqueue_script('wpccf-script', plugins_url('/js/init.js', __FILE__), array('jquery'), null, true);

    }

    // Add the fields to the "category" taxonomy, using callback function
    public function wpccf_categories_show_custom_fields($cat) {

        //Check if category present
        $cat_id = !empty($cat->term_id) ? $cat->term_id : 0;

        // Add category's image section
        echo ((int) $cat_id > 0 ? '<tr' : '<div') . ' class="form-field">'
            . ((int) $cat_id > 0 ? '<th valign="top" scope="row">' : '<div>');
        ?><label for="wpccf-category-image"><?php esc_html_e('Image:', 'wpccf'); ?></label><?php
        echo ((int) $cat_id > 0 ? '</th>' : '</div>')
            . ((int) $cat_id > 0 ? '<td valign="top">' : '<div>');

        // Input with image ID
        $cat_img_id = $cat_id > 0 ? get_term_meta ( $cat->term_id, 'image', true ) : '';
        ?><input type="hidden" id="wpccf-category-image" class="wpccf-image-selector-field" name="wpccf_category_image" value="<?php
        echo $cat_img_id; ?>"><?php

        // Image's section: add and reset buttons
        ?><input type="button" id="wpccf-category-image-button" class="button wpccf-image-selector" value="<?php
        esc_html_e( 'Choose Image', 'wpccf'); ?>">
        <input type="button" id="wpccf-category-image-remove-button" class="button wpccf-image-remove" value="<?php
        esc_html_e( 'Remove Image', 'wpccf'); ?>"><?php

        // Image preview
        ?><span class="wpccf-image-selector-preview"><?php
        if ( $cat_img_id ) {
            echo wp_get_attachment_image ( $cat_img_id, 'medium' );
        }
        ?></span><?php
        echo (int) $cat_id > 0 ? '</td></tr>' : '</div></div>';

        // Add category's video section
        echo ((int) $cat_id > 0 ? '<tr' : '<div') . ' class="form-field">'
            . ((int) $cat_id > 0 ? '<th valign="top" scope="row">' : '<div>');
        ?><label for="wpccf-category-video"><?php esc_html_e('Video (url):', 'wpccf'); ?></label><?php
        echo ((int) $cat_id > 0 ? '</th>' : '</div>')
            . ((int) $cat_id > 0 ? '<td valign="top">' : '<div>');

        // Input with image URL
        $cat_video_url = $cat_id > 0 ? get_term_meta ( $cat->term_id, 'video', true ) : '';
        ?><input type="url" id="wpccf-category-video" class="wpccf-video-selector-field" name="wpccf_category_video" value="<?php
        echo $cat_video_url; ?>"><?php

        // Video's section: add and reset buttons
        ?><p><input type="button" id="wpccf-category-video-button" class="button wpccf-video-selector" value="<?php
        esc_html_e( 'Load Video', 'wpccf'); ?>">
        <input type="button" id="wpccf-category-video-remove-button" class="button wpccf-video-remove" value="<?php
        esc_html_e( 'Remove Video', 'wpccf'); ?>"></p><p><?php

        // Image preview
        ?><span class="wpccf-video-selector-preview"><?php
        if ( $cat_video_url ) {
            echo wp_oembed_get( $cat_video_url );
        }
        ?></span></p><p class="description"><?php
        esc_html_e('Please sure, video URL must be provided in a valid format.', 'wpccf');
        ?></p><p class="description"><?php
        esc_html_e('For YouTube (example): https://www.youtube.com/watch?v=zHQhQVFgKU4 or https://youtu.be/zHQhQVFgKU4', 'wpccf');
        ?></p><p class="description"><?php
        esc_html_e('For Vimeo (example): https://vimeo.com/35652044', 'wpccf');
        ?></p><?php
        echo (int) $cat_id > 0 ? '</td></tr>' : '</div></div>';
    }

    // Save the fields to the "category" taxonomy, using callback function
    public function wpccf_categories_save_custom_fields($term_id) {

        //Save image if it is present
        if (isset($_POST['wpccf_category_image'])) {
            update_term_meta($term_id, 'image', $_POST['wpccf_category_image']);
        }

        //Save video if it is present
        if (isset($_POST['wpccf_category_video'])) {
            update_term_meta($term_id, 'video', $_POST['wpccf_category_video']);
        }
    }

    // Show image on archive page
    public function wpccf_show_category_custom_fild($cat_descr) {

        $output = '';

        //Check if page is category archive
        if(is_category()) {
            ob_start();
            $cat = get_the_category();

            //Check if image is present
            $cat_img_id = get_term_meta($cat[0]->term_id, 'image', true);
            if ($cat_img_id) {

                //Show image
                ?><div class="wpccf-category-featured-image"><?php
                echo wp_get_attachment_image($cat_img_id, 'large');
                ?></div><?php
            }

            //Check if video is present
            $cat_video_url = get_term_meta ($cat[0]->term_id, 'video', true );
            if ( $cat_video_url ) {

                //Show video
                ?><div class="wpccf-category-featured-video"><?php
                echo wp_oembed_get( $cat_video_url );
                ?></div><?php
            }
            $output = ob_get_contents();
            ob_end_clean();
        }

        $output .= $cat_descr;
        return $output;
    }
}

add_action( 'plugins_loaded', array( 'WP_CategoryCFields', 'get_instance' ), 0 );