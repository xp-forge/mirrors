<?php namespace lang\mirrors\parse;

use util\Objects;

class NewInstance extends Resolveable {
  private $type;

  public function __construct($type, $arguments) {
    $this->type= $type;
    $this->arguments= $arguments;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.TypeMirror $type
   * @return var
   */
  public function resolve($type) {
    $constructor= $type->resolve($type, $this->type)->constructor();
    return $constructor->newInstance(...array_map(
      function($arg) use($unit) { return $arg->resolve($unit); },
      $this->arguments
    ));
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->type === $cmp->type &&
      Objects::equal($this->arguments, $cmp->arguments)
    );
  }

  /** @return string */
  public function __toString() { return 'new '.$this->type.'('.Objects::stringOf($this->arguments).')'; }
}