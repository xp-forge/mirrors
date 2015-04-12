<?php namespace lang\mirrors\unittest;

use lang\mirrors\Method;
use lang\mirrors\Modifiers;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;
use lang\IllegalAccessException;

class MethodTest extends AbstractMethodTest {

  /**
   * Fixture
   *
   * @throws lang.IllegalArgumentException
   */
  private function raisesOne() { }

  /**
   * Fixture
   *
   * @throws lang.IllegalArgumentException
   * @throws lang.IllegalAccessException
   */
  private function raisesMore() { }

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
    $this->assertEquals(new Modifiers('protected'), $this->fixture('fixture')->modifiers());
  }

  #[@test]
  public function this_methods_declaring_type() {
    $this->assertEquals($this->type, $this->fixture(__FUNCTION__)->declaredIn());
  }

  #[@test]
  public function fixture_methods_declaring_type() {
    $this->assertEquals($this->type->parent(), $this->fixture('fixture')->declaredIn());
  }

  #[@test]
  public function no_thrown_exceptions() {
    $this->assertEquals(
      [],
      iterator_to_array($this->fixture('fixture')->throws())
    );
  }

  #[@test]
  public function one_thrown_exception() {
    $this->assertEquals(
      [new TypeMirror(IllegalArgumentException::class)],
      iterator_to_array($this->fixture('raisesOne')->throws())
    );
  }

  #[@test]
  public function more_than_one_thrown_exception() {
    $this->assertEquals(
      [new TypeMirror(IllegalArgumentException::class), new TypeMirror(IllegalAccessException::class)],
      iterator_to_array($this->fixture('raisesMore')->throws())
    );
  }

  #[@test, @values([
  #  [new TypeMirror(IllegalArgumentException::class), true],
  #  [IllegalArgumentException::class, true],
  #  ['lang.IllegalArgumentException', true],
  #  [new TypeMirror(IllegalAccessException::class), false],
  #  [IllegalAccessException::class, false],
  #  ['lang.IllegalAccessException', false]
  #])]
  public function throws_contains($class, $expected) {
    $this->assertEquals($expected, $this->fixture('raisesOne')->throws()->contains($class));
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Method(protected lang.mirrors.Method fixture(string $name))',
      $this->fixture('fixture')->toString()
    );
  }
}