<?php namespace lang\mirrors\unittest;

use lang\mirrors\unittest\fixture\{FixtureAbstract, FixtureEnum, FixtureFinal, FixtureInterface, FixtureTrait};
use lang\mirrors\{FromCode, FromIncomplete, FromReflection, Modifiers, Package, Sources, TypeMirror};
use lang\{ElementNotFoundException, IllegalArgumentException, XPClass};
use unittest\{Test, TestCase, Values};

/**
 * Tests TypeMirror
 */
class TypeMirrorTest extends TestCase {

  /** @return iterable */
  private function sources() {
    yield [new FromReflection(new \ReflectionClass(self::class))];
    yield [new FromCode('lang.mirrors.unittest.TypeMirrorTest')];
    yield [new FromIncomplete('does\not\exist')];
  }

  /** @return var[][] */
  private function args() {
    return [
      [self::class, 'type literal'],
      ['lang.mirrors.unittest.TypeMirrorTest', 'fully qualified'],
      [new XPClass(self::class), 'type system'],
      [new \ReflectionClass(self::class), 'php reflection']
    ];
  }

  #[Test, Values('args')]
  public function can_create($arg) {
    new TypeMirror($arg);
  }

  #[Test, Values('args')]
  public function can_create_with_default_source($arg) {
    new TypeMirror($arg, Sources::$DEFAULT);
  }

  #[Test, Values('args')]
  public function can_create_with_reflection_source($arg) {
    new TypeMirror($arg, Sources::$REFLECTION);
  }

  #[Test, Values('args')]
  public function can_create_with_code_source($arg) {
    new TypeMirror($arg, Sources::$CODE);
  }

  #[Test, Values('sources')]
  public function can_create_from_source($source) {
    new TypeMirror($source);
  }

  #[Test]
  public function this_class_is_present() {
    $this->assertTrue((new TypeMirror(self::class))->present());
  }

  #[Test]
  public function non_existant_class_not_present() {
    $this->assertFalse((new TypeMirror('does.not.exist'))->present());
  }

  #[Test]
  public function name() {
    $this->assertEquals('lang.mirrors.unittest.TypeMirrorTest', (new TypeMirror(self::class))->name());
  }

  #[Test]
  public function declaration() {
    $this->assertEquals('TypeMirrorTest', (new TypeMirror(self::class))->declaration());
  }

  #[Test]
  public function type() {
    $this->assertEquals(typeof($this), (new TypeMirror(self::class))->type());
  }

  #[Test]
  public function comment() {
    $this->assertEquals('Tests TypeMirror', (new TypeMirror(self::class))->comment());
  }

  #[Test]
  public function package() {
    $this->assertEquals(new Package('lang.mirrors.unittest'), (new TypeMirror(self::class))->package());
  }

  #[Test]
  public function this_class_has_parent() {
    $this->assertEquals('unittest.TestCase', (new TypeMirror(self::class))->parent()->name());
  }

  #[Test]
  public function value_interface_does_not_have_a_parent() {
    $this->assertNull((new TypeMirror('lang.Value'))->parent());
  }

  #[Test]
  public function base_does_not_have_a_parent() {
    $this->assertNull((new TypeMirror('lang.mirrors.unittest.fixture.FixtureBase'))->parent());
  }

  #[Test]
  public function isClass() {
    $this->assertTrue((new TypeMirror(self::class))->kind()->isClass());
  }

  #[Test]
  public function isInterface() {
    $this->assertTrue((new TypeMirror(FixtureInterface::class))->kind()->isInterface());
  }

  #[Test]
  public function isTrait() {
    $this->assertTrue((new TypeMirror(FixtureTrait::class))->kind()->isTrait());
  }

  #[Test]
  public function isEnum() {
    $this->assertTrue((new TypeMirror(FixtureEnum::class))->kind()->isEnum());
  }

  #[Test]
  public function this_class_is_public() {
    $this->assertEquals(new Modifiers('public'), (new TypeMirror(self::class))->modifiers());
  }

  #[Test]
  public function abstract_class_is_abstract() {
    $this->assertEquals(new Modifiers('public abstract'), (new TypeMirror(FixtureAbstract::class))->modifiers());
  }

  #[Test]
  public function final_class_is_final() {
    $this->assertEquals(new Modifiers('public final'), (new TypeMirror(FixtureFinal::class))->modifiers());
  }

  #[Test]
  public function interface_class_is_public() {
    $this->assertEquals(new Modifiers('public'), (new TypeMirror(FixtureInterface::class))->modifiers());
  }

  #[Test]
  public function trait_class_is_abstract_public() {
    $this->assertEquals(new Modifiers('public abstract'), (new TypeMirror(FixtureTrait::class))->modifiers());
  }

  #[Test, Values(["unittest.TestCase", TestCase::class])]
  public function this_class_is_subtype_of_TestCase($type) {
    $this->assertTrue((new TypeMirror(self::class))->isSubtypeOf($type));
  }

  #[Test]
  public function this_class_is_subtype_of_TestCase_typemirror() {
    $this->assertTrue((new TypeMirror(self::class))->isSubtypeOf(new TypeMirror(TestCase::class)));
  }

  #[Test, Values([FixtureInterface::class, FixtureTrait::class, FixtureEnum::class])]
  public function this_class_is_not_subtype_of($type) {
    $this->assertFalse((new TypeMirror(self::class))->isSubtypeOf($type));
  }

  #[Test]
  public function this_class_has_constructor() {
    $this->assertTrue((new TypeMirror(self::class))->constructor()->present());
  }

  #[Test]
  public function object_class_does_not_have_constructor() {
    $this->assertFalse((new TypeMirror('lang.Object'))->constructor()->present());
  }
}