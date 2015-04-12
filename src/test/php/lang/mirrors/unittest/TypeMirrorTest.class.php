<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Package;
use lang\mirrors\Modifiers;
use lang\ElementNotFoundException;
use lang\IllegalArgumentException;
use unittest\TestCase;

/**
 * Tests TypeMirror
 */
class TypeMirrorTest extends TestCase {

  #[@test]
  public function can_create() {
    new TypeMirror(self::class);
  }

  #[@test]
  public function name() {
    $this->assertEquals('lang.mirrors.unittest.TypeMirrorTest', (new TypeMirror(self::class))->name());
  }

  #[@test]
  public function declaration() {
    $this->assertEquals('TypeMirrorTest', (new TypeMirror(self::class))->declaration());
  }

  #[@test]
  public function comment() {
    $this->assertEquals('Tests TypeMirror', (new TypeMirror(self::class))->comment());
  }

  #[@test]
  public function package() {
    $this->assertEquals(new Package('lang.mirrors.unittest'), (new TypeMirror(self::class))->package());
  }

  #[@test]
  public function this_class_has_parent() {
    $this->assertEquals('unittest.TestCase', (new TypeMirror(self::class))->parent()->name());
  }

  #[@test]
  public function object_class_does_not_have_a_parent() {
    $this->assertNull((new TypeMirror('lang.Object'))->parent());
  }

  #[@test]
  public function isClass() {
    $this->assertTrue((new TypeMirror(self::class))->kind()->isClass());
  }

  #[@test]
  public function isInterface() {
    $this->assertTrue((new TypeMirror(FixtureInterface::class))->kind()->isInterface());
  }

  #[@test]
  public function isTrait() {
    $this->assertTrue((new TypeMirror(FixtureTrait::class))->kind()->isTrait());
  }

  #[@test]
  public function isEnum() {
    $this->assertTrue((new TypeMirror(FixtureEnum::class))->kind()->isEnum());
  }

  #[@test]
  public function this_class_is_public() {
    $this->assertEquals(new Modifiers('public'), (new TypeMirror(self::class))->modifiers());
  }

  #[@test]
  public function abstract_class_is_abstract() {
    $this->assertEquals(new Modifiers('public abstract'), (new TypeMirror(FixtureAbstract::class))->modifiers());
  }

  #[@test]
  public function final_class_is_final() {
    $this->assertEquals(new Modifiers('public final'), (new TypeMirror(FixtureFinal::class))->modifiers());
  }

  #[@test]
  public function interface_class_is_public() {
    $this->assertEquals(new Modifiers('public'), (new TypeMirror(FixtureInterface::class))->modifiers());
  }

  #[@test]
  public function trait_class_is_abstract_public() {
    $this->assertEquals(new Modifiers('public abstract'), (new TypeMirror(FixtureTrait::class))->modifiers());
  }

  #[@test, @values([
  #  'unittest.TestCase',
  #  TestCase::class,
  #  new TypeMirror(TestCase::class)
  #])]
  public function this_class_is_subtype_of_TestCase($type) {
    $this->assertTrue((new TypeMirror(self::class))->isSubtypeOf($type));
  }

  #[@test, @values([
  #  FixtureInterface::class,
  #  FixtureTrait::class,
  #  FixtureEnum::class
  #])]
  public function this_class_is_not_subtype_of($type) {
    $this->assertFalse((new TypeMirror(self::class))->isSubtypeOf($type));
  }

  #[@test]
  public function this_class_has_constructor() {
    $this->assertTrue((new TypeMirror(self::class))->constructor()->present());
  }

  #[@test]
  public function object_class_does_not_have_constructor() {
    $this->assertFalse((new TypeMirror('lang.Object'))->constructor()->present());
  }
}