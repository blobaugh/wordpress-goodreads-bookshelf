=== WordPress Goodreads Bookshelf ===
Contributors: blobaugh, freshmuse, whispert
Tags: goodreads, books, sidebar, widget, shortcode, review
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: trunk

Display a custom list of books from any of your Goodreads.com bookshelves, and stylize it however you want using your own HTML and CSS.

== Description ==

WordPress Goodreads Bookshelf (aka Bookshelf) allows you to display a custom list of books from your [Goodreads](http://goodreads.com) shelves anywhere on your site that PHP is allowed.

*   You can choose from any one of your bookshelves including your custom shelves on [Goodreads](http://goodreads.com).
*   You can specify how many books it should retrieve from the shelf and how often it should update the list on your site.
*   You have full control of how the list is displayed using your own HTML and CSS.  So you can easily make it blend in with the rest of your blog.
*   If you have a private account on [Goodreads](http://goodreads.com) there are specific instructions to set up Bookshelf so that it retrieves your books, but your profile can still remain private.
*   You can use all the standard information (title, author, cover image, isbn) as well as some more detailed information such as rating, date read, review, and many more.

Lots more features to come...

== Installation ==

1.  Upload the files to `wp-content/plugins/bookshelf`
1.  Activate the plugin through the 'Plugins' menu in WordPress
1.  Go to the Bookshelf options page located in _Settings � Bookshelf_ and enter your [Goodreads](http://goodreads.com) user data
1.  Place `<?php if(function_exists('bookshelf')) { bookshelf(); } ?>` in your templates
1.  If your going to put Bookshelf in a widget you will need to [install an execPHP plugin](http://wordpress.org/extend/plugins/php-code-widget/)

Enjoy! :)

== Frequently Asked Questions ==

= Do you have any questions to answer? =

None yet, but I will do my best to answer questions as quickly as I can.

== Screenshots ==

1. Bookshelf Options page in admin.
2. Default settings - Bookshelf in sidebar widget.
3. Default settings - Bookshelf in a page template.
4. Customized in a sidebar widget.


