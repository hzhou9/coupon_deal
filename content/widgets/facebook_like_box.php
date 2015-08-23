<?php

echo '<div class="widget widget_facebook-box' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';

if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

echo '<span>';

echo '<iframe src="//www.facebook.com/plugins/likebox.php?href=' . $content . '&amp;width=380&amp;height=220&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=false&amp;appId=903485619675676" scrolling="no" frameborder="0" style="border:none; width: 100%; margin-top: 5px; overflow:hidden; height:290px;" allowTransparency="true"></iframe>';

echo '</span></div>';