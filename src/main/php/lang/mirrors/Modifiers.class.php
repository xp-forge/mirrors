<?php namespace lang\mirrors;

/**
 * Modifiers
 */
class Modifiers extends \lang\Object {
  private $bits;

  /**
   * Creates a new modifiers instance
   *
   * @param  int $bits
   */
  public function __construct($bits) {
    $this->bits= $bits;
  }

  /**
   * Returns whether a given value is equal to this member
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->bits === $cmp->bits;
  }
}