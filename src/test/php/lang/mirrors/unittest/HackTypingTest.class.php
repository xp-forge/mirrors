<?php namespace lang\mirrors\unittest;

use lang\Object;
use lang\Primitive;
use lang\Type;
use lang\ArrayType;
use lang\MapType;
use lang\XPClass;
use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\FixtureHackTypedClass;

#[@action(new OnlyOnHHVM())]
class HackTypingTest extends \unittest\TestCase {
  private $fixture;

  public function setUp() {
    $this->fixture= new TypeMirror(FixtureHackTypedClass::class);
  }

  /** @return var[][] */
  private function targets($name) {
    $mirror= new TypeMirror(FixtureHackTypedClass::class);
    return [
      [$mirror->fields()->named($name)->type()],
      [$mirror->methods()->named($name)->returns()],
      [$mirror->methods()->named('parameters')->parameters()->named($name)->type()]
    ];
  }

  #[@test, @values(source= 'targets', args= ['typed'])]
  public function typed($target) {
    $this->assertEquals(Primitive::$INT, $target);
  }

  #[@test, @values(source= 'targets', args= ['parentTyped'])]
  public function parent_typed($target) {
    $this->assertEquals(new XPClass(Object::class), $target);
  }

  #[@test, @values(source= 'targets', args= ['arrayTyped'])]
  public function array_typed($target) {
    $this->assertEquals(new ArrayType(Primitive::$STRING), $target);
  }

  #[@test, @values(source= 'targets', args= ['mapTyped'])]
  public function map_typed($target) {
    $this->assertEquals(new MapType(new XPClass(FixtureHackTypedClass::class)), $target);
  }

  #[@test, @values(source= 'targets', args= ['unTyped'])]
  public function untyped($target) {
    $this->assertEquals(Type::$VAR, $target);
  }
}