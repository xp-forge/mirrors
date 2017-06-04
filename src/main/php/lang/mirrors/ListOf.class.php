<?php namespace lang\mirrors;

use util\Objects;

trait ListOf {

  /**
   * Compares a given value to this list
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self
      ? Objects::compare(iterator_to_array($this), iterator_to_array($value))
      : 1
    ;
  }

  /** @return string */
  public function hashCode() { return 'L['.Objects::hashOf(iterator_to_array($this)); }

  /** @return string */
  public function toString() {
    $s= nameof($this)."@[\n";
    foreach ($this as $element) {
      $s.= '  '.str_replace("\n", "\n  ", $element->__toString())."\n";
    }
    return $s.']';
  }
}