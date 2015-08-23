<div class="page-content">

<?php
 
if( exists() && $page = the_page() ) {

echo '<h2>' . $page->name . '</h2>';

echo nl2br( $page->text );

} else {

  echo read_template_part( '404' );

}

?>

</div>