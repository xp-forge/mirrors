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
      return literal($resolved->typeName());
    } else if ('$' === $this->name{0}) {
      $field= $resolved->fieldNamed(substr($this->name, 1));
      return $field['read'](null);
    } else {
      return $resolved->constantNamed($this->name);
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