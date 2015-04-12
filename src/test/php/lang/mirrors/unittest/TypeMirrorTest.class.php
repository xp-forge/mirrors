<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\ElementNotFoundException;
use lang\IllegalArgumentException;
use lang\mirrors\TargetInvocationException;

/**
 * Tests TypeMirror
 */
class TypeMirrorTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new TypeMirror(self::class);
  }

  #[@test]
  public function name() {
    $this->assertEquals('lang.mirrors.unittest.TypeMirrorTest', (new TypeMirror(self::class))->name());
  }

  #[@test]
  public function comment() {
    $this->assertEquals('Tests TypeMirror', (new TypeMirror(self::class))->comment());
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
  public function this_class_has_constructor() {
    $this->assertInstanceOf('lang.mirrors.Constructor', (new TypeMirror(self::class))->constructor());
  }

  #[@test]
  public function object_class_does_not_have_constructor() {
    $this->assertNull((new TypeMirror('lang.Object'))->constructor());
  }

  #[@test]
  public function creating_new_object_instances() {
    $this->assertInstanceOf(
      'lang.Object',
      (new TypeMirror('lang.Object'))->newInstance()
    );
  }

  #[@test]
  public function creating_instances_invokes_constructor() {
    $fixture= newinstance('lang.Object', [], [
      'passed'      => null,
      '__construct' => function(... $args) { $this->passed= $args; }
    ]);
    $this->assertEquals([1, 2, 3], (new TypeMirror(typeof($fixture)))->newInstance(1, 2, 3)->passed);
  }

  #[@test, @expect(TargetInvocationException::class)]
  public function creating_instances_wraps_exceptions() {
    $fixture= newinstance('lang.Object', ['Test'], [
      '__construct' => function($arg) { if (null === $arg) throw new IllegalArgumentException('Test'); }
    ]);
    (new TypeMirror(typeof($fixture)))->newInstance();
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_interfaces() {
    (new TypeMirror(FixtureInterface::class))->newInstance();
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_traits() {
    (new TypeMirror(FixtureTrait::class))->newInstance();
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_create_instances_from_abstract_classes() {
    (new TypeMirror(FixtureAbstract::class))->newInstance();
  }
}