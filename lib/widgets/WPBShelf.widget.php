<?php


add_action( 'widgets_init', create_function( '', 'register_widget( "WPB_Shelf_Widget" );' ) );

class WPB_Shelf_Widget extends WP_Widget{

    
    /**
     * Widget constructor that sets up the widget object with the correct
     * options 
     */
    public function __construct() {
            parent::__construct(
                   WPB_TEXTDOMAIN . 'shelf_widget', // Base ID
                    'GoodReads Bookshelf', // Name
                    array( 'description' => __( 'Display a list of books from a Goodreads shelf', 'text_domain' ), ) // Args
            );
    }
    
    
    /**
     * Builds the wp-admin widget form in Apperances -> Widgets
     * 
     * @param Array $instance - Data from the current widget
     */
    public function form( $instance ) {
        // Setup default values if none currently exist
        $defaults = array( 'title' => 'My Goodreads Bookshelf', 'id' => get_option( 'bookshelf_userid' ), 'shelf' => get_option( 'bookshelf_shelf' ), 'per_page' => get_option( 'bookshelf_book_limit' ) );
        
        // Check current instance for values. If none exist apply defaults
        $instance = wp_parse_args( (array) $instance, $defaults );
        
        $s = "<p>Title: <input class='widefat' name='" . $this->get_field_name( 'title' ) . "' type='text' value='" . 
                esc_attr( $instance['title'] ) . "'></p>";
        
        $s .= "<p>Goodreads userid: <input class='widefat' name='" . $this->get_field_name( 'userid' ) . "' type='text' value='" . 
                esc_attr( $instance['userid'] ) . "'></p>";
        
        $s .= "<p>Shelf: <input class='widefat' name='" . $this->get_field_name( 'shelf' ) . "' type='text' value='" . 
                esc_attr( $instance['shelf'] ) . "'></p>";
        
         $s .= "<p>Num books to show (1 - 200): <input class='widefat' name='" . $this->get_field_name( 'per_page' ) . "' type='text' value='" . 
                esc_attr( $instance['per_page'] ) . "'></p>";

        echo $s;
    }
    
    /**
     * Determines how to display to site visitors
     * 
     * @param Array $args - WordPress specific actions (before_widget, after_widget, etc)
     * @param Array $instance - Widget form elements
     */
    public function widget( $args, $instance ) {
        global $wpb_api;
        
        extract( $args ); // Just because this is normal
        
        
        $defaults = array( 'title' => 'My Goodreads Bookshelf', 'id' => get_option( 'bookshelf_userid' ), 'shelf' => get_option( 'bookshelf_shelf' ), 'per_page' => get_option( 'bookshelf_book_limit' ) );
        $instance = wp_parse_args( $instance, $defaults );
        
        $shelf = $wpb_api->getShelf( $instance );
       
        
        require_once( WPB_PLUGIN_DIR . 'views/bookshelf_widget.php' );
        

       
        // End of widget display HTML
        
    }
    
} // end widget