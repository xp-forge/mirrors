<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Sources;
use lang\mirrors\Package;
use lang\mirrors\Modifiers;
use lang\mirrors\FromIncomplete;
use lang\mirrors\FromReflection;
use lang\mirrors\FromCode;
use lang\ElementNotFoundException;
use lang\IllegalArgumentException;
use unittest\TestCase;
use lang\mirrors\unittest\fixture\FixtureTrait;
use lang\mirrors\unittest\fixture\FixtureInterface;
use lang\mirrors\unittest\fixture\FixtureEnum;
use lang\mirrors\unittest\fixture\FixtureAbstract;
use lang\mirrors\unittest\fixture\FixtureFinal;
use lang\XPClass;

/**
 * Tests TypeMirror
 */
class TypeMirrorTest extends TestCase {

  /** @return var[][] */
  private function args() {
    return [
      [self::class, 'type literal'],
      ['lang.mirrors.unittest.TypeMirrorTest', 'fully qualified'],
      [new XPClass(self::class), 'type system'],
      [new \ReflectionClass(self::class), 'php reflection']
    ];
  }

  #[@test, @values('args')]
  public function can_create($arg) {
    new TypeMirror($arg);
  }

  #[@test, @values('args')]
  public function can_create_with_default_source($arg) {
    new TypeMirror($arg, Sources::$DEFAULT);
  }

  #[@test, @values('args')]
  public function can_create_with_reflection_source($arg) {
    new TypeMirror($arg, Sources::$REFLECTION);
  }

  #[@test, @values('args')]
  public function can_create_with_code_source($arg) {
    new TypeMirror($arg, Sources::$CODE);
  }

  #[@test, @values([
  #  [new FromReflection(new \ReflectionClass(self::class))],
  #  [new FromCode('lang.mirrors.unittest.TypeMirrorTest')],
  #  [new FromIncomplete('does\not\exist')]
  #])]
  public function can_create_from_source($source) {
    new TypeMirror($source);
  }

  #[@test]
  public function this_class_is_present() {
    $this->assertTrue((new TypeMirror(self::class))->present());
  }

  #[@test]
  public function non_existant_class_not_present() {
    $this->assertFalse((new TypeMirror('does.not.exist'))->present());
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