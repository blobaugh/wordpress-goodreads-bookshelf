<?php 

echo "<ul class='goodreads_bookshelf_shortcode'>";
foreach( $shelf AS $book ) {
    
    echo "<li><div>";
    
    echo "<div class='title'><a href='" . $book['book']['link'] . "'>" . $book['book']['title'] . "</a></div>";
    echo "<div class='cover'><a href='" . $book['book']['link'] . "'><img src='" . $book['book']['image_url'] . "' /></a></div>";
    echo "</div></li>";
}
echo "</ul>";



