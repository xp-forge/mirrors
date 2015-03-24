<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;

abstract class AbstractFieldTest extends \unittest\TestCase {
  protected $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror($this->getClassName());
  }

  /**
   * Retrieves a fixture method by a given name
   *
   * @param  string $name
   * @return lang.mirrors.Field
   */
  protected function fixture($name) {
    return $this->type->fields()->named($name);
  }
}