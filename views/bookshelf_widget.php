<?php 

echo $before_widget; 
echo $before_title . $instance['title'] . $after_title;

echo "<ul class='goodreads_bookshelf_widget'>";
foreach( $shelf AS $book ) {
    //echo '<pre>'; var_dump($book);die();
    echo "<li><div>";
    
    echo "<div class='title'><a href='" . $book['book']['link'] . "'>" . $book['book']['title'] . "</a></div>";
    echo "<div class='cover'><a href='" . $book['book']['link'] . "'><img src='" . $book['book']['image_url'] . "' /></a></div>";
    echo "</div></li>";
}
echo "</ul>";




echo $after_widget;