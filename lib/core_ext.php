<?php

/**
 * Backward compat version of get_called_class
 *   Try to look backward for 100 lines
 */
if (!function_exists('get_called_class')):
  function get_called_class() {
    $bt = debug_backtrace();
    $lines = file($bt[1]['file']);
    $pattern = '/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/';

    for($line = $bt[1]['line']-1, $i = 0; $i < 100; $i++, $line--) {
      if (preg_match($pattern, $lines[$line], $matches))
        return $matches[1];
    }
    
    return false;
  }
endif;

function call_static() {
  $args = func_get_args();
  $callback = array_shift($args);
  if (is_string($callback))
    $callback = explode('::', $callback);
  return user_func_call_array($callback, $args);
}

function pluralize($word) {
  return $word . 's';
}
?>