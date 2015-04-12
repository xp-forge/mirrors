<?php namespace lang\mirrors\unittest;

use lang\mirrors\Parameter;
use lang\mirrors\Method;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;
use lang\Type;
use lang\XPClass;

class ParameterTest extends \unittest\TestCase {

  private function noParam() { }

  private function oneParam($arg) { }

  private function oneOptionalParam($arg= null) { }

  private function oneVariadicParam(...$arg) { }

  private function oneTypeHintedParam(Type $arg) { }

  private function oneSelfTypeHintedParam(self $arg) { }

  private function oneArrayTypeHintedParam(array $arg) { }

  private function oneCallableTypeHintedParam(callable $arg) { }

  /** @param lang.Type */
  private function oneDocumentedTypeParam($arg) { }


  /**
   * Fixture
   *
   * @param var $a
   * @param lang.Type $b
   */
  private function twoDocumentedTypeParams($a, $b) { }

  /**
   * Creates a new parameter
   *
   * @param  string $method Reference to one of the above fixture methods
   * @return lang.mirrors.Parameter
   */
  private function newFixture($method, $num) {
    return new Parameter(new Method(new TypeMirror(self::class), $method), $num);
  }

  #[@test]
  public function can_create_from_method_and_offset() {
    new Parameter(new Method(new TypeMirror(self::class), 'oneParam'), 0);
  }

  #[@test]
  public function can_create_from_method_and_parameter() {
    new Parameter(
      new Method(new TypeMirror(self::class), 'oneParam'),
      new \ReflectionParameter([__CLASS__, 'oneParam'], 0)
    );
  }

  #[@test, @expect(IllegalArgumentException::class), @values([
  #  ['noParam', 0], ['noParam', 1], ['noParam', -1],
  #  ['oneParam', 1], ['oneParam', -1]
  #])]
  public function raises_exception_when_parameter_does_not_exist($method, $offset) {
    $this->newFixture($method, $offset);
  }

  #[@test]
  public function name() {
    $this->assertEquals('arg', $this->newFixture('oneParam', 0)->name());
  }

  #[@test]
  public function position() {
    $this->assertEquals(0, $this->newFixture('oneParam', 0)->position());
  }

  #[@test, @values([['oneOptionalParam', true], ['oneParam', false]])]
  public function isOptional($method, $result) {
    $this->assertEquals($result, $this->newFixture($method, 0)->isOptional());
  }

  #[@test, @values([['oneVariadicParam', true], ['oneParam', false]])]
  public function isVariadic($method, $result) {
    $this->assertEquals($result, $this->newFixture($method, 0)->isVariadic());
  }

  #[@test]
  public function var_is_default_for_no_type_hint() {
    $this->assertEquals(Type::$VAR, $this->newFixture('oneParam', 0)->type());
  }

  #[@test]
  public function type_hint() {
    $this->assertEquals(new XPClass(Type::class), $this->newFixture('oneTypeHintedParam', 0)->type());
  }

  #[@test]
  public function self_type_hint() {
    $this->assertEquals(typeof($this), $this->newFixture('oneSelfTypeHintedParam', 0)->type());
  }

  #[@test]
  public function array_type_hint() {
    $this->assertEquals(Type::$ARRAY, $this->newFixture('oneArrayTypeHintedParam', 0)->type());
  }

  #[@test]
  public function callable_type_hint() {
    $this->assertEquals(Type::$CALLABLE, $this->newFixture('oneCallableTypeHintedParam', 0)->type());
  }

  #[@test]
  public function documented_type_hint_using_short_form() {
    $this->assertEquals(new XPClass(Type::class), $this->newFixture('oneDocumentedTypeParam', 0)->type());
  }

  #[@test]
  public function documented_type_hint_using_long_form() {
    $this->assertEquals(new XPClass(Type::class), $this->newFixture('twoDocumentedTypeParams', 1)->type());
  }
}