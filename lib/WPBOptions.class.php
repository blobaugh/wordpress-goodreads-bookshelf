<?php

class WPBOptions {
    
    public function __construct() {
        
        // Add bookself options page
        add_action('admin_menu', array( &$this, 'addOptionsPage' ) );
        
        // Setup sane default options
        add_action( 'init', array( &$this, 'setDefaultOptions' ) );
    }
    
    public function addOptionsPage() {
        add_options_page('Goodreads Bookshelf Settings', 'Goodreads Bookshelf', 1, WPB_TEXTDOMAIN, array( &$this, 'renderOptionsPage' ) );
    }
    
    public function renderOptionsPage() {        
                
                require_once( WPB_PLUGIN_DIR . 'views/wp-admin/options_page.php' );
    } // end function renderOptionsPage
    
    public function getOption( $Key ) {
        return get_option( $Key );
    }
    
    public function setOption( $Key, $Value ) {
        return update_option( $Key, $Value );
    }
    
    public function setDefaultOptions() {

      //  add_option('bookshelf_cachepath', dirname(__FILE__).'/bookshelf_cache.xml'); // Default cache location
        add_option('bookshelf_userid', ''); // Default to ''
        add_option('bookshelf_private','false'); // Default to 'false'
        add_option('bookshelf_secret_key',''); // Default to ''
        add_option('bookshelf_shelf', 'read'); // Default to 'read'
        add_option('bookshelf_custom', ''); // Default ''
        add_option('bookshelf_book_limit', 5); // Default to max of 5 books
        add_option('bookshelf_sort', 'avg_rating'); // Default to 'avg_rating'
        add_option('bookshelf_order', 'd'); // Default to 'd'
        add_option('bookshelf_update_frequency', 3600); // Default to 'Every hour'
        add_option('bookshelf_display_format', '<li><img src="[cover]" alt="[title] by [author]" style="width:45px" /><br />[title] by [author]</li>'); // Default format

    }
    
} // end class