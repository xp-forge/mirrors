<?php namespace lang\mirrors\parse;

use lang\XPClass;
use util\Objects;

class GenericTypeRef extends \lang\Object {
  private $base, $arguments;

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
    return new FunctionType(/*X*/);
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

  /**
   * Returns a string represenation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.$this->base->toString().', '.Objects::stringOf($this->arguments).')';
  }
}