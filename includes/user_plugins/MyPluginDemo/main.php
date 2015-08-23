<?php

if( !$GLOBALS['me']->is_admin ) die;

echo 'action='.$_GET['action'];