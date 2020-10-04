<?php namespace lang\mirrors\unittest;

use lang\mirrors\{Method, TypeMirror};
use lang\{Primitive, Type};
use unittest\Test;
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

  #[Test]
  public function var_is_default_if_no_return_type_documented() {
    $this->assertEquals(Type::$VAR, $this->fixture('noReturnFixture')->returns());
  }

  #[Test]
  public function short_form() {
    $this->assertEquals(Type::$VOID, $this->fixture('shortFormFixture')->returns());
  }

  #[Test]
  public function long_form() {
    $this->assertEquals(Primitive::$STRING, $this->fixture('longFormFixture')->returns());
  }

  #[Test]
  public function self_supported() {
    $this->assertEquals(typeof($this), $this->fixture('selfFixture')->returns());
  }

  #[Test]
  public function parent_supported() {
    $this->assertEquals(typeof($this)->getParentclass(), $this->fixture('parentFixture')->returns());
  }

  #[Test]
  public function iterable_supported() {
    $this->assertEquals(property_exists(Type::class, 'ITERABLE') ? Type::$ITERABLE : Type::$VAR, $this->fixture('iterableFixture')->returns());
  }

  #[Test]
  public function object_supported() {
    $this->assertEquals(property_exists(Type::class, 'OBJECT') ? Type::$OBJECT : Type::$VAR, $this->fixture('objectFixture')->returns());
  }

  #[Test]
  public function returns() {
    $this->assertEquals(Type::forName('lang.mirrors.Method'), $this->fixture('fixture')->returns());
  }

  #[Test]
  public function returns_is_resolved() {
    $this->assertEquals(Type::forName('lang.mirrors.Method'), $this->fixture('resolved')->returns());
  }
}