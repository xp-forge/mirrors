<?php namespace lang\mirrors;

class Constant extends \lang\Object {
  private $name, $value;

  public function __construct($name, $value) {
    $this->name= $name;
    $this->value= $value;
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return var */
  public function value() { return $this->value; }
}