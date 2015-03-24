<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\TargetInvocationException;
use lang\Object;
use lang\IllegalArgumentException;

class MethodInvocationTest extends AbstractMethodTest {

  private function noReturnFixture() { }

  private function returnsTestFixture() { return 'Test'; }

  private function returnsArgsFixture() { return func_get_args(); }

  private function throwsExceptionFixture() { throw new IllegalArgumentException('Test'); }

  private function typeHintedFixture(Object $arg) { }

  private static function staticMethodFixture() { return 'Test'; }

  #[@test]
  public function no_return() {
    $this->assertNull($this->fixture('noReturnFixture')->invoke($this));
  }

  #[@test]
  public function returns_test() {
    $this->assertEquals('Test', $this->fixture('returnsTestFixture')->invoke($this));
  }

  #[@test]
  public function returns_args() {
    $args= ['Test', 1, $this];
    $this->assertEquals($args, $this->fixture('returnsArgsFixture')->invoke($this, $args));
  }

  #[@test]
  public function invoke_static_method() {
    $this->assertEquals('Test', $this->fixture('staticMethodFixture')->invoke(null));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function raises_exception_with_incompatible_instance() {
    $this->fixture('noReturnFixture')->invoke(new Object());
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function raises_exception_with_null_instance() {
    $this->fixture('noReturnFixture')->invoke(null);
  }

  #[@test, @expect(TargetInvocationException::class)]
  public function wraps_exceptions_raised_from_invoked_method() {
    $this->fixture('throwsExceptionFixture')->invoke($this);
  }

  #[@test, @expect(TargetInvocationException::class)]
  public function wraps_exceptions_raised_from_argument_mismatch() {
    $this->fixture('typeHintedFixture')->invoke($this, ['not.an.obhject']);
  }
}