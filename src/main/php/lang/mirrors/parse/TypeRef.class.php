<?php namespace lang\mirrors\parse;

use lang\Type;

/**
 * Generic type reference
 *
 * @test  xp://lang.mirrors.unittest.TypeRefTest
 */
class TypeRef extends Resolveable {
  private $type;

  public function __construct(Type $type) {
    $this->type= $type;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.TypeMirror $type
   * @return var
   */
  public function resolve($type) {
    return $this->type;
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->type->equals($cmp->type);
  }

  /** @return string */
  public function __toString() { return $this->type->getName(); }
}