<?php namespace lang\mirrors\unittest;

use lang\mirrors\unittest\fixture\Identity;
use lang\mirrors\{TargetInvocationException, TypeMirror};
use lang\{Error, IllegalArgumentException, Value};
use unittest\{Expect, Test};

class MethodInvocationTest extends AbstractMethodTest {

  private function noReturnFixture() { }

  private function returnsTestFixture() { return 'Test'; }

  private function returnsArgsFixture(... $args) { return $args; }

  private function throwsExceptionFixture() { throw new IllegalArgumentException('Test'); }

  private function raisesErrorFixture() { $value= null; $value->invoke(); }

  private function typeHintedFixture(Value $arg) { }

  private static function staticMethodFixture() { return 'Test'; }

  #[Test]
  public function no_return() {
    $this->assertNull($this->fixture('noReturnFixture')->invoke($this));
  }

  #[Test]
  public function returns_test() {
    $this->assertEquals('Test', $this->fixture('returnsTestFixture')->invoke($this));
  }

  #[Test]
  public function returns_args() {
    $args= ['Test', 1, $this];
    $this->assertEquals($args, $this->fixture('returnsArgsFixture')->invoke($this, $args));
  }

  #[Test]
  public function invoke_static_method() {
    $this->assertEquals('Test', $this->fixture('staticMethodFixture')->invoke(null));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function raises_exception_with_incompatible_instance() {
    $this->fixture('noReturnFixture')->invoke(new Identity('Test'));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function raises_exception_with_null_instance() {
    $this->fixture('noReturnFixture')->invoke(null);
  }

  #[Test, Expect(TargetInvocationException::class)]
  public function wraps_exceptions_raised_from_invoked_method() {
    $this->fixture('throwsExceptionFixture')->invoke($this);
  }

  #[Test, Expect(TargetInvocationException::class)]
  public function wraps_errors_raised_from_invoked_method() {
    $this->fixture('raisesErrorFixture')->invoke($this);
  }

  #[Test, Expect(TargetInvocationException::class)]
  public function wraps_exceptions_raised_from_argument_mismatch() {
    $this->fixture('typeHintedFixture')->invoke($this, ['not.an.obhject']);
  }

  #[Test]
  public function sets_cause_for_exceptions_thrown_from_invoked_method() {
    try {
      $this->fixture('throwsExceptionFixture')->invoke($this, []);
      $this->fail('No exception raised', null, TargetInvocationException::class);
    } catch (TargetInvocationException $expected) {
      $this->assertInstanceOf(IllegalArgumentException::class, $expected->getCause());
    }
  }

  #[Test]
  public function sets_cause_for_errors_raised_from_invoked_method() {
    try {
      $this->fixture('raisesErrorFixture')->invoke($this, []);
      $this->fail('No exception raised', null, TargetInvocationException::class);
    } catch (TargetInvocationException $expected) {
      $this->assertInstanceOf(Error::class, $expected->getCause());
    }
  }
}