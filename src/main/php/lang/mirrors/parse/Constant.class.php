<?php namespace lang\mirrors\parse;

/**
 * Represents a parsed value 
 *
 * @test  xp://lang.reflection.unittest.ConstantTest
 */
class Constant extends \lang\Object {
  private $name;

  public function __construct($name) {
    $this->name= $name;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.reflection.CodeUnit $unit
   * @return var
   */
  public function resolve($unit) {
    if (defined($this->name)) {
      return constant($this->name);
    }
    raise('lang.ElementNotFoundException', 'Undefined constant "'.$this->name.'"');
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.$this->name.')';
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }
}