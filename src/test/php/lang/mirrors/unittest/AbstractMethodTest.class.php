<?php namespace lang\mirrors\unittest;

abstract class AbstractMethodTest extends AbstractMemberTest {

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