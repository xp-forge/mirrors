<?php namespace lang\mirrors\unittest;

abstract class AbstractFieldTest extends AbstractMemberTest {

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