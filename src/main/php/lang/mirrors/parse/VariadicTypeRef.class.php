<?php namespace lang\mirrors\parse;

/**
 * Variadic type reference
 */
class VariadicTypeRef extends Resolveable {
  private $component;

  public function __construct($component) {
    $this->component= $component;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.Source $source
   * @return var
   */
  public function resolve($type) {
    return $this->component->resolve($type);
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