<?php namespace lang\mirrors\parse;

use util\Objects;

class Pairs extends Resolveable {
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
    $resolved= [];
    foreach ($this->backing as $key => $value) {
      $resolved[$key]= $value->resolve($type);
    }
    return $resolved;
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