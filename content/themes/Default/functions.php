<?php

function register_widgets() {
  $widgets['right'] = array('name' => 'Right Side Widgets', 'description' => 'Appears on the right side of the page.');
  return $widgets;
}

// do not delete this function, it is an important part of this theme
function theme_has_rewards() {
  return true;
}

// do not delete this function, it is an important part of this theme
function theme_has_products() {
  return true;
}