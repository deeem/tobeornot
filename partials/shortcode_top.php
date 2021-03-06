<?php global $post; ?>
<div class="tobeornot_dashboard">
    <div class="tobeornot_dashboard_section tobeornot_dashboard_section_latest">
        <h4>самые последние</h4>
        <ul>
        <?php
        $query_latest = new WP_Query( $args_latest );
        if ( $query_latest->have_posts() ) {
            while ( $query_latest->have_posts() ) {
                $query_latest->the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php echo $post->post_title;?></a>
                    <span><?php echo do_shortcode( '[tobeornot counter_text]' ); ?></span>
                </li>
            <?php
            }
            wp_reset_postdata();
        }
        ?>
        </ul>
    </div>
    <div class="tobeornot_dashboard_section tobeornot_dashboard_section_popular">
        <h4>самые голосуемые</h4>
        <ul>
        <?php
        $query_popular = new WP_Query( $args_popular );
        if ( $query_popular->have_posts() ) {
            while ( $query_popular->have_posts() ) {
                $query_popular->the_post();?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php echo $post->post_title;?></a>
                    <span><?php echo do_shortcode( '[tobeornot counter_text]' ); ?></span>
                </li>
            <?php
            }
            wp_reset_postdata();
        }
        ?>
        </ul>
    </div>
    <div class="tobeornot_dashboard_section tobeornot_dashboard_section_closest">
        <h4>подходят к завершению</h4>
        <ul>
        <?php
        $query_closest = new WP_Query( $args_closest );
        if ( $query_closest->have_posts() ) {
            while ( $query_closest->have_posts() ) {
                $query_closest->the_post();?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php echo $post->post_title;?></a>
                    <span><?php echo do_shortcode( '[tobeornot counter_text]' ); ?></span>
                </li>                
            <?php
            }
            wp_reset_postdata();
        }
        ?>
        </ul>
    </div>
</div>
