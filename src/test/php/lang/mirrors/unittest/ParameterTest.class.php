<?php namespace lang\mirrors\unittest;

use lang\mirrors\Parameter;
use lang\mirrors\Method;
use lang\mirrors\Annotation;
use lang\mirrors\TypeMirror;
use lang\IllegalArgumentException;
use lang\IllegalStateException;
use lang\Type;
use lang\XPClass;
use lang\ClassLoader;
use lang\mirrors\unittest\fixture\FixtureParams;

class ParameterTest extends \unittest\TestCase {
  private static $type= null;

  /**
   * Creates a new parameter
   *
   * @param  string $method Reference to one of the above fixture methods
   * @return lang.mirrors.Parameter
   */
  private function newFixture($method, $num) {
    if (null === self::$type) {
      if (NotOnHHVM::runtime()) {
        self::$type= new TypeMirror(FixtureParams::class);
      } else {
        $class= ClassLoader::defineClass('FixtureParamsWithTypedVariadic', FixtureParams::class, [], '{
          private function oneVariadicTypedParam(\lang\Type... $arg) { }
        }');
        self::$type= new TypeMirror($class);
      }
    }

    return new Parameter(new Method(self::$type, $method), $num);
  }

  #[@test]
  public function can_create_from_method_and_offset() {
    new Parameter(new Method(new TypeMirror(FixtureParams::class), 'oneParam'), 0);
  }

  #[@test]
  public function can_create_from_method_and_parameter() {
    new Parameter(
      new Method(new TypeMirror(FixtureParams::class), 'oneParam'),
      new \ReflectionParameter([FixtureParams::class, 'oneParam'], 0)
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

  #[@test, @action(new NotOnHHVM())]
  public function isVariadicWithType() {
    $this->assertTrue($this->newFixture('oneVariadicTypedParam', 0)->isVariadic());
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
    $this->assertEquals(new XPClass(FixtureParams::class), $this->newFixture('oneSelfTypeHintedParam', 0)->type());
  }

  #[@test, @action(new NotOnHHVM())]
  public function variadic_type_hint() {
    $this->assertEquals(new XPClass(Type::class), $this->newFixture('oneVariadicTypedParam', 0)->type());
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
    $this->assertEquals(new XPClass(Type::class), $this->newFixture('twoDocumentedTypeParams', 0)->type());
  }

  #[@test, @expect(IllegalStateException::class), @values([
  #  ['oneParam', 0],
  #  ['oneVariadicParam', 0]
  #])]
  public function cannot_get_default_value_for_non_optional($method, $offset) {
    $this->newFixture($method, $offset)->defaultValue();
  }

  #[@test]
  public function null_default_value_for_optional() {
    $this->assertEquals(null, $this->newFixture('oneOptionalParam', 0)->defaultValue());
  }

  #[@test]
  public function constant_default_value_for_optional() {
    $this->assertEquals(FixtureParams::CONSTANT, $this->newFixture('oneConstantOptionalParam', 0)->defaultValue());
  }

  #[@test]
  public function array_default_value_for_optional() {
    $this->assertEquals([1, 2, 3], $this->newFixture('oneArrayOptionalParam', 0)->defaultValue());
  }

  #[@test]
  public function no_annotations() {
    $this->assertFalse($this->newFixture('oneParam', 0)->annotations()->present());
  }

  #[@test]
  public function annotated_parameter() {
    $fixture= $this->newFixture('oneAnnotatedParam', 0);
    $this->assertEquals(
      [new Annotation(new TypeMirror(self::class), 'test', null)],
      iterator_to_array($fixture->annotations())
    );
  }

  #[@test, @values([
  #  ['oneParam', false],
  #  ['oneVariadicParam', false],
  #  ['oneTypeHintedParam', true],
  #  ['oneSelfTypeHintedParam', true],
  #  ['oneArrayTypeHintedParam', true],
  #  ['oneCallableTypeHintedParam', true]
  #])]
  public function isVerified($method, $expect) {
    $this->assertEquals($expect, $this->newFixture($method, 0)->isVerified());
  }

  #[@test, @action(new NotOnHHVM())]
  public function variadicWithTypeisVerified() {
    $this->assertTrue($this->newFixture('oneVariadicTypedParam', 0)->isVerified());
  }
}