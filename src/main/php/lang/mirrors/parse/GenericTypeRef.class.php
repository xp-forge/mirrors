<?php namespace lang\mirrors\parse;

use lang\XPClass;
use util\Objects;

/**
 * References a generic type
 *
 * @test   xp://lang.mirrors.unittest.GenericTypeRefTest
 */
class GenericTypeRef extends Resolveable {
  private $base, $arguments;

  /**
   * @param  lang.mirrors.parse.ReferenceTypeRef $base
   * @param  lang.mirrors.parse.TypeRef[] $arguments
   */
  public function __construct($base, $arguments) {
    $this->base= $base;
    $this->arguments= $arguments;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.TypeMirror $type
   * @return var
   */
  public function resolve($type) {
    $base= $this->base->resolve($type);
    $arguments= array_map(function($arg) use($type) { return $arg->resolve($type); }, $this->arguments);
    return $base->newGenericType($arguments);
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->base->equals($cmp->base) &&
      Objects::equal($this->arguments, $cmp->arguments)
    );
  }

  /** @return string */
  public function __toString() { return $this->base.', '.Objects::stringOf($this->arguments); }
}