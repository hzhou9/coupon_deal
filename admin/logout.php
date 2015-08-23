<?php

if( \user\main::logout() )

  echo '<div style="text-align: center; margin-top: 20px;">

  <h2>' . $LANG['msg_loggiout'] . '</h2>';
  echo '<meta http-equiv="refresh" content="1; url=index.php">

  </div>';