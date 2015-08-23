<?php

echo '<div class="widget widget_search' . ( !$mobile_view ? ' mobile_view' : '' ) . '">';
if( !empty( $title ) ) {
  echo '<h2>' . $title . '</h2>';
}

echo '

<form action="' . tlink( 'search' ) . '" method="GET">
<input type="text" name="s" />
<button>' . $LANG['search'] . '</button>
</form>
</div>';