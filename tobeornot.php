<?php
/**
 * Plugin Name: Tobeornot
 * Version: 0.1-alpha
 * Description: Будет или не будет
 * Author: deeem
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
