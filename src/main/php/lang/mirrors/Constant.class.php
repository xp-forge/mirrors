<?php namespace lang\mirrors;

/**
 * A class constant
 *
 * @test   xp://lang.mirrors.unittest.ConstantTest
 */
class Constant extends \lang\Object {
  private $name, $value;

  /**
   * Creates a new constat
   *
   * @param  string $name
   * @param  var $value
   */
  public function __construct($name, $value) {
    $this->name= $name;
    $this->value= $value;
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return var */
  public function value() { return $this->value; }

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
    return $this->name.'= '.var_export($this->value, true);
  }
}