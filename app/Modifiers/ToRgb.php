<?php
namespace App\Modifiers;
use Statamic\Modifiers\Modifier;

class ToRgb extends Modifier
{
  /**
   * Convert a HEX color (e.g. #ff9900) to an RGB string: "255 153 0"
   *
   * @param mixed  $value    The value to be modified
   * @param array  $params   Any parameters used in the modifier
   * @param array  $context  Contextual values
   * @return mixed
   */
  public function index($value, $params, $context)
  {
    // Remove '#' if present
    $hex = ltrim($value, '#');

    // Support short HEX (#fff)
    if (strlen($hex) === 3) {
      $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
      $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
      $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
    } else {
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
    }

    return "$r $g $b";
  }
}
