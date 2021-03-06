<?php namespace lang\mirrors\parse;

use util\Objects;

/**
 * Represents a parsed value 
 *
 * @test  xp://lang.mirrors.unittest.ValueTest
 */
class Value extends Resolveable {
  private $backing;

  public function __construct($value) {
    $this->backing= $value;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.Source $source
   * @return var
   */
  public function resolve($source) {
    return $this->backing;
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

  /** @return string */
  public function __toString() { return Objects::stringOf($this->backing); }
}