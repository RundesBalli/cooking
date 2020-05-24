<?php
// https://gist.github.com/RundesBalli/a987971322ce7122e223393901fd90ec

/**
 * Fractionizer
 * 
 * A simple number format function with quarter, half and three quarter fraction output.
 * 
 * @author    RundesBalli <webspam@rundesballi.com>
 * @copyright 2020 RundesBalli
 * @version   1.0
 * @license   MIT-License
 * @see       https://www.php.net/manual/en/function.number-format.php
 * 
 * @param mixed  $number            Integer or float number to be formatted. Strings will be formatted to float.
 * @param int    $decimals          Decimal count, will be ignoref if $number is dividable by 0.25
 * @param bool   $decimalsIfInt     Ignores $decimals if $number is an integer
 * @param string $decPoint          Character to separate decimal numbers from integers
 * @param string $thousandSeparator Thousand separator
 * 
 * @return string Formatted number
 */
function fractionizer($number, int $decimals = 0, bool $decimalsIfInt = FALSE, string $decPoint = ",", string $thousandSeparator = ".") {
  /**
   * Convert the input number to float.
   */
  $number = floatval($number);
  
  /**
   * Check if the number has no decimals.
   */
  if(fmod($number, 1) == 0) {
    if($decimalsIfInt === FALSE) {
      return number_format($number, 0, $decPoint, $thousandSeparator);
    } else {
      return number_format($number, $decimals, $decPoint, $thousandSeparator);
    }
  }

  /**
   * Check if the number is dividable by 0.25.
   */
  if(fmod($number, 0.25) == 0) {
    $mod = abs(fmod($number, 1));
    if($mod == 0.25) {
      $fraction = "¼"; // Vulgar Fraction One Quarter, U+00BC
    } elseif($mod == 0.5) {
      $fraction = "½"; // Vulgar Fraction One Half, U+00BD
    } elseif($mod == 0.75) {
      $fraction = "¾"; // Vulgar Fraction Three Quarters, U+00BE
    }
    /**
     * It is unnecessary to check if $fmod == 0, because integers have been previously checked and returned.
     */

    /**
     * If the number is negative, it must be "rounded up".
     * 
     * ceil(-2.75) == 2
     * @see https://www.php.net/manual/en/function.ceil.php
     * 
     * floor(2.75) == 2
     * @see https://www.php.net/manual/en/function.floor.php
     */
    if($number < 0) {
      return (ceil($number) == 0 ? NULL : ceil($number)).$fraction;
    }
    return (floor($number) == 0 ? NULL : floor($number)).$fraction;
  }
  
  /**
   * If the Number has no quarter, half or three quarter decimals, it will return without extra formatting.
   */
  return number_format($number, $decimals, $decPoint, $thousandSeparator);
}
?>
