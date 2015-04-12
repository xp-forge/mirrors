<?php namespace lang\mirrors\unittest;

use lang\mirrors\Method;
use lang\mirrors\Modifiers;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;

class MethodTest extends AbstractMethodTest {

  #[@test]
  public function can_create_from_method_name() {
    new Method($this->type, __FUNCTION__);
  }

  #[@test]
  public function can_create_from_reflection_method() {
    new Method($this->type, new \ReflectionMethod(self::class, __FUNCTION__));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function constructor_raises_exception_if_method_does_not_exist() {
    new Method($this->type, 'not.a.method');
  }

  #[@test]
  public function name() {
    $this->assertEquals(__FUNCTION__, $this->fixture(__FUNCTION__)->name());
  }

  #[@test]
  public function modifiers() {
    $this->assertEquals(new Modifiers(MODIFIER_PROTECTED), $this->fixture('fixture')->modifiers());
  }

  #[@test]
  public function this_methods_declaring_type() {
    $this->assertEquals($this->type, $this->fixture(__FUNCTION__)->declaredIn());
  }

  #[@test]
  public function fixture_methods_declaring_type() {
    $this->assertEquals($this->type->parent(), $this->fixture('fixture')->declaredIn());
  }
}