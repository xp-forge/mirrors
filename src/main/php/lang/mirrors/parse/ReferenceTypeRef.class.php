<?php namespace lang\mirrors\parse;

use lang\XPClass;

/**
 * Reference type
 *
 * @test  xp://lang.mirrors.unittest.ReferenceTypeRefTest
 */
class ReferenceTypeRef extends \lang\Object {
  private $name;

  /**
   * Creates a new reference type reference
   *
   * @param  string $name Qualified or unqualified as well as "self" and "parent" keywords
   */
  public function __construct($name) {
    $this->name= $name;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.TypeMirror $type
   * @return var
   */
  public function resolve($type) {
    return new XPClass($type->resolve($this->name)->reflect);
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }

  /**
   * Returns a string represenation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->name.'>';
  }
}