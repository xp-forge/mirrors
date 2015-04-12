<?php namespace lang\mirrors\parse;

use lang\ElementNotFoundException;

/**
 * Represents a parsed value 
 *
 * @test  xp://lang.reflection.unittest.ParsedConstantTest
 */
class Constant extends Resolveable {
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
    throw new ElementNotFoundException('Undefined constant "'.$this->name.'"');
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

  /** @return string */
  public function __toString() { return $this->name; }
}