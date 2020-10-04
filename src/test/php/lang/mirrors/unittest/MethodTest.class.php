<?php namespace lang\mirrors\unittest;

use lang\mirrors\{Method, Modifiers, TypeMirror};
use lang\{ElementNotFoundException, IllegalAccessException, IllegalArgumentException};
use unittest\{Expect, Test, Values};

class MethodTest extends AbstractMethodTest {

  /** @return iterable */
  private function exceptions() {
    yield [new TypeMirror(IllegalArgumentException::class), true];
    yield [IllegalArgumentException::class, true];
    yield ['lang.IllegalArgumentException', true];
    yield [new TypeMirror(IllegalAccessException::class), false];
    yield [IllegalAccessException::class, false];
    yield ['lang.IllegalAccessException', false];
  }

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

  #[Test]
  public function can_create_from_method_name() {
    new Method($this->type, __FUNCTION__);
  }

  #[Test]
  public function can_create_from_reflection_method() {
    new Method($this->type, new \ReflectionMethod(self::class, __FUNCTION__));
  }

  #[Test, Expect(ElementNotFoundException::class)]
  public function constructor_raises_exception_if_method_does_not_exist() {
    new Method($this->type, 'not.a.method');
  }

  #[Test]
  public function name() {
    $this->assertEquals(__FUNCTION__, $this->fixture(__FUNCTION__)->name());
  }

  #[Test]
  public function modifiers() {
    $this->assertEquals(new Modifiers('protected'), $this->fixture('fixture')->modifiers());
  }

  #[Test]
  public function this_methods_declaring_type() {
    $this->assertEquals($this->type, $this->fixture(__FUNCTION__)->declaredIn());
  }

  #[Test]
  public function fixture_methods_declaring_type() {
    $this->assertEquals($this->type->parent(), $this->fixture('fixture')->declaredIn());
  }

  #[Test]
  public function no_thrown_exceptions() {
    $this->assertEquals(
      [],
      iterator_to_array($this->fixture('fixture')->throws())
    );
  }

  #[Test]
  public function one_thrown_exception() {
    $this->assertEquals(
      [new TypeMirror(IllegalArgumentException::class)],
      iterator_to_array($this->fixture('raisesOne')->throws())
    );
  }

  #[Test]
  public function more_than_one_thrown_exception() {
    $this->assertEquals(
      [new TypeMirror(IllegalArgumentException::class), new TypeMirror(IllegalAccessException::class)],
      iterator_to_array($this->fixture('raisesMore')->throws())
    );
  }

  #[Test, Values('exceptions')]
  public function throws_contains($class, $expected) {
    $this->assertEquals($expected, $this->fixture('raisesOne')->throws()->contains($class));
  }

  #[Test]
  public function string_representation() {
    $this->assertEquals(
      'lang.mirrors.Method(protected lang.mirrors.Method fixture(string $name))',
      $this->fixture('fixture')->toString()
    );
  }
}