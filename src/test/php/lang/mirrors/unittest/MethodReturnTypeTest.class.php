<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Method;
use lang\Type;
use lang\Primitive;
use unittest\actions\VerifyThat;

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

  /** @return iterable */
  private function iterableFixture() { }

  /** @return object */
  private function objectFixture() { }

  /** @return Method */
  private function resolved() { }

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
    $this->assertEquals(typeof($this), $this->fixture('selfFixture')->returns());
  }

  #[@test]
  public function parent_supported() {
    $this->assertEquals(typeof($this)->getParentclass(), $this->fixture('parentFixture')->returns());
  }

  #[@test]
  public function iterable_supported() {
    $this->assertEquals(property_exists(Type::class, 'ITERABLE') ? Type::$ITERABLE : Type::$VAR, $this->fixture('iterableFixture')->returns());
  }

  #[@test]
  public function object_supported() {
    $this->assertEquals(property_exists(Type::class, 'OBJECT') ? Type::$OBJECT : Type::$VAR, $this->fixture('objectFixture')->returns());
  }

  #[@test]
  public function returns() {
    $this->assertEquals(Type::forName('lang.mirrors.Method'), $this->fixture('fixture')->returns());
  }

  #[@test]
  public function returns_is_resolved() {
    $this->assertEquals(Type::forName('lang.mirrors.Method'), $this->fixture('resolved')->returns());
  }
}