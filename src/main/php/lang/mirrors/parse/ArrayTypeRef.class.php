<?php namespace lang\mirrors\parse;

use lang\ArrayType;

/**
 * Array type reference
 *
 * @test  xp://lang.mirrors.unittest.ArrayTypeRefTest
 */
class ArrayTypeRef extends \lang\Object {
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

  /**
   * Returns a string represenation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->component->toString().'>';
  }
}