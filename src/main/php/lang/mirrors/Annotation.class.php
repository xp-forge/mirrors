<?php namespace lang\mirrors;

use util\Objects;

class Annotation extends \lang\Object {
  private $type, $name, $value;

  public function __construct(TypeMirror $type, $name, $value) {
    $this->type= $type;
    $this->name= $name;
    $this->value= $value;
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return var */
  public function value() { return $this->value ? $this->value->resolve($this->type->unit()) : null; }

  /**
   * Returns whether a given value is equal to this annotation
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->name === $cmp->name &&
      Objects::equal($this->value, $cmp->value)
    );
  }
}