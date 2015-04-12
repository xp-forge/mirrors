<?php namespace lang\mirrors\parse;

use lang\MapType;

/**
 * Map type reference
 *
 * @test  xp://lang.mirrors.unittest.MapTypeRefTest
 */
class MapTypeRef extends Resolveable {
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
    return new MapType($this->component->resolve($type));
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