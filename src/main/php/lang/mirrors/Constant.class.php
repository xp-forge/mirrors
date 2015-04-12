<?php namespace lang\mirrors;

use util\Objects;

/**
 * A class constant
 *
 * @test   xp://lang.mirrors.unittest.ConstantTest
 */
class Constant extends \lang\Object {
  private $mirror, $name, $value;

  /**
   * Creates a new constat
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  string $name
   * @param  var $value
   */
  public function __construct($mirror, $name, $value) {
    $this->mirror= $mirror;
    $this->name= $name;
    $this->value= $value;
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return var */
  public function value() { return $this->value; }

  /**
   * Returns the type this member was declared in.
   *
   * @return lang.mirrors.TypeMirror
   */
  public function declaredIn() { return $this->mirror; }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.$this.')';
  }

  /** @return string */
  public function __toString() {
    return $this->name.'= '.Objects::stringOf($this->value);
  }
}