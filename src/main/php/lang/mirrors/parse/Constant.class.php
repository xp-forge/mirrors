<?php namespace lang\mirrors\parse;

use lang\ElementNotFoundException;

/**
 * Represents a parsed value 
 *
 * @test  xp://lang.mirrors.unittest.ParsedConstantTest
 */
class Constant extends Resolveable {
  private $name;

  public function __construct($name) {
    $this->name= $name;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.CodeUnit $unit
   * @return var
   */
  public function resolve($unit) {
    if (defined($this->name)) {
      return constant($this->name);
    }
    throw new ElementNotFoundException('Undefined constant "'.$this->name.'"');
  }

  /**
   * Compares a given value to this list
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->name, $value->name) : 1;
  }

  /** @return string */
  public function __toString() { return $this->name; }
}