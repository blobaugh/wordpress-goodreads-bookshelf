<?php

add_shortcode( 'goodreads_shelf',  'goodreads_shelf_shortcode' );

function goodreads_shelf_shortcode( $instance ) {
    global $wpb_api;

    
    $defaults = array( 'title' => 'My Goodreads Bookshelf', 'id' => get_option( 'bookshelf_userid' ), 'shelf' => get_option( 'bookshelf_shelf' ), 'num_to_show' => get_option( 'bookshelf_book_limit' ) );
    $instance = wp_parse_args( $instance, $defaults );
    
    $shelf = $wpb_api->getShelf( $instance );


    require_once( WPB_PLUGIN_DIR . 'views/bookshelf_shortcode.php' );
        
    
}