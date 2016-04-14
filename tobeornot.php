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
        $date = DateTime::createFromFormat( 'd.m.Y H:i', $_POST['tobeornot_date'] );
        update_post_meta(
            $post_id,
            '_tobeornot_date',
            $date->getTimestamp()
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
        wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ) );
        wp_enqueue_script( 'jquery-ui-timepicker-addon-ru', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/i18n/jquery-ui-timepicker-ru.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider', 'jquery-ui-timepicker-addon') );
        wp_enqueue_style( 'jquery-ui-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.css',array( 'jquery-ui-datepicker-style' ) );
        wp_enqueue_script( 'tobeornot-admin', plugin_dir_url( __FILE__ ) . '/tobeornot-admin.js', array( 'jquery' ) );
    }
}
add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

/*
 *                  ADMIN COLUMNS
 */

/* Manage columns */
function manage_columns_for_posts( $columns ) {
    $columns['tobeornot_counter'] = 'До завершения';

    return $columns;
}
add_action( 'manage_post_posts_columns', 'manage_columns_for_posts' );

/* Populate columns */
function populate_posts_columns( $column, $post_id ) {
    if ( $column == 'tobeornot_counter' ) {
        $tobeornot_timestamp = get_post_meta( $post_id, '_tobeornot_date', true );

        if ( !empty( $tobeornot_timestamp ) ) {
            echo tobeornot_interval( $tobeornot_timestamp );
        }

    }
}
add_action( 'manage_post_posts_custom_column', 'populate_posts_columns', 10, 2 );

/**
 * Represent a date interval from now till given date
 */
function tobeornot_interval ( $event_timestamp ) {
    $now = new DateTime();
    $counter = DateTime::createFromFormat( 'U', $event_timestamp )->diff( $now );

    if ( $event_timestamp < $now->format( 'U' ) ) return 'завершён';
    
    $message = '';
    $message .= ( $counter->y ) ? $counter->y . ' г. ' : '';
    $message .= ( $counter->m ) ? $counter->m . ' м. ' : '';
    $message .= ( $counter->d ) ? $counter->d . ' д. ' : '';
    $message .= ( $counter->h ) ? $counter->h . ' ч. ' : '';
    $message .= ( $counter->i ) ? $counter->i . ' м. ' : '';
    $message .= ( $counter->s ) ? $counter->s . ' с. ' : '';

    return $message;
}
