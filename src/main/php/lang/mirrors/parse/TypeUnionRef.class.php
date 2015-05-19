<?php namespace lang\mirrors\parse;

use lang\TypeUnion;
use util\Objects;

class TypeUnionRef extends Resolveable {
  private $types;

  /**
   * Creates a new instance
   *
   * @param  lang.mirrors.parse.TypeRef[] $arguments
   */
  public function __construct($types) {
    $this->types= $types;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.Source $source
   * @return var
   */
  public function resolve($type) {
    return new TypeUnion(array_map(
      function($arg) use($type) { return $arg->resolve($type); },
      $this->types
    ));
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && Objects::equal($this->types, $cmp->types);
  }

  /** @return string */
  public function __toString() { return Objects::stringOf($this->types); }
}