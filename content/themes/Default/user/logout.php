<?php if( $GLOBALS['me'] ) { ?>

<div class="content">

<div class="left">

<?php

if( logout() ) {

echo '<div style="margin: 20% 0; color: #0086CE; font-size: 35px; text-align: center;">

Thank You !

<span style="display: block; margin-top: 10px; color: #cccccc; font-size: 20px;">Thank you for visiting, we hope you come back soon :(</span>

</div>';

}

?>

</div>

<div class="right"></div>

</div>

<?php

} else {

    echo read_template_part( '404' );

}

?>