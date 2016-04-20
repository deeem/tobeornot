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
    require_once plugin_dir_path( __FILE__ ) . '/partials/metabox.php';
    ob_end_flush();
}

/* Saving Values */
add_action( 'save_post', 'tobeornot_save_postdata' );
function tobeornot_save_postdata( $post_id ) {
    if ( array_key_exists('tobeornot_date', $_POST ) ) {
        if ( !empty( $_POST['tobeornot_date'] ) ) {
            $date = DateTime::createFromFormat( 'd.m.Y H:i', $_POST['tobeornot_date'] );
            update_post_meta(
                $post_id,
                '_tobeornot_date',
                $date->getTimestamp()
            );
        }
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

/* Public scripts */
function enqueue_public_scripts() {
    wp_enqueue_style( 'tobeornot-public', plugin_dir_url( __FILE__ ) . '/tobeornot-public.css' );
    wp_enqueue_script( 'tobeornot-public', plugin_dir_url( __FILE__ ) . '/tobeornot-public.js', array( 'jquery' ) );
    $ajax_params = array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'post_id' => get_the_ID(),
        'nonce' => wp_create_nonce( 'tobeornot-nonce' )
     );
    wp_localize_script( "tobeornot-public", "tobeornot_ajax", $ajax_params );
}
add_action( 'wp_enqueue_scripts', 'enqueue_public_scripts' );

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
        $counter = do_shortcode( '[tobeornot admin id=' . $post_id . ']' );
        if ( $counter ) {
            echo $counter;
        }
    }
}
add_action( 'manage_post_posts_custom_column', 'populate_posts_columns', 10, 2 );

/*
 *           DISPLAYING COUNTER AND POLL
 */

/* Modify title */
function change_post_title( $title, $id ) {
    if ( 'post' == get_post_type() && in_the_loop() ) {
        $counter = do_shortcode( '[tobeornot counter id=' . $id . ']' );
        if ( is_single() ) {
            $title = $counter . $title;
        }
        if ( is_archive() ) {
            $title = $title . $counter;
        }
    }

    return $title;
}
add_filter( 'the_title', 'change_post_title', 10, 2 );

/* Modify content */
function change_post_content( $content ) {
    if ( 'post' == get_post_type() ) {
        $voter = do_shortcode( '[tobeornot voter]' );
        return $content . $voter;
    }
}
add_filter( 'the_content', 'change_post_content' );

/*
 *          SHORTCODES
 */

/* [tobeontop] Shortcode
 * Выводит ссылки на посты, отобранные по 3-м критериям:
 * - по дате публикации (ранние сверху)
 * - по самым голосуемым
 * - по самым близким к завершению
 * Если выводится на главной странице, то отбор идёт по всем постам
 * во всех категориях постов, а если из категории, то выбираются посты
 * принадлежащие этой категории
 */
function tobeontop_shortcode( $atts ) {
    if ( ! 'post' == get_post_type() ) return;

    $html = '';

    if ( is_front_page() ) {

        // отобрать посты по дате публикации
        $args_latest = array(
            'posts_per_page' => 5,
            'meta_query' => array(
                array(
                    'key' => '_tobeornot_date',
                    'value' => '0',
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        // отобрать самые голосуемые
        $args_popular = array(
            'posts_per_page' => 5,
            'meta_query' => array(
                array(
                    'key' => '_tobeornot_votes',
                    'value' => '0',
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => '_tobeornot_votes',
            'order' => 'DESC'
        );
        // отобрать подходищие к завершению
        $now = new DateTime();
        $args_closest = array(
            'posts_per_page' => 5,
            'meta_query' => array(
                array(
                    'key' => '_tobeornot_date',
                    'value' => $now->format( 'U' ),
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => '_tobeornot_date',
            'order' => 'ASC'
        );

        ob_start();
        require plugin_dir_path( __FILE__ ) . '/partials/shortcode_top.php';
        $partial = ob_get_clean();
        $html .= $partial;

    } elseif ( is_category() ){

        $categories = get_the_category();
        $category_id = $categories[0]->term_id;

        // отобрать посты по дате публикации
        $args_latest = array(
            'posts_per_page' => 5,
            'cat' => $category_id,
            'meta_query' => array(
                array(
                    'key' => '_tobeornot_date',
                    'value' => '0',
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        // отобрать самые голосуемые
        $args_popular = array(
            'posts_per_page' => 5,
            'cat' => $category_id,
            'meta_query' => array(
                array(
                    'key' => '_tobeornot_votes',
                    'value' => '0',
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => '_tobeornot_votes',
            'order' => 'DESC'
        );
        // отобрать подходищие к завершению
        $now = new DateTime();
        $args_closest = array(
            'posts_per_page' => 5,
            'cat' => $category_id,
            'meta_query' => array(
                array(
                    'key' => '_tobeornot_date',
                    'value' => $now->format( 'U' ),
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => '_tobeornot_date',
            'order' => 'ASC'
        );

        ob_start();
        require plugin_dir_path( __FILE__ ) . '/partials/shortcode_top.php';
        $partial = ob_get_clean();
        $html .= $partial;
    }

    return $html;
}

/* [tobeornot] Shortcode
 *
 * [tobeornot (counter|voter|result|admin)]
 * counter - счётчик обратного отсчёта
 * voter - кнопки для голосования
 * result - результаты голосования
 * admin - счётчик обратного отсчёта для админки
 */
function tobeornot_shortcode( $atts ) {

    if ( !is_array( $atts ) ) return;

    $id = ( $atts['id'] ) ? $atts['id'] : get_the_ID();
    $message = counter_message( $id );

    $html = '';
    ob_start();

    if ( in_array( 'counter', $atts ) ) {

        require plugin_dir_path( __FILE__ ) . '/partials/shortcode_counter.php';

    } elseif ( in_array( 'admin', $atts ) ) {

        require plugin_dir_path( __FILE__ ) . '/partials/shortcode_admin.php';

    } elseif ( in_array( 'voter', $atts ) ) {

        if ( !empty( $message ) ) {
            require plugin_dir_path( __FILE__ ) . '/partials/shortcode_voter.php';
        }

    } elseif ( in_array( 'result', $atts ) ) {

        $tobeornot_true = get_post_meta( $id, '_tobeornot_true', true );
        $tobeornot_false = get_post_meta( $id, '_tobeornot_false', true );
        require plugin_dir_path( __FILE__ ) . '/partials/shortcode_result.php';
        
    }

    $partial = ob_get_clean();
    $html .= $partial;

    return $html;
}
function tobeornot_shortcode_register() {
    add_shortcode( 'tobeornot', 'tobeornot_shortcode' );
    add_shortcode( 'tobeontop', 'tobeontop_shortcode' );
}
add_action( 'init', 'tobeornot_shortcode_register' );

/**
 * Формирует сообщение о дате завершения голосования
 * @param integer $post_id post id
 * @return string|false|null сообщение с оставшемя временем, null в случае просроченной даты, false в случае не установленной даты
 */
function counter_message( $post_id ) {
    $timestamp = get_post_meta( $post_id, '_tobeornot_date', true );

    if ( empty( $timestamp ) )  return FALSE;

    $now = new DateTime();
    $counter = DateTime::createFromFormat( 'U', $timestamp )->diff( $now );

    if ( $timestamp < $now->format( 'U' ) ) return NULL;

    $message = '';
    $message .= ( $counter->y ) ? $counter->y . ' г. ' : '';
    $message .= ( $counter->m ) ? $counter->m . ' м. ' : '';
    $message .= ( $counter->d ) ? $counter->d . ' д. ' : '';
    $message .= ( $counter->h ) ? $counter->h . ' ч. ' : '';
    $message .= ( $counter->i ) ? $counter->i . ' м. ' : '';
    $message .= ( $counter->s ) ? $counter->s . ' с. ' : '';

    return $message;
}

/*
 *                  AJAX
 */
 function tobeornot_ajax_public() {
    // check nonce
    $nonce = $_POST['nonce'];
    if ( ! wp_verify_nonce( $nonce, 'tobeornot-nonce' ) ) {
        die ( 'Busted!' );
    }

    $post_id = intval( $_POST['post_id'] );
    if ( ! counter_message( $post_id ) ) {
        die ( 'Busted!' );
    }

    $result = $_POST['result'];
    if ( ! in_array( $result, array( 'true', 'false' ) ) ) {
        die ( 'Busted!' );
    }

    if ( $result == 'true' ) {
        $vote_true = intval( get_post_meta( $post_id, '_tobeornot_true', true ) );
        update_post_meta( $post_id, '_tobeornot_true', $vote_true + 1 );
    }

    if ( $result == 'false' ) {
        $vote_false = intval( get_post_meta( $post_id, '_tobeornot_false', true ) );
        update_post_meta( $post_id, '_tobeornot_false', $vote_false + 1 );
    }

    $counter = intval( get_post_meta( $post_id, '_tobeornot_votes', true ) );
    update_post_meta( $post_id, '_tobeornot_votes' , ++$counter );

    // response output
    // header( "Content-Type: application/json" );
    // echo $response;
    exit;
 }
 add_action( 'wp_ajax_tobeornot-ajax-public', 'tobeornot_ajax_public' );
 add_action( 'wp_ajax_nopriv_tobeornot-ajax-public', 'tobeornot_ajax_public' );
