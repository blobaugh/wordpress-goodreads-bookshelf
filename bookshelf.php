<?php
/*
Plugin Name: WorPress Goodreads Bookshelf
Version: beta
Plugin URI: http://thoughtcramps.com/wordpress-goodreads-bookshelf/
Description: Display a custom list of books from your <a href="http://www.goodreads.com" target="_blank">Goodreads</a> bookshelves.  You can choose from any one of your bookshelves including your custom shelves on Goodreads.  Then you can stylize the display of the list however you want using your own HTML and CSS.
Author: Ben Lobaugh
Author URI: http://ben.lobaugh.net
Text Domain: wp-goodreads-bookshelf
*/
define( 'WPB_TEXTDOMAIN', 'wp-goodreads-bookshelf' );
define( 'WPB_PLUGIN_DIR', trailingslashit( dirname( __FILE__) ) );
define( 'WPB_PLUGIN_URL', trailingslashit ( WP_PLUGIN_URL . "/" . basename( __DIR__  ) ) );
define( 'WPB_PLUGIN_FILE', WPB_PLUGIN_DIR . basename( __DIR__  ) . ".php" );

define( 'WPB_GOODREADS_API_ENTRY', 'http://goodreads.com/' );


/*
 * Global level connection for the Goodreads API
 */
require_once( WPB_PLUGIN_DIR . 'lib/WPBGoodreadsApi.class.php' );
$wpb_api = new WPBGoodreadsApi( );




/*
 * Setup and manage options for system
 */
require_once( WPB_PLUGIN_DIR . 'lib/WPBOptions.class.php' );
$wpb_options = new WPBOptions();



/*
 * Grab the widgets
 */
require_once( WPB_PLUGIN_DIR . 'lib/widgets/widgets.php' );

/*
 * Grab the shortcodes
 */
require_once( WPB_PLUGIN_DIR . 'lib/shortcodes/shortcodes.php' );