<?php
/**
 * Plugin Name: Tobeornot
 * Version: 0.1-alpha
 * Description: Будет или не будет
 * Author: deeem
 */

/*
 *                  METABOX
 */

 /* Adds a meta box to the post edit screen */
add_action( 'add_meta_boxes', 'tobeornot_add_custom_box' );
function tobeornot_add_custom_box() {
    add_meta_box(
        'tobeornot_box_id',             // Unique ID
        'To Be or Not To Be?',          // Box title
        'tobeornot_inner_custom_box',   // Content callback
        'post'                          // post type
    );
}

 /* Prints the box content */
function tobeornot_inner_custom_box( $post ) {
    ob_start();
    require_once plugin_dir_path( __FILE__ ) . '/tobeornot_custom_box.php';
    ob_end_flush();
}

/* Saving Values */
add_action( 'save_post', 'tobeornot_save_postdata' );
function tobeornot_save_postdata( $post_id ) {
    if ( array_key_exists('tobeornot_title', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_tobeornot_title',
            $_POST['tobeornot_title']
        );
    }
    if ( array_key_exists('tobeornot_description', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_tobeornot_description',
            $_POST['tobeornot_description']
        );
    }
    if ( array_key_exists('tobeornot_date', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_tobeornot_date',
            $_POST['tobeornot_date']
        );
    }
}

/*
 *                  ENQUEUE SCRIPTS
 */

 /* Admin Screen */
function enqueue_scripts() {
    $screen = get_current_screen();
    if ( $screen->id == 'post' ) {
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ) );
        wp_enqueue_script( 'jquery-ui-timepicker-addon-ru', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/i18n/jquery-ui-timepicker-ru.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider', 'jquery-ui-timepicker-addon') );
        wp_enqueue_style( 'jquery-ui-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.css' );
        wp_enqueue_script( 'tobeornot-admin', plugin_dir_url( __FILE__ ) . '/tobeornot-admin.js', array( 'jquery' ) );
    }
}
add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );
