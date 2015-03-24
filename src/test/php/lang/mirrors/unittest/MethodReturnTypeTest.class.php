<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\Type;
use lang\Primitive;

class MethodReturnTypeTest extends AbstractMethodTest {

  private function noReturnFixture() { }

  /** @return void */
  private function shortFormFixture() { }

  /**
   * Returns the value "Test"
   *
   * @return string
   */
  private function longFormFixture() { return 'Test'; }

  /** @return self */
  private function selfFixture() { }

  /** @return parent */
  private function parentFixture() { }

  #[@test]
  public function var_is_default_if_no_return_type_documented() {
    $this->assertEquals(Type::$VAR, $this->fixture('noReturnFixture')->returns());
  }

  #[@test]
  public function short_form() {
    $this->assertEquals(Type::$VOID, $this->fixture('shortFormFixture')->returns());
  }

  #[@test]
  public function long_form() {
    $this->assertEquals(Primitive::$STRING, $this->fixture('longFormFixture')->returns());
  }

  #[@test]
  public function self_supported() {
    $this->assertEquals($this->getClass(), $this->fixture('selfFixture')->returns());
  }

  #[@test]
  public function parent_supported() {
    $this->assertEquals($this->getClass()->getParentclass(), $this->fixture('parentFixture')->returns());
  }
}