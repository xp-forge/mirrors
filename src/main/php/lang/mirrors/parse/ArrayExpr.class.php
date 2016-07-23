<?php namespace lang\mirrors\parse;

use util\Objects;

/**
 * Represents a parsed array expression - the PHP way: array or map.
 *
 * @test  xp://lang.mirrors.unittest.ArrayExprTest
 */
class ArrayExpr extends \lang\Object {
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
  public function resolve($type) {
    $resolved= [];
    foreach ($this->backing as $key => $value) {
      $resolved[$key]= $value->resolve($type);
    }
    return $resolved;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'('.Objects::stringOf($this->backing).')';
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