<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;

abstract class AbstractMethodTest extends \unittest\TestCase {
  protected $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(typeof($this));
  }

  /**
   * Retrieves a fixture method by a given name
   *
   * @param  string $name
   * @return lang.mirrors.Method
   */
  protected function fixture($name) {
    return $this->type->methods()->named($name);
  }
}