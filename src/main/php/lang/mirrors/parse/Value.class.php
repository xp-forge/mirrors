<?php namespace lang\mirrors\parse;

use util\Objects;

/**
 * Represents a parsed value 
 *
 * @test  xp://lang.reflection.unittest.ValueTest
 */
class Value extends \lang\Object {
  private $backing;

  public function __construct($value) {
    $this->backing= $value;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.TypeMirror $type
   * @return var
   */
  public function resolve($type) {
    return $this->backing;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.Objects::stringOf($this->backing).')';
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && Objects::equal($this->backing, $cmp->backing);
  }
}