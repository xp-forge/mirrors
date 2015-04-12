<?php namespace lang\mirrors\parse;

use lang\ArrayType;

/**
 * Array type reference
 *
 * @test  xp://lang.mirrors.unittest.ArrayTypeRefTest
 */
class ArrayTypeRef extends Resolveable {
  private $component;

  public function __construct($component) {
    $this->component= $component;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.TypeMirror $type
   * @return var
   */
  public function resolve($type) {
    return new ArrayType($this->component->resolve($type));
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->component->equals($cmp->component);
  }

  /** @return string */
  public function __toString() { return (string)$this->component; }
}