<?php namespace lang\mirrors\parse;

use lang\Objects;

class Member extends Resolveable {
  private $type;

  public function __construct($type, $name) {
    $this->type= $type;
    $this->name= $name;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.Source $source
   * @return var
   */
  public function resolve($type) {
    $resolved= $type->resolve($this->type);
    if ('class' === $this->name) {
      return literal($resolved->name());
    } else if ('$' === $this->name{0}) {
      return $resolved->fields()->named(substr($this->name, 1))->value(null);
    } else {
      return $resolved->constants()->named($this->name)->value();
    }
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->name === $cmp->name &&
      $this->type === $cmp->type
    );
  }

  /** @return string */
  public function __toString() { return $this->type.'::'.$this->name; }
}