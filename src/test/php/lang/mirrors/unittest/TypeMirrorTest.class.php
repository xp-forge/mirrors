<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Package;
use lang\ElementNotFoundException;
use lang\IllegalArgumentException;

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
  public function this_class_has_constructor() {
    $this->assertTrue((new TypeMirror(self::class))->constructor()->present());
  }

  #[@test]
  public function object_class_does_not_have_constructor() {
    $this->assertFalse((new TypeMirror('lang.Object'))->constructor()->present());
  }
}