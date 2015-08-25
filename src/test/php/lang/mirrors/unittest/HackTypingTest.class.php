<?php namespace lang\mirrors\unittest;

use lang\Object;
use lang\Primitive;
use lang\Type;
use lang\ArrayType;
use lang\MapType;
use lang\FunctionType;
use lang\XPClass;
use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\FixtureHackTypedClass;
use unittest\actions\RuntimeVersion;

#[@action(new RuntimeVersion('>=5.5.0'))]
abstract class HackTypingTest extends \unittest\TestCase {

  /** @return lang.mirrors.Sources */
  protected abstract function source();

  /** @return var[][] */
  private function targets($name) {
    $mirror= new TypeMirror('lang.mirrors.unittext.fixture.FixtureHackTypedClass', $this->source());
    return [
      [$mirror->fields()->named($name)->type(), 'field'],
      [$mirror->methods()->named($name)->returns(), 'method'],
      [$mirror->methods()->named('parameters')->parameters()->named($name)->type(), 'param']
    ];
  }

  #[@test, @values(source= 'targets', args= ['typed'])]
  public function typed($target) {
    $this->assertEquals(Primitive::$INT, $target);
  }

  #[@test, @values(source= 'targets', args= ['parentTyped'])]
  public function parent_typed($target) {
    $this->assertEquals(new XPClass('lang.Object'), $target);
  }

  #[@test, @values(source= 'targets', args= ['thisTyped'])]
  public function this_typed($target) {
    $this->assertEquals(new XPClass('lang.mirrors.unittext.fixture.FixtureHackTypedClass'), $target);
  }

  #[@test, @values(source= 'targets', args= ['arrayTyped'])]
  public function array_typed($target) {
    $this->assertEquals(new ArrayType(Primitive::$STRING), $target);
  }

  #[@test, @values(source= 'targets', args= ['mapTyped'])]
  public function map_typed($target) {
    $this->assertEquals(new MapType(new XPClass('lang.mirrors.unittext.fixture.FixtureHackTypedClass')), $target);
  }

  #[@test, @values(source= 'targets', args= ['unTypedArrayTyped'])]
  public function untyped_array_typed($target) {
    $this->assertEquals(Type::$ARRAY, $target);
  }

  #[@test, @values(source= 'targets', args= ['funcTyped'])]
  public function func_typed($target) {
    $this->assertEquals(new FunctionType([Primitive::$STRING, Primitive::$INT], Type::$VOID), $target);
  }

  #[@test, @values(source= 'targets', args= ['nullableTyped'])]
  public function nullable_typed($target) {
    $this->assertEquals(Primitive::$INT, $target);
  }

  #[@test, @values(source= 'targets', args= ['mixedTyped'])]
  public function mixed_typed($target) {
    $this->assertEquals(Type::$VAR, $target);
  }

  #[@test, @values(source= 'targets', args= ['unTyped'])]
  public function untyped($target) {
    $this->assertEquals(Type::$VAR, $target);
  }
}