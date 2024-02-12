<?php
/*
Plugin Name: WP Query
Plugin URI: localhost/trainingground
Description: This is a query training plugin.
Author: Ian
Version: 1.0
*/

// Define the addaction callback function
function my_plugin_enqueue_styles() {
    // Get the URL to the plugin directory
    $plugin_url = plugin_dir_url( __FILE__ );

    // Enqueue the stylesheet
    wp_enqueue_style( 'my-plugin-styles', $plugin_url . 'wp-query-training.css', array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'my_plugin_enqueue_styles' );

// Define the shortcode callback function
function ian_wp_query_callback() {
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $args = array(
        'post_type' => array('post','page'),
        'posts_per_page' => 1,
        'paged' => $paged, // Pagination
    );

    // The Query
    $the_query = new WP_Query( $args );

    // The Loop
    $output = '<div id="wp-query-plugin">';
    if ( $the_query->have_posts() ) {
        $output .= '<ul>';
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $output .= '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
            $output .= '<div>' . get_the_excerpt() . '</div>';
        }
        $output .= '</ul>';
        $output .= '<div class="pagination">';
        $output .= paginate_links( array(
            'total' => $the_query->max_num_pages,
            'current' => $paged,
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
        ) );
        $output .= '</div>';
    } else {
        $output .= '<p>' . esc_html__( 'Sorry, no posts matched your criteria.', 'wp-query' ) . '</p>';
    }
    $output .= '</div>';

    // Restore original Post Data
    wp_reset_postdata();

    return $output;
}

// Register the shortcode
add_shortcode( 'ian_query', 'ian_wp_query_callback' );
